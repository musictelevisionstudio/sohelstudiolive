
<?php
/* File: config/phpmailer_config.php - FINAL REVIEWED */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'musictelevisionstudio@gmail.com';
        $mail->Password   = 'zaar oaop idty oyvj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Timeout    = 30;
        $mail->CharSet    = 'UTF-8'; // এটি এভাবেও রাখা যায়

        $mail->setFrom('musictelevisionstudio@gmail.com', 'Sohel Premium TV');
        $mail->addAddress($to);
        $mail->addReplyTo('musictelevisionstudio@gmail.com', 'Sohel Premium TV');

        $mail->isHTML(true);
        $mail->Subject    = $subject; // PHPMailer অটোমেটিক UTF-8 হ্যান্ডেল করে যদি CharSet সেট থাকে
        $mail->Body       = $body;
        $mail->AltBody    = strip_tags($body); 
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>