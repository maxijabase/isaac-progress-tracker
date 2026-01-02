<?php
/**
 * Steam OpenID Login Endpoint
 * 
 * Handles Steam OpenID authentication flow:
 * 1. User visits this page → redirects to Steam login
 * 2. Steam redirects back with auth data → validates and extracts Steam ID
 * 3. Returns Steam ID to frontend via redirect with hash
 */

require_once(__DIR__ . "/../../vendor/autoload.php");

use xPaw\Steam\SteamOpenID;

// Determine the return URL (this same endpoint)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$returnUrl = $protocol . '://' . $host . '/api/steam-login.php';

// Base URL for redirecting back to the app
$appUrl = $protocol . '://' . $host . '/';

try {
    $steamOpenID = new SteamOpenID($returnUrl);
    
    if ($steamOpenID->ShouldValidate()) {
        // We're handling a callback from Steam - validate the response
        try {
            $steamId = $steamOpenID->Validate();
            
            // Redirect to app with Steam ID in the hash (triggers auto-sync)
            header('Location: ' . $appUrl . '#' . $steamId);
            exit;
        } catch (Exception $e) {
            // Validation failed
            header('Location: ' . $appUrl . '?error=steam_login_failed');
            exit;
        }
    } else {
        // Redirect user to Steam's login page
        header('Location: ' . $steamOpenID->GetAuthUrl());
        exit;
    }
} catch (Exception $e) {
    // Something went wrong
    header('Location: ' . $appUrl . '?error=steam_login_error');
    exit;
}

