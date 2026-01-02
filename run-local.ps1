# Isaac Progress Tracker - Local Development Script
# Usage: .\run-local.ps1 [command]
# Commands: start, stop, restart, logs, build, shell

param(
    [Parameter(Position=0)]
    [ValidateSet("start", "stop", "restart", "logs", "build", "shell")]
    [string]$Command = "start"
)

$ContainerName = "isaac-tracker-local"
$ImageName = "isaac-tracker:local"
$Port = 8080

# Load Steam API key from .env if it exists
$SteamApiKey = ""
if (Test-Path ".env") {
    Get-Content ".env" | ForEach-Object {
        if ($_ -match "^STEAM_API_KEY=(.+)$") {
            $SteamApiKey = $matches[1]
        }
    }
}

if ([string]::IsNullOrEmpty($SteamApiKey) -or $SteamApiKey -eq "your_steam_api_key_here") {
    if (-not (Test-Path ".env")) {
        Write-Host "Creating .env file template..." -ForegroundColor Yellow
        @"
# Steam API Key - Get yours at https://steamcommunity.com/dev/apikey
STEAM_API_KEY=your_steam_api_key_here
"@ | Out-File -FilePath ".env" -Encoding UTF8
    }
    Write-Host "Warning: No Steam API key configured. Edit .env file to enable Steam sync." -ForegroundColor Yellow
    Write-Host ""
}

function Stop-Container {
    $existing = podman ps -aq --filter "name=$ContainerName" 2>$null
    if ($existing) {
        Write-Host "Stopping existing container..." -ForegroundColor Yellow
        podman stop $ContainerName 2>$null | Out-Null
        podman rm $ContainerName 2>$null | Out-Null
    }
}

function Install-ComposerDeps {
    # Check if vendor directory exists in src
    if (-not (Test-Path "src/vendor")) {
        Write-Host "Installing composer dependencies..." -ForegroundColor Cyan
        # Use composer image to install dependencies
        podman run --rm -v "${PWD}/src:/app:Z" -w /app composer:latest install --no-dev --optimize-autoloader
    }
}

function Start-Container {
    Stop-Container
    
    # Ensure composer dependencies are installed
    Install-ComposerDeps
    
    Write-Host "Starting container..." -ForegroundColor Green
    
    $envArgs = @()
    if (-not [string]::IsNullOrEmpty($SteamApiKey) -and $SteamApiKey -ne "your_steam_api_key_here") {
        $envArgs = @("-e", "STEAM_API_KEY=$SteamApiKey")
    }
    
    podman run -d `
        --name $ContainerName `
        -p "${Port}:80" `
        -v "${PWD}/src:/var/www/html:Z" `
        @envArgs `
        $ImageName
    
    Write-Host ""
    Write-Host "App is running at: http://localhost:$Port" -ForegroundColor Cyan
}

function Build-Image {
    Write-Host "Building image..." -ForegroundColor Green
    podman build -t $ImageName .
}

switch ($Command) {
    "start" {
        # Check if image exists
        $imageExists = podman images -q $ImageName 2>$null
        if (-not $imageExists) {
            Build-Image
        }
        Start-Container
    }
    "stop" {
        Write-Host "Stopping Isaac Progress Tracker..." -ForegroundColor Yellow
        Stop-Container
        Write-Host "Stopped." -ForegroundColor Green
    }
    "restart" {
        Write-Host "Restarting Isaac Progress Tracker..." -ForegroundColor Yellow
        Start-Container
    }
    "logs" {
        Write-Host "Showing logs (Ctrl+C to exit)..." -ForegroundColor Cyan
        podman logs -f $ContainerName
    }
    "build" {
        Build-Image
        # Force reinstall composer deps on rebuild
        if (Test-Path "src/vendor") {
            Remove-Item -Recurse -Force "src/vendor"
        }
        Start-Container
    }
    "shell" {
        Write-Host "Opening shell in container..." -ForegroundColor Cyan
        podman exec -it $ContainerName /bin/bash
    }
}
