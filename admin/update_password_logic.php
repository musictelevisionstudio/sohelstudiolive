<?php
/* File: admin/update_password_logic.php - SECURE VERSION */
session_start();
require_once '../config/db.php';

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true || !isset($_SESSION['reset_admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['reset_admin_id'];

    // ২. ভ্যালিডেশন
    if ($new_password !== $confirm_password) {
        $_SESSION['msg'] = "ERROR: PASSWORDS DO NOT MATCH!";
        header("Location: reset_password.php");
        exit();
    }

    // নিরাপত্তা: পাসওয়ার্ড হ্যাশ করে ডাটাবেসে সেভ করা (এটি অত্যন্ত জরুরি)
    // যদি আপনার সিস্টেমের পুরনো পাসওয়ার্ডগুলো হ্যাশ করা না থাকে, তবে সাবধান। 
    // যদি আপনার লগইন ফাইলে password_verify ব্যবহার করা থাকে, তবেই এটি হ্যাশ করবেন।
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admins SET password = ?, otp_verification = NULL WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $admin_id);
    
    if ($stmt->execute()) {
        // সব সেশন মুছে ফেলা
        session_unset();
        session_destroy();
        
        // লগইন পেজে রিডাইরেক্ট করে মেসেজ দেখানো
        session_start();
        $_SESSION['msg'] = "SUCCESS: PASSWORD UPDATED SUCCESSFULLY! PLEASE LOGIN.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['msg'] = "ERROR: FAILED TO UPDATE PASSWORD.";
        header("Location: reset_password.php");
        exit();
    }
} else {
    header("Location: reset_password.php");
    exit();
}
?>
