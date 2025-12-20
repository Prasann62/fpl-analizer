<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$baseUrl = "https://fantasy.premierleague.com/api/";
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// Whitelist allowed endpoints for security
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
    http_response_code(403);
    echo json_encode(["error" => "Invalid endpoint"]);
    exit;
}

// Construct the full URL
// If endpoint contains query parameters (e.g. ?event=1), append them correctly
$url = $baseUrl . $endpoint;

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => curl_error($ch)]);
} else {
    echo $response;
}

curl_close($ch);
?>
