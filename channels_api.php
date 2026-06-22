<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');

// --- ১. প্রোফাইল আপডেট ও রিড লজিক একই থাকবে ---
// (আপনার আগের লজিক অনুযায়ী ঠিক আছে)

// --- ২. চ্যানেল লিস্ট এবং ডিভাইস স্ট্যাটাস লজিক (সংশোধিত) ---
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
// আপনার ইনডেক্সের সাথে মিল রেখে চ্যানেল লিস্ট তৈরি
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"           => $row['channel_name'],
        "url"            => $row['channel_url'],
        "ads_status"     => (int)$row['ads_status'], // ইনডেক্সের ad_enabled লজিকের সাথে মিলবে
        "ad_url"         => $row['ad_url'],
        "ad_duration"    => (int)$row['ad_duration'],
        "ticker_text"    => $row['ticker_text'],
        "ticker_speed"   => (int)$row['ticker_speed'],
        "live_text"      => $row['live_text']       // ইনডেক্সের liveBtn টেক্সট
    ]; 
}

echo json_encode([
    "status"   => "active",
    "channels" => $channels
]);
?>