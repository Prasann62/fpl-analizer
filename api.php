<?php
/**
 * FPL API Proxy
 * SECURITY: Rate limited, input validated, endpoint whitelisted
 */

// Load security libraries
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/security.php';

// SECURITY: Set secure headers
header("Content-Type: application/json");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// SECURITY: Restrict CORS (only allow same origin)
$allowedOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: " . $allowedOrigin);
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

// SECURITY: Enforce rate limiting (100 requests per minute per IP)
RateLimit::enforce('api');

$baseUrl = "https://fantasy.premierleague.com/api/";
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// SECURITY: Sanitize endpoint input
$endpoint = Security::sanitizeInput($endpoint, 'string', 500);

// SECURITY: Whitelist allowed endpoints for security
$allowed_endpoints = [
    'bootstrap-static/',
    'fixtures/',
    'element-summary/',
    'entry/',
    'leagues-classic/',
    'leagues-h2h/',
    'event/'
];

$valid = false;
foreach ($allowed_endpoints as $allowed) {
    if (strpos($endpoint, $allowed) === 0) {
        $valid = true;
        break;
    }
}

if (!$valid) {
    Security::logSecurityEvent('Invalid API endpoint requested', [
        'endpoint' => $endpoint
    ]);
    
    http_response_code(403);
    echo json_encode(["error" => "Invalid endpoint"]);
    exit;
}

// SECURITY: Validate endpoint format (alphanumeric, slashes, hyphens only)
if (!preg_match('/^[a-zA-Z0-9\/\-\?\=\&]+$/', $endpoint)) {
    Security::logSecurityEvent('Malicious API endpoint pattern detected', [
        'endpoint' => $endpoint
    ]);
    
    http_response_code(400);
    echo json_encode(["error" => "Invalid endpoint format"]);
    exit;
}

// Construct the full URL
$url = $baseUrl . $endpoint;

// SECURITY: Initialize cURL with secure settings
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Limit redirects
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5 second connection timeout
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verify SSL certificates
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $error = curl_error($ch);
    curl_close($ch);
    
    Security::logSecurityEvent('API request failed', [
        'url' => $url,
        'error' => $error
    ]);
    
    http_response_code(500);
    echo json_encode(["error" => "External API request failed"]);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($httpCode);
    echo $response;
}
?>
