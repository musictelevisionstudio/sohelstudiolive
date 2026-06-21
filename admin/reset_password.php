
<?php
/* File: admin/reset_password.php - FINAL UPDATED */
session_start();

// শুধুমাত্র ওটিপি ভেরিফাই হওয়া ইউজারই এখানে আসতে পারবে
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - Sohel Premium TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { height: 100%; margin: 0; display: flex; align-items: center; justify-content: center; background: #000; color: #fff; font-family: sans-serif; overflow: hidden; }
        .card { width: 90%; max-width: 400px; padding: 30px; background: #111; border: 1px solid gold; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); }
        .form-control { background: #222; border: 1px solid #444; color: #fff; padding: 12px; }
        .form-control:focus { background: #222; color: #fff; border-color: gold; box-shadow: none; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; margin-top: 10px; padding: 12px; }
        .btn-gold:hover { background: #ffd700; }
        h2 { color: gold; text-align: center; margin-bottom: 20px; font-size: 1.5rem; }
        .msg-alert { background: #333; color: #ff4444; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 12px; border: 1px solid #ff4444; }
    </style>
</head>
<body>

<div class="card">
    <h2>NEW PASSWORD</h2>
    
    <!-- সেশন মেসেজ দেখানোর স্থান -->
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <form action="update_password_logic.php" method="POST">
        <div class="mb-3">
            <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
        </div>
        <div class="mb-3">
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn btn-gold">UPDATE PASSWORD</button>
    </form>
</div>

</body>
</html>