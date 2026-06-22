<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");

$did = isset($_REQUEST['did']) ? trim($_REQUEST['did']) : '';
if (empty($did)) { echo json_encode(["status" => "error", "message" => "Device ID Required"]); exit; }

// --- ১. ডিভাইস অটোমেটিক রেজিস্ট্রেশন লজিক ---
// যদি ডিভাইসটি ডাটাবেজে না থাকে, তবে নতুন এন্ট্রি তৈরি হবে (status 0 অর্থাৎ ব্লকড থাকবে)
$stmt_check = $conn->prepare("INSERT IGNORE INTO devices (device_id, status, last_visit) VALUES (?, 0, NOW())");
$stmt_check->bind_param("s", $did);
$stmt_check->execute();

// লাস্ট ভিজিট টাইম আপডেট করা
$conn->prepare("UPDATE devices SET last_visit = NOW() WHERE device_id = ?")->execute([$did]);
// ------------------------------------------

// ২. প্রোফাইল লোড (GET)
if (isset($_GET['get_profile']) && $_GET['get_profile'] == 'true') {
    $stmt = $conn->prepare("SELECT name, fname, mname, addr, mobile, email FROM devices WHERE device_id = ?");
    $stmt->bind_param("s", $did);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc() ?: []);
    exit;
}

// ৩. প্রোফাইল আপডেট (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE devices SET name=?, fname=?, mname=?, addr=?, mobile=?, email=? WHERE device_id=?");
    $stmt->bind_param("sssssss", $_POST['name'], $_POST['fname'], $_POST['mname'], $_POST['addr'], $_POST['mobile'], $_POST['email'], $did);
    echo json_encode($stmt->execute() ? ["status" => "success"] : ["status" => "error"]);
    exit;
}

// ৪. স্ট্যাটাস চেক (ডিভাইস ব্লক না কি অ্যাক্টিভ)
$stmt_status = $conn->prepare("SELECT status FROM devices WHERE device_id = ?");
$stmt_status->bind_param("s", $did);
$stmt_status->execute();
$device = $stmt_status->get_result()->fetch_assoc();

if (!$device || $device['status'] == 0) {
    echo json_encode(["status" => "inactive", "message" => "আপনার ডিভাইসটি ব্লকড। অনুগ্রহ করে সাপোর্টের সাথে যোগাযোগ করুন।"]);
    exit;
}

// ৫. চ্যানেল লিস্ট (শুধুমাত্র অ্যাক্টিভ ডিভাইসের জন্য)
$query = $conn->query("SELECT * FROM channels WHERE status = 1 ORDER BY channel_order ASC");
$channels = [];
while($row = $query->fetch_assoc()){ 
    $channels[] = [
        "name" => $row['channel_name'],
        "url"  => $row['channel_url'],
        // ... আপনার বাকি ফিল্ডগুলো এখানে থাকবে
    ]; 
}
echo json_encode(["status" => "active", "channels" => $channels]);
?>
