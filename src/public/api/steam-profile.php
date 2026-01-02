<?php
/**
 * Steam Profile API Endpoint
 * 
 * Fetches Steam user profile (avatar, name) for display in the navbar
 */

require_once(__DIR__ . "/../../config.php");

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get Steam ID from POST data
$steamId = $_POST['steamid'] ?? '';

// Validate Steam ID format (17 digits)
if (!preg_match('/^[0-9]{17}$/', $steamId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Steam ID format']);
    exit;
}

// Check if Steam API key is configured
if (empty(STEAM_API_KEY)) {
    http_response_code(500);
    echo json_encode(['error' => 'Steam API key not configured']);
    exit;
}

// Fetch player summary from Steam API
$apiUrl = sprintf(
    'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=%s&steamids=%s',
    STEAM_API_KEY,
    $steamId
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'IsaacProgressTracker/1.0');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to fetch profile from Steam']);
    exit;
}

$data = json_decode($response, true);

if (!isset($data['response']['players'][0])) {
    http_response_code(404);
    echo json_encode(['error' => 'Steam profile not found']);
    exit;
}

$player = $data['response']['players'][0];

// Return only the data we need
echo json_encode([
    'steamid' => $player['steamid'],
    'personaname' => $player['personaname'],
    'avatar' => $player['avatar'],
    'avatarmedium' => $player['avatarmedium'],
    'profileurl' => $player['profileurl']
]);

