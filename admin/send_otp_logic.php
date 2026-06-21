
<?php
/* File: admin/send_otp_logic.php - FINAL UPDATED & SYNCED */
session_start();
require_once '../config/db.php';
require_once '../config/whatsapp_api.php'; 
require_once '../config/phpmailer_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = $_POST['identity'];
    $method   = $_POST['method'];

    $stmt = $conn->prepare("SELECT id, email, phone_number FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        $otp = rand(100000, 999999);
        
        $update = $conn->prepare("UPDATE admins SET otp_verification = ? WHERE id = ?");
        $update->bind_param("ii", $otp, $admin['id']);
        $update->execute();

        if ($method === 'whatsapp') {
            $raw_phone = $admin['phone_number'];
            $clean_phone = preg_replace('/^0/', '', $raw_phone); 
            if (strlen($clean_phone) == 10) { 
                $clean_phone = '880' . $clean_phone; 
            }
            
            $whatsapp_result = sendWhatsApp($clean_phone, "আপনার ওটিপি কোডটি হলো: " . $otp);
            
            if ($whatsapp_result['status'] === 'error') {
                $_SESSION['msg'] = "ERROR: " . $whatsapp_result['message'];
                header("Location: forgot_password.php");
                exit();
            }
            
        } elseif ($method === 'email') {
            $subject = "OTP Verification - Sohel Premium TV";
            $body = "আপনার ওটিপি কোডটি হলো: " . $otp;
            
            if (!sendMail($admin['email'], $subject, $body)) {
                $_SESSION['msg'] = "ERROR: FAILED TO SEND EMAIL.";
                header("Location: forgot_password.php");
                exit();
            }
        }

        $_SESSION['reset_admin_id'] = $admin['id'];
        $_SESSION['reset_method']   = $method; 
        
        $_SESSION['msg'] = "SUCCESS: OTP SENT TO YOUR " . strtoupper($method) . "!";
        
        header("Location: verify_otp.php");
        exit();
    } else {
        $_SESSION['msg'] = "ERROR: USER NOT FOUND!";
        header("Location: forgot_password.php");
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
?>