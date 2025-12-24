<?php
	require_once(__DIR__ . "/../../config.php");
	
	// Start session for rate limiting
	session_start();
	
	header('Content-Type: application/json');
	
	// Rate limiting: max 10 requests per minute
	$rateLimitMax = 10;
	$rateLimitWindow = 60; // seconds
	
	// Initialize rate limit tracking
	if(!isset($_SESSION['rate_limit_requests'])) {
		$_SESSION['rate_limit_requests'] = [];
	}
	
	// Clean up old requests outside the window
	$now = time();
	$_SESSION['rate_limit_requests'] = array_filter($_SESSION['rate_limit_requests'], function($timestamp) use ($now, $rateLimitWindow) {
		return ($now - $timestamp) < $rateLimitWindow;
	});
	
	// Check if rate limit exceeded
	if(count($_SESSION['rate_limit_requests']) >= $rateLimitMax) {
		$oldestRequest = min($_SESSION['rate_limit_requests']);
		$retryAfter = $rateLimitWindow - ($now - $oldestRequest);
		
		http_response_code(429);
		header('Retry-After: ' . $retryAfter);
		echo json_encode(['error' => 'Too many requests. Please wait ' . $retryAfter . ' seconds before trying again.']);
		exit;
	}
	
	// Record this request
	$_SESSION['rate_limit_requests'][] = $now;
	
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
	
	// Handle cURL errors
	if($curlError) {
		http_response_code(500);
		echo json_encode(['error' => 'Failed to connect to Steam API. Please try again later.']);
		exit;
	}
	
	// Handle Steam API HTTP errors with friendly messages
	if($httpCode === 401) {
		http_response_code(401);
		echo json_encode(['error' => 'Invalid Steam API Key. Please check your key at steamcommunity.com/dev/apikey']);
		exit;
	}
	
	if($httpCode === 403) {
		http_response_code(403);
		echo json_encode(['error' => 'Access denied. Make sure your Steam profile and game details are set to public.']);
		exit;
	}
	
	if($httpCode === 500) {
		http_response_code(500);
		echo json_encode(['error' => 'Steam API is currently unavailable. Please try again later.']);
		exit;
	}
	
	if($httpCode !== 200) {
		http_response_code($httpCode);
		echo json_encode(['error' => 'Steam API returned an unexpected error (HTTP ' . $httpCode . '). Please try again later.']);
		exit;
	}
	
	// Try to decode the response
	$data = json_decode($response, true);
	
	if(json_last_error() !== JSON_ERROR_NONE) {
		http_response_code(500);
		echo json_encode(['error' => 'Failed to parse Steam API response. Please try again later.']);
		exit;
	}
	
	// Check for empty or invalid response
	if(empty($data) || !isset($data['playerstats'])) {
		http_response_code(404);
		echo json_encode(['error' => 'No achievement data found. This could mean: the Steam ID is invalid, the profile is private, or the game is not owned.']);
		exit;
	}
	
	// Check if achievements exist
	if(!isset($data['playerstats']['achievements']) || empty($data['playerstats']['achievements'])) {
		http_response_code(404);
		echo json_encode(['error' => 'No achievements found for this game. Make sure you own The Binding of Isaac: Rebirth and have played it at least once.']);
		exit;
	}
	
	// Return the Steam API response
	echo $response;
