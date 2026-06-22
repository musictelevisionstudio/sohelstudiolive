<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_GET['did']) ? trim($_GET['did']) : '';
if (empty($did)) { exit; }

$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");
$channels = [];

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"             => $row['channel_name'],
        "url"              => $row['channel_url'],
        "status"           => (int)$row['status'],
        
        // বিজ্ঞাপন সম্পর্কিত
        "ads_status"       => (int)$row['ads_status'],
        "ad_enabled"       => (int)$row['ad_enabled'],
        "ad_url"           => $row['ad_url'],
        "ad_duration"      => (int)$row['ad_duration'],
        "ad_type"          => $row['ad_type'], // short বা full
        "ad_size"          => (int)$row['ad_size'],
        
        // লাইভ এবং টিক্কার
        "live_text"        => $row['live_text'], // এটি আপনার বাংলা টেক্সট নিবে
        "live_button_text" => $row['live_text'], // মনিটরে দেখানোর জন্য
        "ticker_text"      => $row['ticker_text'],
        "ticker_speed"     => (int)$row['ticker_speed']
    ]; 
}

echo json_encode(["status" => "active", "channels" => $channels]);
?>
