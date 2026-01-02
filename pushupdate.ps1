#!/usr/bin/env pwsh

# Isaac Progress Tracker - Podman Build and Deploy Script
# Builds the image, pushes to Docker Hub, and deploys to VPS

param(
    [string]$ImageName = "maxijabase/isaac-tracker",
    [string]$Tag = "latest",
    [switch]$SkipPodman = $false,
    [switch]$SkipDeploy = $false,
    [string]$SSHHost = "149.50.130.122",
    [string]$SSHUser = "root",
    [int]$SSHPort = 6000,
    [string]$SSHKeyPath = "",
    [string]$DeployPath = "/docker/isaac-tracker"
)

$ErrorActionPreference = "Stop"

function Write-Status {
    param([string]$Message, [string]$Color = "White")
    Write-Host "[$(Get-Date -Format 'HH:mm:ss')] $Message" -ForegroundColor $Color
}

function Test-PodmanAvailable {
    try {
        podman version --format "{{.Server.Version}}" 2>$null | Out-Null
        if ($LASTEXITCODE -ne 0) { return $false }
        
        podman ps 2>$null | Out-Null
        if ($LASTEXITCODE -ne 0) { return $false }
        
        return $true
    }
    catch {
        return $false
    }
}

function Start-PodmanDesktop {
    Write-Status "Starting Podman Desktop..." "Yellow"
    
    $podmanPaths = @(
        "${env:LOCALAPPDATA}\Programs\Podman Desktop\Podman Desktop.exe",
        "${env:PROGRAMFILES}\Podman Desktop\Podman Desktop.exe",
        "${env:PROGRAMFILES}\RedHat\Podman Desktop\Podman Desktop.exe"
    )
    
    $podmanExe = $null
    foreach ($path in $podmanPaths) {
        if (Test-Path $path) {
            $podmanExe = $path
            break
        }
    }
    
    if (-not $podmanExe) {
        Write-Status "Podman Desktop not found. Please ensure it's installed." "Red"
        exit 1
    }
    
    try {
        $existingProcess = Get-Process -Name "Podman Desktop" -ErrorAction SilentlyContinue
        if ($existingProcess) {
            Write-Status "Podman Desktop is already running" "Green"
            return
        }
        
        $null = Start-Process -FilePath "cmd.exe" -ArgumentList "/c", "start", "`"`"", "`"$podmanExe`"" -WindowStyle Hidden -Wait:$false
        Start-Sleep -Seconds 1
        
        Write-Status "Podman Desktop started successfully" "Green"
    }
    catch {
        Write-Status "Failed to start Podman Desktop: $($_.Exception.Message)" "Red"
        exit 1
    }
}

function Wait-ForPodman {
    Write-Status "Waiting for Podman to be ready..." "Yellow"
    
    $maxWaitTime = 120
    $waitInterval = 3
    $elapsed = 0
    
    while ($elapsed -lt $maxWaitTime) {
        Write-Status "Checking Podman availability... ($elapsed/$maxWaitTime seconds)" "Yellow"
        
        if (Test-PodmanAvailable) {
            Write-Status "Podman is ready!" "Green"
            return $true
        }
        
        Start-Sleep -Seconds $waitInterval
        $elapsed += $waitInterval
    }
    
    Write-Status "Timeout waiting for Podman." "Red"
    return $false
}

function Build-PodmanImage {
    param([string]$ImageName, [string]$Tag)
    
    $fullImageName = "${ImageName}:${Tag}"
    Write-Status "Building image: $fullImageName" "Yellow"
    
    try {
        podman build --no-cache -t $fullImageName .
        if ($LASTEXITCODE -eq 0) {
            Write-Status "Image built successfully!" "Green"
            return $true
        } else {
            Write-Status "Build failed with exit code: $LASTEXITCODE" "Red"
            return $false
        }
    }
    catch {
        Write-Status "Error during build: $($_.Exception.Message)" "Red"
        return $false
    }
}

function Push-PodmanImage {
    param([string]$ImageName, [string]$Tag)
    
    $fullImageName = "${ImageName}:${Tag}"
    Write-Status "Pushing image: $fullImageName" "Yellow"
    
    try {
        podman push $fullImageName
        if ($LASTEXITCODE -eq 0) {
            Write-Status "Image pushed successfully!" "Green"
            return $true
        } else {
            Write-Status "Push failed with exit code: $LASTEXITCODE" "Red"
            return $false
        }
    }
    catch {
        Write-Status "Error during push: $($_.Exception.Message)" "Red"
        return $false
    }
}

