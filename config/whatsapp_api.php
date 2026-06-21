
<?php
/* File: config/whatsapp_api.php - UPDATED */

// 1. ডাইরেক্ট এক্সেস আটকানো
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    exit("Direct access not allowed.");
}

// 2. ডাটাবেস ফাইল চেক করা
if (!file_exists(__DIR__ . '/db.php')) {
    die("Error: db.php file not found.");
}
require_once __DIR__ . '/db.php';

// 3. API সেটিংস ফেচ করা
$settings = [
    'api_url' => '',
    'instance_id' => '',
    'api_token' => '',
    'admin_whatsapp' => '8801615896688'
];

if (isset($conn) && $conn instanceof mysqli) {
    $result = $conn->query("SELECT api_url, instance_id, api_token, admin_whatsapp FROM green_api_settings WHERE id = 1 LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $settings = $result->fetch_assoc();
    }
}

define('GREEN_API_URL', $settings['api_url'] ?? '');
define('GREEN_INSTANCE_ID', $settings['instance_id'] ?? '');
define('GREEN_API_TOKEN', $settings['api_token'] ?? '');
define('ADMIN_WHATSAPP', $settings['admin_whatsapp'] ?? '8801615896688');

/**
 * Function to send WhatsApp messages using cURL
 */
function sendWhatsApp($phone, $message) {
    if (empty(GREEN_API_URL) || empty(GREEN_INSTANCE_ID) || empty(GREEN_API_TOKEN)) {
        return ["status" => "error", "message" => "API configuration is missing."];
    }

    $baseUrl = rtrim(GREEN_API_URL, '/');
    $url = "{$baseUrl}/waInstance" . GREEN_INSTANCE_ID . "/sendMessage/" . GREEN_API_TOKEN;

    $data = [
        "chatId" => (strpos($phone, '@') === false) ? $phone . "@c.us" : $phone,
        "message" => $message
    ];
    
    $json_data = json_encode($data, JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_data)
    ]);
    
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);

    if ($error) {
        return ["status" => "error", "message" => "cURL Error: " . $error];
    }

    $decodedResponse = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return ["status" => "success", "data" => $decodedResponse];
    } else {
        return ["status" => "error", "message" => "API returned HTTP $httpCode", "response" => $decodedResponse];
    }
}
?>