<?php
/* File: channels_api.php - MASTER FINAL VERSION */
require_once 'config/db.php'; 
header('Content-Type: application/json; charset=utf-8');

// ১. রিকোয়েস্ট চেক
$did = isset($_GET['did']) ? trim($_GET['did']) : '';
if (empty($did)) {
    echo json_encode(["status" => "error", "message" => "Device ID is required"]);
    exit;
}

// ২. ডিভাইস স্ট্যাটাস চেক ও ইনসার্ট
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

// ৩. চ্যানেল লিস্ট ফেচ করা (আপনার টেবিল অনুযায়ী)
$channels = [];
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

if ($query) {
    while($row = $query->fetch_assoc()){ 
        $channels[] = [
            "name"           => (string)$row['channel_name'],
            "url"            => (string)$row['channel_url'],
            "ads_status"     => (int)$row['ads_status'],
            "ticker_text"    => (string)$row['ticker_text'],
            "ticker_enabled" => (int)$row['ticker_enabled'],
            "ticker_speed"   => (int)$row['ticker_speed'],
            "ad_url"         => (string)$row['ad_url'],
            "ad_enabled"     => (int)$row['ad_enabled'],
            "ad_duration"    => (int)$row['ad_duration'],
            "live_text"      => (string)$row['live_text']
        ]; 
    }
}

// ৪. সাইট সেটিংস ফেচ করা (site_settings টেবিল থেকে)
$settings = $conn->query("SELECT * FROM site_settings LIMIT 1")->fetch_assoc();

// ৫. প্রোফাইল ডাটা রিড করা (যদি থাকে)
$profile = $conn->prepare("SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = ?");
$profile->bind_param("s", $did);
$profile->execute();
$profData = $profile->get_result()->fetch_assoc();

// ফাইনাল মাস্টার রেসপন্স
echo json_encode([
    "status"           => "active",
    "channels"         => $channels,
    "profile"          => $profData,
    "app_notice"       => $settings['app_notice'] ?? "Welcome!",
    "admin_whatsapp"   => $settings['admin_whatsapp'] ?? ""
]);
?>