function Deploy-ToVPS {
    param([string]$SSHHost, [string]$SSHUser, [int]$SSHPort, [string]$SSHKeyPath, [string]$DeployPath)
    
    Write-Status "Deploying to VPS: $SSHUser@$SSHHost`:$SSHPort" "Yellow"
    
    if ([string]::IsNullOrEmpty($SSHHost) -or [string]::IsNullOrEmpty($SSHUser)) {
        Write-Status "SSH host or user not specified." "Red"
        return $false
    }
    
    # Build SSH command
    $sshCommand = "ssh"
    if (-not [string]::IsNullOrEmpty($SSHKeyPath)) {
        if (-not (Test-Path $SSHKeyPath)) {
            Write-Status "SSH key file not found: $SSHKeyPath" "Red"
            return $false
        }
        $sshCommand += " -i `"$SSHKeyPath`""
    }
    $sshCommand += " -p $SSHPort"
    $sshCommand += " -o StrictHostKeyChecking=no -o ConnectTimeout=30"
    $sshCommand += " $SSHUser@$SSHHost"
    
    # Pull and recreate container (assumes docker-compose.yml exists at DeployPath)
    $remoteCommand = "cd $DeployPath && docker compose pull && docker compose up --force-recreate -d"
    
    Write-Status "Executing deployment..." "Yellow"
    
    try {
        $fullCommand = "$sshCommand `"$remoteCommand`""
        Invoke-Expression $fullCommand
        
        if ($LASTEXITCODE -eq 0) {
            Write-Status "Deployment completed successfully!" "Green"
            return $true
        } else {
            Write-Status "Deployment failed with exit code: $LASTEXITCODE" "Red"
            return $false
        }
    }
    catch {
        Write-Status "Error during deployment: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Main execution
Write-Status "=== Isaac Progress Tracker - Build & Deploy ===" "Cyan"
Write-Status "Image: ${ImageName}:${Tag}" "Cyan"

# Check if Dockerfile exists
if (-not (Test-Path "Dockerfile")) {
    Write-Status "Dockerfile not found! Run this script from the project root." "Red"
    exit 1
}

# Start Podman Desktop if not skipped
if (-not $SkipPodman) {
    Start-PodmanDesktop
    if (-not (Wait-ForPodman)) {
        exit 1
    }
} else {
    Write-Status "Skipping Podman Desktop startup" "Yellow"
    if (-not (Test-PodmanAvailable)) {
        Write-Status "Podman is not available. Please start Podman Desktop manually." "Red"
        exit 1
    }
}

# Build the image
if (-not (Build-PodmanImage -ImageName $ImageName -Tag $Tag)) {
    exit 1
}

# Push the image
if (-not (Push-PodmanImage -ImageName $ImageName -Tag $Tag)) {
    exit 1
}

# Deploy to VPS
if (-not $SkipDeploy) {
    Write-Status "=== Starting VPS Deployment ===" "Cyan"
    
    $deployHost = if ($SSHHost) { $SSHHost } else { $env:SSH_HOST }
    $deployUser = if ($SSHUser) { $SSHUser } else { $env:SSH_USER }
    $deployPort = if ($SSHPort) { $SSHPort } else { if ($env:SSH_PORT) { [int]$env:SSH_PORT } else { 6000 } }
    $deployKeyPath = if ($SSHKeyPath) { $SSHKeyPath } else { $env:SSH_KEY_PATH }
    
    if ($deployHost -and $deployUser) {
        if (-not (Deploy-ToVPS -SSHHost $deployHost -SSHUser $deployUser -SSHPort $deployPort -SSHKeyPath $deployKeyPath -DeployPath $DeployPath)) {
            Write-Status "Deployment failed, but build and push were successful." "Yellow"
            Write-Status "Manual deploy: ssh -p$deployPort $deployUser@$deployHost 'cd $DeployPath && docker compose pull && docker compose up --force-recreate -d'" "Yellow"
            exit 1
        }
    } else {
        Write-Status "SSH deployment skipped - no SSH parameters provided." "Yellow"
    }
} else {
    Write-Status "Deployment skipped by user request." "Yellow"
}

Write-Status "=== Build and Deploy Complete! ===" "Green"
Write-Status "Image: ${ImageName}:${Tag}" "Green"
Write-Status "URL: https://isaac.maxijabase.dev/" "Green"
