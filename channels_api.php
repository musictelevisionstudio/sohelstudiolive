<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_GET['did']) ? trim($_GET['did']) : '';
if (empty($did)) { echo json_encode(["status" => "error"]); exit; }

// ডিভাইস চেক
$stmt = $conn->prepare("SELECT status FROM devices WHERE device_id = ?");
$stmt->bind_param("s", $did);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0 || $res->fetch_assoc()['status'] != 1) { echo json_encode(["status" => "inactive"]); exit; }

$channels = [];
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"             => $row['channel_name'],
        "url"              => $row['channel_url'], // আপনার index.html এটাই খুঁজছে
        "status"           => (int)$row['status'],
        
        // বিজ্ঞাপন সম্পর্কিত
        "ads_status"       => (int)$row['ads_status'],
        "ad_enabled"       => (int)$row['ad_enabled'],
        "ad_url"           => $row['ad_url'],
        "ad_duration"      => (int)$row['ad_duration'],
        
        // লাইভ এবং টিক্কার
        "live_text"        => $row['live_text'],
        "live_button_text" => $row['live_text'], 
        "ticker_text"      => $row['ticker_text'],
        "ticker_speed"     => (int)$row['ticker_speed']
    ]; 
}

echo json_encode(["status" => "active", "channels" => $channels]);
?>
