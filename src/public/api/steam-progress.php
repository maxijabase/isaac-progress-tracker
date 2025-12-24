<?php
	require_once(__DIR__ . "/../../config.php");
	
	header('Content-Type: application/json');
	
	// Get API key from POST request
	$apiKey = $_POST['apikey'] ?? '';
	
	// Validate API key format (should be 32 hex characters)
	if(!preg_match('/^[A-Fa-f0-9]{32}$/', $apiKey)) {
		http_response_code(400);
		echo json_encode(['error' => 'Invalid Steam API Key format. It should be 32 hexadecimal characters.']);
		exit;
	}
	
	// Get Steam ID from POST request
	$steamId = $_POST['steamid'] ?? '';
	
	// Validate Steam ID (should be 17 digits)
	if(!preg_match('/^[0-9]{17}$/', $steamId)) {
		http_response_code(400);
		echo json_encode(['error' => 'Invalid Steam ID. It should be 17 digits.']);
		exit;
	}
	
	// Build the Steam API URL
	$apiUrl = sprintf(
		'https://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?key=%s&steamid=%s&appid=%s',
		urlencode($apiKey),
		urlencode($steamId),
		urlencode(STEAM_APP_ID)
	);
	
	// Make the request to Steam API
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curlError = curl_error($ch);
	curl_close($ch);
	
	if($curlError) {
		http_response_code(500);
		echo json_encode(['error' => 'Failed to connect to Steam API: ' . $curlError]);
		exit;
	}
	
	if($httpCode !== 200) {
		http_response_code($httpCode);
		echo json_encode(['error' => 'Steam API returned an error', 'status' => $httpCode]);
		exit;
	}
	
	// Return the Steam API response
	echo $response;
