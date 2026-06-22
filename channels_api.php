<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
// ক্যাশ যাতে না ধরে সেই জন্য হেডার
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
// আপনার ডাটাবেসের টেবিল স্ট্রাকচারের সাথে মিল রেখে সিলেক্ট কোয়েরি
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"           => $row['channel_name'],
        "url"            => $row['channel_url'],
        "status"         => (int)$row['status'],
        
        // বিজ্ঞাপন সম্পর্কিত ডাটা
        "ad_enabled"     => (int)$row['ad_enabled'],
        "ad_url"         => $row['ad_url'],
        "ad_duration"    => (int)$row['ad_duration'],
        
        // লাইভ এবং এনিমেশন সম্পর্কিত ডাটা
        "live_text"      => $row['live_text'],
        "live_enabled"   => (int)$row['live_enabled'],
        "live_animation" => $row['live_animation'],
        
        // হেডলাইন বা টিক্কার সম্পর্কিত ডাটা
        "ticker_text"    => $row['ticker_text'],
        "ticker_enabled" => (int)$row['ticker_enabled'],
        "ticker_speed"   => (int)$row['ticker_speed'],
        "ticker_direction"=> $row['ticker_direction']
    ]; 
}

echo json_encode([
    "status"   => "active",
    "channels" => $channels
]);
?>
