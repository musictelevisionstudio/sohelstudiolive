<?php
/* File: admin/resend_otp.php - FINAL UPDATED & SYNCED */
session_start();
require_once '../config/db.php';
require_once '../config/whatsapp_api.php';
require_once '../config/phpmailer_config.php';

// ১. সিকিউরিটি চেক: যদি সেশনে ইউজার আইডি না থাকে
if (!isset($_SESSION['reset_admin_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$user_id = $_SESSION['reset_admin_id'];
$new_otp = rand(100000, 999999);

// ২. ডাটাবেসে নতুন OTP আপডেট করা
$stmt = $conn->prepare("UPDATE admins SET otp_verification = ? WHERE id = ?");
$stmt->bind_param("ii", $new_otp, $user_id);

if ($stmt->execute()) {
    // ইউজারের তথ্য আনা
    $query = $conn->prepare("SELECT email, phone_number FROM admins WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $user = $query->get_result()->fetch_assoc();

    $method = $_SESSION['reset_method'] ?? 'email'; 

    if ($method === 'whatsapp') {
        // হোয়াটসঅ্যাপ নাম্বার ফরম্যাটিং (আপনার ডাটাবেস অনুযায়ী)
        $clean_phone = preg_replace('/^0/', '', $user['phone_number']);
        if (strlen($clean_phone) == 10) { $clean_phone = '880' . $clean_phone; }
        
        $whatsapp_msg = "আপনার নতুন ওটিপি কোডটি হলো: " . $new_otp;
        sendWhatsApp($clean_phone, $whatsapp_msg);
    } else {
        // ইমেইল পাঠানো
        $subject = "Resend OTP - Sohel Premium TV";
        $body = "আপনার নতুন ওটিপি কোডটি হলো: <b>" . $new_otp . "</b>";
        sendMail($user['email'], $subject, $body);
    }

    // ৩. সেশন মেসেজ সেট করা
    $_SESSION['msg'] = "SUCCESS: NEW OTP HAS BEEN SENT SUCCESSFULLY!";
} else {
    $_SESSION['msg'] = "ERROR: FAILED TO RESEND OTP.";
}

// ৪. ভেরিফিকেশন পেজে ফেরত পাঠানো
header("Location: verify_otp.php");
exit();
?>
