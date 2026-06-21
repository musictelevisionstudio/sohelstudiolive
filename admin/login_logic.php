
<?php
/* File: admin/login_logic.php - FINAL UPDATED & SYNCED */
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ডাটাবেস থেকে ইউজার চেক করা
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // পাসওয়ার্ড যাচাইকরণ (এখানে সরাসরি মিলানো হচ্ছে)
        if ($password == $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            
            // সাকসেস মেসেজ দিয়ে ড্যাশবোর্ডে পাঠানো
            $_SESSION['msg'] = "WELCOME BACK, ADMIN!";
            header("Location: dashboard.php");
            exit();
        } else {
            // ভুল পাসওয়ার্ডের জন্য মেসেজ
            $_SESSION['msg'] = "ERROR: INVALID PASSWORD!";
            header("Location: login.php");
            exit();
        }
    } else {
        // ইউজার না পাওয়ার জন্য মেসেজ
        $_SESSION['msg'] = "ERROR: USER NOT FOUND!";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>