<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');

// ডাটাবেস কানেকশন ঠিক আছে কি না চেক করুন
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// --- ১. প্রোফাইল আপডেট লজিক ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['did'])) {
    $stmt = $conn->prepare("UPDATE devices SET name=?, fname=?, mname=?, addr=?, mobile=?, email=? WHERE device_id=?");
    $stmt->bind_param("sssssss", $_POST['name'], $_POST['fname'], $_POST['mname'], $_POST['addr'], $_POST['mobile'], $_POST['email'], $_POST['did']);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}

// --- ২. প্রোফাইল ডাটা রিড লজিক ---
if (isset($_GET['get_profile']) && isset($_GET['did'])) {
    $stmt = $conn->prepare("SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = ?");
    $stmt->bind_param("s", $_GET['did']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
    exit;
}

// --- ৩. চ্যানেল লিস্ট লজিক ---
$did = isset($_GET['did']) ? trim($_GET['did']) : '';
if (empty($did)) {
    echo json_encode(["status" => "error", "message" => "Device ID missing"]);
    exit;
}

// ডিভাইস চেক এবং ইনসার্ট
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
if ((int)$device['status'] !== 1) {
    echo json_encode(["status" => "inactive"]);
    exit;
}

// চ্যানেল লিস্ট ফেচ করা
$channels = [];
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"           => $row['channel_name'],
        "url"            => $row['channel_url'],
        "ads_status"     => (int)$row['ads_status'],
        "ticker_text"    => $row['ticker_text'] ?? "Welcome!",
        "ticker_enabled" => (int)($row['ticker_enabled'] ?? 1),
        "ticker_speed"   => (int)($row['ticker_speed'] ?? 50),
        "ad_url"         => $row['ad_url'] ?? "",
        "ad_enabled"     => (int)($row['ad_enabled'] ?? 0),
        "ad_duration"    => (int)($row['ad_duration'] ?? 30),
        "live_text"      => $row['live_text'] ?? "LIVE"
    ]; 
}

// সেটিংস ফেচ করা
$settings = $conn->query("SELECT * FROM site_settings LIMIT 1")->fetch_assoc();

echo json_encode([
    "status"           => "active",
    "channels"         => $channels,
    "app_notice"       => $settings['app_notice'] ?? "স্বাগতম!",
    "admin_whatsapp"   => $settings['admin_whatsapp'] ?? ""
]);
?>
