
<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');

// --- ১. প্রোফাইল আপডেট লজিক (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['did'])) {
    $did = mysqli_real_escape_string($conn, $_POST['did']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname = mysqli_real_escape_string($conn, $_POST['mname']);
    $addr = mysqli_real_escape_string($conn, $_POST['addr']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE devices SET name='$name', fname='$fname', mname='$mname', addr='$addr', mobile='$mobile', email='$email' WHERE device_id='$did'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "db_error" => mysqli_error($conn)]);
    }
    exit;
}

// --- ২. প্রোফাইল ডাটা রিড লজিক (GET Request) ---
$did = isset($_GET['did']) ? trim($_GET['did']) : '';

if (isset($_GET['get_profile']) && !empty($did)) {
    $did = mysqli_real_escape_string($conn, $did);
    $query = mysqli_query($conn, "SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = '$did'");
    echo json_encode(mysqli_fetch_assoc($query));
    exit;
}

// --- ৩. চ্যানেল লিস্ট এবং ডিভাইস স্ট্যাটাস লজিক ---
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
// আপনার টেবিলের কলামগুলোর সাথে মিল রেখে কোয়েরি সাজানো হয়েছে
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");

while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name"               => $row['channel_name'],
        "url"                => $row['channel_url'],
        "ads_status"         => (int)$row['ads_status'],
        "ticker_text"        => $row['ticker_text'],
        "ticker_enabled"     => (int)$row['ticker_enabled'],
        "ticker_speed"       => (int)$row['ticker_speed'],
        "ad_url"             => $row['ad_url'],
        "ad_enabled"         => (int)$row['ad_enabled'],
        "ad_duration"        => (int)$row['ad_duration'],
        "live_button_text"   => $row['live_text'] // আপনার অরিজিনাল কোডের সাথে সামঞ্জস্যপূর্ণ
    ]; 
}

$settings = $conn->query("SELECT * FROM site_settings LIMIT 1")->fetch_assoc();

echo json_encode([
    "status"           => "active",
    "channels"         => $channels,
    "app_notice"       => $settings['app_notice'] ?? "",
    "admin_whatsapp"   => $settings['admin_whatsapp'] ?? ""
]);
?>