<?php
/* File: admin/logout.php */
session_start();

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
