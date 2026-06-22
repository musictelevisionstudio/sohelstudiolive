<?php
/* File: channel_api.php - FULLY UPDATED WITH ALL DATABASE COLUMNS */
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_GET['did']) ? trim($_GET['did']) : '';
if (empty($did)) {
    echo json_encode(["status" => "error", "message" => "Device ID missing"]);
    exit;
}

$stmt = $conn->prepare("SELECT status FROM devices WHERE device_id = ?");
$stmt->bind_param("s", $did);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    $insert = $conn->prepare("INSERT INTO devices (device_id, status) VALUES (?, 0)");
    $insert->bind_param("s", $did);
    $insert->execute();
    echo json_encode(["status" => "inactive"]);
    exit;
}

$device = $res->fetch_assoc();
if ($device['status'] != 1) {
    echo json_encode(["status" => "inactive"]);
    exit;
}

$channels = [];
// ডাটাবেস থেকে সব কলামসহ তথ্য তোলা হচ্ছে
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "id"               => (int)$row['id'],
        "channel_name"     => $row['channel_name'],
        "channel_url"      => $row['channel_url'],
        "status"           => (int)$row['status'],
        "channel_order"    => (int)$row['channel_order'],
        
        // বিজ্ঞাপন সম্পর্কিত কলামগুলো (স্ক্রিনশট অনুযায়ী)
        "ad_url"           => $row['ad_url'],
        "ad_enabled"       => (int)$row['ad_enabled'],
        "ads_status"       => (int)$row['ads_status'],
        "ad_type"          => $row['ad_type'],
        "ad_size"          => (int)$row['ad_size'],
        "ad_duration"      => (int)$row['ad_duration'],
        
        // লাইভ এবং এনিমেশন সম্পর্কিত
        "live_text"        => $row['live_text'],
        "live_animation"   => $row['live_animation'],
        "live_enabled"     => (int)$row['live_enabled'],
        
        // হেডলাইন বা টিক্কার সম্পর্কিত
        "ticker_text"      => $row['ticker_text'],
        "ticker_enabled"   => (int)$row['ticker_enabled'],
        "ticker_speed"     => (int)$row['ticker_speed'],
        "ticker_direction" => $row['ticker_direction']
    ]; 
}

echo json_encode([
    "status"   => "active",
    "channels" => $channels
]);
?>