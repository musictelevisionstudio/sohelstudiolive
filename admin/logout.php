
<?php
/* File: admin/logout.php - FINAL UPDATED */
session_start();

// মেসেজটি সেশনে সেভ করে রাখা যাতে লগইন পেজে দেখানো যায়
session_start();
$_SESSION['msg'] = "SUCCESS: YOU HAVE BEEN LOGGED OUT SUCCESSFULLY!";

// সমস্ত সেশন ভেরিয়েবল মুছে ফেলা
$_SESSION = array();

// সেশন কুকি ধ্বংস করা
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// সেশনটি পুরোপুরি ধ্বংস করা
session_destroy();

// লগইন পেজে রিডাইরেক্ট করা
header("Location: login.php");
exit();
?>