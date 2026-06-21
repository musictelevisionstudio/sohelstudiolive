<?php
/* File: admin/free/delete_channels.php - FINAL MASTER VERSION */
session_start();
require_once '../../config/db.php';

// ১. সেশন এবং অ্যাডমিন স্ট্যাটাস যাচাই
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// ২. আইডি যাচাই এবং ডিলিট অপারেশন
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // ডাটাবেস থেকে ডিলিট অপারেশন
    $stmt = $conn->prepare("DELETE FROM channels WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // সফলভাবে ডিলিট হলে সেশন মেসেজ সেট করা
        $_SESSION['msg'] = "SUCCESS: CHANNEL DELETED.";
    } else {
        // এরর হলে সেশন মেসেজ সেট করা
        $_SESSION['msg'] = "ERROR: FAILED TO DELETE CHANNEL.";
    }
    $stmt->close();
}

// ৩. সবশেষে লিস্ট পেজে পাঠিয়ে দেওয়া
header("Location: channels.php");
exit();
?>
