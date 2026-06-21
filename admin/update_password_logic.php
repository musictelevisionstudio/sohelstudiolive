<?php
/* File: admin/update_password_logic.php */
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true || !isset($_SESSION['reset_admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['reset_admin_id'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='reset_password.php';</script>";
        exit();
    }

    // সরাসরি পাসওয়ার্ড সেভ হবে (হ্যাশ করা হবে না)
    $stmt = $conn->prepare("UPDATE admins SET password = ?, otp_verification = NULL WHERE id = ?");
    $stmt->bind_param("si", $new_password, $admin_id);
    
    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        echo "<script>alert('Password updated successfully!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update password.'); window.location.href='reset_password.php';</script>";
        exit();
    }
} else {
    header("Location: reset_password.php");
    exit();
}
?>

