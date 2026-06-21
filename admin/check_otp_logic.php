
<?php
/* File: admin/check_otp_logic.php - FINAL UPDATED */
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['reset_admin_id'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];
    $admin_id = $_SESSION['reset_admin_id'];

    // ১. ওটিপি যাচাইকরণ
    $stmt = $conn->prepare("SELECT otp_verification FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['otp_verification'] == $user_otp) {
        // ওটিপি সঠিক হলে
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        // ভুল ওটিপি হলে সেশন মেসেজ সেট করা
        $_SESSION['msg'] = "ERROR: INVALID OTP! PLEASE TRY AGAIN.";
        header("Location: verify_otp.php");
        exit();
    }
} else {
    header("Location: verify_otp.php");
    exit();
}
?>