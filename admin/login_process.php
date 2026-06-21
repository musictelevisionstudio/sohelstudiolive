<?php
ob_start(); 
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = mysqli_real_escape_string($conn, $_POST['user_identity']);
    $password = $_POST['password']; // পাসওয়ার্ড সরাসরি তুলনা করার জন্য এস্কেপ করার প্রয়োজন নেই

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if ($password == $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_logged_in'] = true;
            
            // হেডার পাঠানোর আগে সব বাফার ক্লিয়ার করা
            ob_clean(); 
            header("Location: dashboard.php");
            exit();
        } else {
            // ভুল পাসওয়ার্ড হলে এরর মেসেজ
            echo "<script>alert('Invalid Password!'); window.location.href='login.php';</script>";
        }
    } else {
        // ইউজার না পেলে এরর মেসেজ
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
    }
} else {
    // সরাসরি ফাইলটি অ্যাক্সেস করলে লগইন পেজে পাঠিয়ে দেওয়া
    header("Location: login.php");
}
ob_end_flush();
?>
