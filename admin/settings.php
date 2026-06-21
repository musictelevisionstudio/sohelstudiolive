<?php
/* File: admin/settings.php - FULLY DETAILED & UPDATED */
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// ২. আপডেট হ্যান্ডলিং
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_status = isset($_POST['force_ad']) ? 1 : 0;
    $notice = $_POST['app_notice'];
    $whatsapp = $_POST['whatsapp'];

    $stmt = $conn->prepare("UPDATE site_settings SET force_ad_status=?, app_notice=?, admin_whatsapp=?, updated_at=CURRENT_TIMESTAMP WHERE id=1");
    $stmt->bind_param("iss", $ad_status, $notice, $whatsapp);
    
    if ($stmt->execute()) {
        $_SESSION['msg'] = "SUCCESS: SETTINGS UPDATED SUCCESSFULLY!";
    } else {
        $_SESSION['msg'] = "ERROR: FAILED TO UPDATE SETTINGS.";
    }
    
    // রিডাইরেক্ট করে মেসেজ দেখানো
    header("Location: settings.php");
    exit();
}

// ৩. ডাটা ফেচিং
$settings = $conn->query("SELECT * FROM site_settings WHERE id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; }
        .full-container { max-width: 800px; margin: 20px auto; padding: 40px; background: #111; border: 2px solid gold; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); }
        h2 { color: gold; text-align: center; margin-bottom: 30px; border-bottom: 2px solid gold; padding-bottom: 10px; }
        .form-label { color: gold; font-size: 16px; font-weight: bold; }
        .form-control { background: #222 !important; color: #fff !important; border: 1px solid #444 !important; padding: 12px; }
        .btn-warning { font-size: 18px; font-weight: bold; padding: 12px; }
        .msg-alert { background: gold; color: #000; font-weight: bold; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container full-container">
    <h2>SITE SETTINGS</h2>
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-4">
            <label class="form-label">Force Ad Status (On/Off)</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="force_ad" style="transform: scale(1.5);" <?php echo ($settings && $settings['force_ad_status'] == 1) ? 'checked' : ''; ?>>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label">App Notice (Marquee Text)</label>
            <textarea name="app_notice" class="form-control" rows="5"><?php echo htmlspecialchars($settings ? $settings['app_notice'] : ''); ?></textarea>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Admin WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" value="<?php echo htmlspecialchars($settings ? $settings['admin_whatsapp'] : ''); ?>">
        </div>
        
        <button type="submit" class="btn btn-warning w-100">SAVE SETTINGS</button>
        <a href="dashboard.php" class="btn btn-outline-light w-100 mt-3">BACK TO DASHBOARD</a>
    </form>
</div>

</body>
</html>
