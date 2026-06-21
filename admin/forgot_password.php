<?php
/* File: admin/forgot_password.php - FINAL UPDATED */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Access - Sohel Premium TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: sans-serif; }
        .card { width: 90%; max-width: 450px; padding: 40px; background: #111; border: 1px solid gold; border-radius: 20px; box-shadow: 0 0 30px rgba(255, 215, 0, 0.15); }
        h2 { color: gold; text-align: center; margin-bottom: 20px; letter-spacing: 1px; }
        p { text-align: center; font-size: 0.95rem; color: #bbb; margin-bottom: 25px; }
        .form-control { background: #222; border: 1px solid #444; color: #fff; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .form-control:focus { background: #222; border-color: gold; color: #fff; box-shadow: none; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; padding: 12px; border-radius: 8px; border: none; font-size: 1.1rem; }
        .btn-gold:hover { background: #ffd700; transform: scale(1.02); transition: 0.3s; }
        .msg-alert { background: #333; color: #ffc107; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 13px; border: 1px solid #ffc107; }
    </style>
</head>
<body>

<div class="card">
    <h2>RECOVER ACCESS</h2>
    
    <!-- সেশন মেসেজ দেখানোর স্থান -->
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <p>Enter your identity and <b>select your preferred OTP method</b> to receive the verification code.</p>
    
    <form action="send_otp_logic.php" method="POST">
        <div class="mb-3">
            <input type="text" name="identity" class="form-control" placeholder="Username, Email, or Phone" required>
        </div>
        
        <div class="mb-4">
            <select name="method" class="form-control" required>
                <option value="" disabled selected>Select OTP Method</option>
                <option value="whatsapp">Send via WhatsApp</option>
                <option value="email">Send via Email</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-gold">SEND OTP</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="login.php" style="color: gold; text-decoration: none; font-weight: bold;">← Back to Login</a>
    </div>
</div>

</body>
</html>
