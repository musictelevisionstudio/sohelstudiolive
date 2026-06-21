<?php
/* File: admin/login_process.php */
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // এখানে login.php এর ইনপুট নাম অনুযায়ী পরিবর্তন করা হয়েছে
    $identity = $_POST['user_identity'];
    $password = $_POST['password'];

    // ডাটাবেস থেকে ইউজার চেক করা
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // সরাসরি পাসওয়ার্ড মিলিয়ে দেখা (হ্যাশ ছাড়া)
        if ($password == $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_logged_in'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

