<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_REQUEST['did']) ? trim($_REQUEST['did']) : '';
if (empty($did)) { echo json_encode(["status" => "error", "message" => "Device ID Required"]); exit; }

$stmt_check = $conn->prepare("INSERT IGNORE INTO devices (device_id, status, last_visit) VALUES (?, 0, NOW())");
$stmt_check->bind_param("s", $did);
$stmt_check->execute();

$conn->prepare("UPDATE devices SET last_visit = NOW() WHERE device_id = ?")->execute([$did]);

if (isset($_GET['get_profile']) && $_GET['get_profile'] == 'true') {
    $stmt = $conn->prepare("SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = ?");
    $stmt->bind_param("s", $did);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc() ?: []);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE devices SET name=?, fname=?, mname=?, addr=?, mobile=?, email=? WHERE device_id=?");
    $stmt->bind_param("sssssss", $_POST['name'], $_POST['fname'], $_POST['mname'], $_POST['addr'], $_POST['mobile'], $_POST['email'], $did);
    echo json_encode($stmt->execute() ? ["status" => "success"] : ["status" => "error"]);
    exit;
}

$stmt_status = $conn->prepare("SELECT status FROM devices WHERE device_id = ?");
$stmt_status->bind_param("s", $did);
$stmt_status->execute();
$device = $stmt_status->get_result()->fetch_assoc();

if (!$device || (int)$device['status'] === 0) {
    echo json_encode(["status" => "inactive", "message" => "আপনার ডিভাইসটি ব্লকড। অনুগ্রহ করে সাপোর্টের সাথে যোগাযোগ করুন।"]);
    exit;
}

$query = $conn->query("SELECT * FROM channels WHERE status = 'true' ORDER BY channel_order ASC");
$channels = [];
while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"             => $row['channel_name'],
        "url"              => $row['channel_url'],
        "ads_status"       => ($row['ads_status'] === 'true'),
        "ad_url"           => $row['ad_url'],
        "ad_duration"      => (int)$row['ad_duration'],
        "ticker_text"      => $row['ticker_text'],
        "ticker_speed"     => (int)$row['ticker_speed'],
        "live_text"        => $row['live_text'],
        "ticker_enabled"   => ($row['ticker_enabled'] === 'true'),
        "ad_enabled"       => ($row['ad_enabled'] === 'true'),
        "live_enabled"     => ($row['live_enabled'] === 'true'),
        "ticker_direction" => $row['ticker_direction'],
        "ad_type"          => $row['ad_type'],
        "ad_size"          => (int)$row['ad_size']
    ]; 
}
echo json_encode(["status" => "active", "channels" => $channels]);
?>
