<?php
/* File: admin/send_otp_logic.php */
session_start();
require_once '../config/db.php';
require_once '../config/whatsapp_api.php'; 
require_once '../config/phpmailer_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = $_POST['identity'];
    $method   = $_POST['method'];

    // ডাটাবেস থেকে ইউজার খুঁজে বের করা
    $stmt = $conn->prepare("SELECT id, email, phone_number FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // ৬ ডিজিটের ওটিপি তৈরি
        $otp = rand(100000, 999999);
        
        // ডাটাবেসে ওটিপি আপডেট করা
        $update = $conn->prepare("UPDATE admins SET otp_verification = ? WHERE id = ?");
        $update->bind_param("ii", $otp, $admin['id']);
        $update->execute();

        // ওটিপি পাঠানো
        if ($method === 'whatsapp') {
            $raw_phone = $admin['phone_number'];
            $clean_phone = preg_replace('/^0/', '', $raw_phone); 
            if (strlen($clean_phone) == 10) { 
                $clean_phone = '880' . $clean_phone; 
            }
            sendWhatsApp($clean_phone, "আপনার ওটিপি কোডটি হলো: " . $otp);
            
        } elseif ($method === 'email') {
            $subject = "OTP Verification - Sohel Premium TV";
            $body = "আপনার ওটিপি কোডটি হলো: " . $otp;
            sendMail($admin['email'], $subject, $body);
        }

        $_SESSION['reset_admin_id'] = $admin['id'];
        $_SESSION['reset_method']   = $method; 
        
        header("Location: verify_otp.php");
        exit();
    } else {
        echo "<script>alert('User not found!'); window.location.href='forgot_password.php';</script>";
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
?>

