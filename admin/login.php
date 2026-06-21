<?php
/* File: admin/login.php - FINAL UPDATED */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sohel Premium TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: sans-serif; }
        .login-card { width: 90%; max-width: 450px; padding: 40px; background: #111; border: 1px solid gold; border-radius: 20px; box-shadow: 0 0 30px rgba(255, 215, 0, 0.15); }
        .form-label { color: gold; font-weight: bold; }
        .form-control { background: #222; border: 1px solid #444; color: #fff; padding: 12px; border-radius: 8px; }
        .form-control:focus { background: #222; border-color: gold; color: #fff; box-shadow: none; }
        .btn-gold { background: gold; color: #000; font-weight: bold; padding: 12px; width: 100%; border-radius: 8px; border: none; font-size: 1.1rem; }
        .btn-gold:hover { background: #ffd700; transform: scale(1.02); transition: 0.3s; }
        h2 { color: gold; text-align: center; margin-bottom: 30px; letter-spacing: 2px; }
        .msg-alert { background: #333; color: #ffc107; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 13px; border: 1px solid #ffc107; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>ADMIN LOGIN</h2>
    
    <!-- সেশন মেসেজ দেখানোর স্থান -->
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <div class="mb-4">
            <label class="form-label">Username, Email, or Phone</label>
            <input type="text" name="user_identity" class="form-control" placeholder="Enter your identity" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-gold">LOGIN NOW</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="forgot_password.php" style="color: gold; text-decoration: none; font-size: 0.9rem;">Forgot Password?</a>
    </div>
</div>

</body>
</html>
