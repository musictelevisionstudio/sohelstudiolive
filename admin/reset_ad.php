<?php
/* File: admin/reset_ad.php */
session_start();
require_once '../config/db.php';

// ১. চেক করুন ইউজার লগইন করা কি না (নিরাপত্তার জন্য জরুরি)
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// ২. ডায়নামিক আপডেট
$stmt = $conn->prepare("UPDATE site_settings SET force_ad_status = 0 WHERE id = 1");

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Ad has been force-stopped.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
}
?>

