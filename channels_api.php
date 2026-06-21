<?php
require_once 'config/db.php'; 
header('Content-Type: application/json; charset=utf-8');

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection error"]);
    exit;
}

// ... (আপনার আগের প্রোফাইল লজিকগুলো এখানে থাকবে) ...

// চ্যানেল লিস্ট ফেচ করা - এখানে কলামের নামগুলো আপনার add_channels.php এর সাথে মিলিয়ে আপডেট করা হয়েছে
$channels = [];
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

if ($query) {
    while($row = $query->fetch_assoc()){ 
        $channels[] = [
            "name"           => (string)$row['channel_name'],
            "url"            => (string)$row['channel_url'],
            "ads_status"     => (int)($row['ad_enabled'] ?? 0), // ad_enabled কে ads_status হিসেবে ম্যাপ করা হয়েছে
            "ticker_text"    => (string)($row['ticker_text'] ?? ""),
            "ticker_enabled" => (int)($row['ticker_enabled'] ?? 0),
            "ticker_speed"   => (int)($row['ticker_speed'] ?? 50),
            "ad_url"         => (string)($row['ad_url'] ?? ""),
            "ad_enabled"     => (int)($row['ad_enabled'] ?? 0),
            "ad_duration"    => (int)($row['ad_duration'] ?? 30),
            "live_text"      => (string)($row['live_text'] ?? "LIVE")
        ]; 
    }
}

// সাইট সেটিংস
$settings = $conn->query("SELECT * FROM site_settings LIMIT 1")->fetch_assoc();

echo json_encode([
    "status"           => "active",
    "channels"         => $channels,
    "app_notice"       => $settings['app_notice'] ?? "স্বাগতম!",
    "admin_whatsapp"   => $settings['admin_whatsapp'] ?? ""
]);
?>
