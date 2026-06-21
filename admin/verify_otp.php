<?php
/* File: admin/verify_otp.php - FINAL UPDATED */
session_start();
// মেথড চেক করা
$method = isset($_SESSION['reset_method']) ? ucfirst($_SESSION['reset_method']) : 'WhatsApp/Email';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verify OTP - Sohel Premium TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { height: 100%; margin: 0; display: flex; align-items: center; justify-content: center; background: #000; color: #fff; font-family: sans-serif; overflow: hidden; }
        .card { width: 90%; max-width: 400px; padding: 30px; background: #111; border: 1px solid gold; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); }
        .form-control { background: #222; border: 1px solid #444; color: #fff; text-align: center; letter-spacing: 5px; font-size: 1.5rem; padding: 10px; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; margin-top: 10px; padding: 12px; }
        h2 { color: gold; text-align: center; margin-bottom: 20px; font-size: 1.5rem; }
        p { text-align: center; font-size: 0.9rem; color: #ccc; }
        .msg-alert { background: #333; color: #ffc107; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 13px; border: 1px solid #ffc107; }
    </style>
</head>
<body>

<div class="card">
    <h2>VERIFY OTP</h2>
    
    <!-- সেশন মেসেজ দেখানোর স্থান -->
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <p>Enter the 6-digit code sent to your <b><?php echo $method; ?></b>.</p>
    <form action="check_otp_logic.php" method="POST">
        <div class="mb-3">
            <input type="text" name="otp" class="form-control" maxlength="6" placeholder="000000" required>
        </div>
        <button type="submit" class="btn btn-gold">VERIFY CODE</button>
    </form>
    <div class="text-center mt-3">
        <p>Didn't receive the code? <a href="resend_otp.php" style="color: gold; text-decoration: none;">Resend OTP</a></p>
    </div>
</div>

</body>
</html>
