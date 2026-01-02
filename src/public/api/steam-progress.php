<?php
	require_once(__DIR__ . "/../../config.php");
	
	header('Content-Type: application/json');
	
	// Check if host has configured an API key
	if(empty(STEAM_API_KEY)) {
		http_response_code(503);
		echo json_encode(['error' => 'Steam API key not configured by the host.']);
		exit;
	}
	
	// Cache configuration
	$cacheDir = '/tmp/steam-cache';
	$cacheTtl = 300; // 5 minutes
	
	// Rate limiting configuration (IP-based, stored in files)
	$rateLimitDir = '/tmp/steam-ratelimit';
	$rateLimitMax = 10; // max requests per window
	$rateLimitWindow = 60; // seconds
	
	// Get client IP (handle proxies)
	function getClientIp(): string {
		$headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
		
		foreach($headers as $header) {
			if(!empty($_SERVER[$header])) {
				$ip = $_SERVER[$header];
				// Handle comma-separated IPs (X-Forwarded-For)
				if(str_contains($ip, ',')) {
					$ip = trim(explode(',', $ip)[0]);
				}
				if(filter_var($ip, FILTER_VALIDATE_IP)) {
					return $ip;
				}
			}
		}
		
		return 'unknown';
	}
	
	// IP-based rate limiting
	function checkRateLimit(string $ip, string $rateLimitDir, int $rateLimitMax, int $rateLimitWindow): array {
		if(!is_dir($rateLimitDir)) {
			@mkdir($rateLimitDir, 0755, true);
		}
		
		$ipHash = hash('sha256', $ip);
		$rateLimitFile = $rateLimitDir . '/' . $ipHash . '.json';
		$now = time();
		$requests = [];
		
		// Load existing requests
		if(file_exists($rateLimitFile)) {
			$data = @file_get_contents($rateLimitFile);
			if($data !== false) {
				$requests = json_decode($data, true) ?: [];
			}
		}
		
		// Filter out old requests outside the window
		$requests = array_filter($requests, function($timestamp) use ($now, $rateLimitWindow) {
			return ($now - $timestamp) < $rateLimitWindow;
		});
		
		// Check if rate limited
		if(count($requests) >= $rateLimitMax) {
			$oldestRequest = min($requests);
			$retryAfter = $rateLimitWindow - ($now - $oldestRequest);
			return ['limited' => true, 'retry_after' => max(1, $retryAfter)];
		}
		
		// Record this request
		$requests[] = $now;
		@file_put_contents($rateLimitFile, json_encode(array_values($requests)));
		
		return ['limited' => false, 'remaining' => $rateLimitMax - count($requests)];
	}
	
	// Check rate limit
	$clientIp = getClientIp();
	$rateLimit = checkRateLimit($clientIp, $rateLimitDir, $rateLimitMax, $rateLimitWindow);
	
	if($rateLimit['limited']) {
		http_response_code(429);
		header('Retry-After: ' . $rateLimit['retry_after']);
		echo json_encode(['error' => 'Too many requests. Please wait ' . $rateLimit['retry_after'] . ' seconds before trying again.']);
		exit;
	}
	
	// Add rate limit headers
	header('X-RateLimit-Remaining: ' . ($rateLimit['remaining'] ?? 0));
	
	// Get Steam ID from POST request
	$steamId = $_POST['steamid'] ?? '';
	
	// Validate Steam ID (should be 17 digits)
	if(!preg_match('/^[0-9]{17}$/', $steamId)) {
		http_response_code(400);
		echo json_encode(['error' => 'Invalid Steam ID. It should be 17 digits.']);
		exit;
	}
	
	// Generate cache key based on Steam ID only (since API key is server-side now)
	$cacheKey = hash('sha256', $steamId . STEAM_APP_ID);
	$cacheFile = $cacheDir . '/' . $cacheKey . '.json';
	$now = time();
	
	// Check cache first
	if(file_exists($cacheFile)) {
		$cacheAge = $now - filemtime($cacheFile);
		
		if($cacheAge < $cacheTtl) {
			// Serve cached response
			$cachedData = file_get_contents($cacheFile);
			if($cachedData !== false) {
				header('X-Cache: HIT');
				header('X-Cache-Age: ' . $cacheAge);
				echo $cachedData;
				exit;
			}
		}
	}
	
	// Build the Steam API URL using server-side API key
	$apiUrl = sprintf(
		'https://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?key=%s&steamid=%s&appid=%s',
		urlencode(STEAM_API_KEY),
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
	if($httpCode === 401 || $httpCode === 403) {
		// Don't expose that it's an API key issue to users
		http_response_code(500);
		error_log('Steam API key error: HTTP ' . $httpCode);
		echo json_encode(['error' => 'Steam API configuration error. Please contact the site administrator.']);
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
	
	// Cache the successful response
	if(!is_dir($cacheDir)) {
		@mkdir($cacheDir, 0755, true);
	}
	
	if(is_writable($cacheDir) || @mkdir($cacheDir, 0755, true)) {
		@file_put_contents($cacheFile, $response);
	}
	
	// Return the Steam API response
	header('X-Cache: MISS');
	echo $response;
