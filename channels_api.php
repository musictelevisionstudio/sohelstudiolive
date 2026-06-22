<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_REQUEST['did']) ? trim($_REQUEST['did']) : '';
if (empty($did)) { echo json_encode(["status" => "error", "message" => "Device ID Required"]); exit; }

// ১. প্রোফাইল লোড (GET)
if (isset($_GET['get_profile']) && $_GET['get_profile'] == 'true') {
    $stmt = $conn->prepare("SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = ?");
    $stmt->bind_param("s", $did);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc() ?: []);
    exit;
}

// ২. প্রোফাইল আপডেট (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE devices SET name=?, fname=?, mname=?, addr=?, mobile=?, email=? WHERE device_id=?");
    $stmt->bind_param("sssssss", $_POST['name'], $_POST['fname'], $_POST['mname'], $_POST['addr'], $_POST['mobile'], $_POST['email'], $did);
    echo json_encode($stmt->execute() ? ["status" => "success"] : ["status" => "error", "db_error" => $conn->error]);
    exit;
}

// ৩. চ্যানেল লিস্ট ও স্টেশন সেটিং (মূল অ্যাপের জন্য)
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");
$channels = [];
while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"             => $row['channel_name'],
        "url"              => $row['channel_url'],
        "status"           => (int)$row['status'],
        "ads_status"       => (int)$row['ads_status'],
        "ad_url"           => $row['ad_url'],
        "ad_duration"      => (int)$row['ad_duration'],
        "ad_size"          => (int)($row['ad_size'] ?? 70), // বিজ্ঞাপন সাইজ
        "live_text"        => $row['live_text'],
        "live_animation"   => $row['live_animation'] ?? 'none', // লাইভ অ্যানিমেশন
        "ticker_text"      => $row['ticker_text'],
        "ticker_speed"     => (int)($row['ticker_speed'] ?? 40)
    ]; 
}
echo json_encode(["status" => "active", "channels" => $channels]);
?>
