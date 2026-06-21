<?php
/* File: admin/login_process.php - FINAL CLEAN VERSION */
session_start();

// হেডার পাঠানোর আগে কোনো আউটপুট যেন না থাকে
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ডাটাবেস থেকে ইউজার চেক
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $username, $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // পাসওয়ার্ড যাচাই (এখানে আপনার ডাটাবেসের পাসওয়ার্ড চেক)
        if ($password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['msg'] = "ERROR: INVALID PASSWORD!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['msg'] = "ERROR: USER NOT FOUND!";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
