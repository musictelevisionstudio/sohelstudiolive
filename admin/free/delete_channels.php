<?php
/* File: admin/free/delete_channels.php - FINAL UPDATED */
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
    
    // ডিলিট করার আগে নিশ্চিত হোন যে, এটি ডাটাবেজের সাথে সঠিকভাবে কানেক্টেড
    $stmt = $conn->prepare("DELETE FROM channels WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // ডিলিট সফল হলে সাকসেস মেসেজসহ রিডাইরেক্ট
        header("Location: channels.php?msg=deleted");
    } else {
        // এরর হলে মেসেজসহ রিডাইরেক্ট
        header("Location: channels.php?msg=error");
    }
    $stmt->close();
    exit();
} else {
    // আইডি প্যারামিটার না থাকলে লিস্ট পেজে পাঠিয়ে দিন
    header("Location: channels.php");
    exit();
}
?>

