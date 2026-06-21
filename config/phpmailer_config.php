<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer এর পাথ ঠিক আছে কি না নিশ্চিত করুন
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // সার্ভার সেটিংস
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'musictelevisionstudio@gmail.com';
        $mail->Password   = 'zaar oaop idty oyvj'; // আপনার অ্যাপ পাসওয়ার্ড
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;
        
        // ইনফিনিটি ফ্রি বা ফ্রি হোস্টিংয়ের জন্য SSL অপশন (এটি কানেকশন ফেইলর রোধ করে)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // টাইমআউট এবং এনকোডিং
        $mail->Timeout    = 30; // কিছুটা বাড়িয়ে দেওয়া হলো
        $mail->CharSet    = PHPMailer::CHARSET_UTF8;
        $mail->Encoding   = 'base64'; 

        // প্রেরক এবং প্রাপক
        $mail->setFrom('musictelevisionstudio@gmail.com', 'Sohel Premium TV');
        $mail->addAddress($to);
        $mail->addReplyTo('musictelevisionstudio@gmail.com', 'Sohel Premium TV');

        // কন্টেন্ট
        $mail->isHTML(true);
        $mail->Subject    = "=?UTF-8?B?" . base64_encode($subject) . "?="; // সাবজেক্টেও ইউনিকোড সাপোর্ট
        $mail->Body       = $body;
        $mail->AltBody    = strip_tags($body); 
        
        return $mail->send();
    } catch (Exception $e) {
        // এরর লগ ফাইলে লিখুন যাতে আপনি পরে দেখতে পারেন কেন মেইল যাচ্ছে না
        error_log("PHPMailer Error to $to: " . $mail->ErrorInfo);
        return false;
    }
}
?>

