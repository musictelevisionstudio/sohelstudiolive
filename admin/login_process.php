
<?php
/* File: admin/login_process.php - FINAL UPDATED & SYNCED */
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = $_POST['user_identity'];
    $password = $_POST['password'];

    // ডাটাবেস থেকে ইউজার চেক করা (username, email, phone_number সবগুলো দিয়ে সার্চ করার ক্ষমতা)
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // সরাসরি পাসওয়ার্ড মিলিয়ে দেখা
        if ($password == $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_logged_in'] = true;
            
            // সফল হলে ড্যাশবোর্ডে রিডাইরেক্ট
            header("Location: dashboard.php");
            exit();
        } else {
            // ভুল পাসওয়ার্ডের ক্ষেত্রে সেশন মেসেজ
            $_SESSION['msg'] = "ERROR: INVALID PASSWORD!";
            header("Location: login.php");
            exit();
        }
    } else {
        // ইউজার না পাওয়ার ক্ষেত্রে সেশন মেসেজ
        $_SESSION['msg'] = "ERROR: USER NOT FOUND!";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>