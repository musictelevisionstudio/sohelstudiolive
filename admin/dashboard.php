<?php
/* File: admin/dashboard.php */
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; min-height: 100vh; padding-bottom: 20px; }
        .profile-area { text-align: center; padding: 20px; border-bottom: 1px solid #333; margin-bottom: 10px; }
        .avatar { font-size: 70px; color: gold; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; padding: 0 20px; font-size: 13px; }
        .info-item { background: #111; padding: 8px; border-radius: 5px; border: 1px solid #222; }
        .label { color: #888; display: block; font-size: 10px; text-transform: uppercase; }
        .value { color: #fff; font-weight: bold; }
        .status-active { color: #0f0; }
        
        .title-border { border: 1px solid gold; color: gold; padding: 10px; text-align: center; margin: 20px auto; width: 90%; border-radius: 8px; font-weight: bold; }
        .menu-box { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding: 0 20px; }
        .btn-menu { border: 1px solid gold; color: gold; padding: 12px; font-weight: bold; background: #111; border-radius: 8px; text-decoration: none; text-align: center; font-size: 13px; }
        .btn-menu:hover { background: gold; color: #000; }
        
        .logout-box { border: 1px solid white; color: white; padding: 12px; text-align: center; margin: 30px 20px; border-radius: 8px; text-decoration: none; display: block; font-weight: bold; }
        .logout-box:hover { background: red; color: #fff; border-color: red; }
    </style>
</head>
<body>

    <div class="profile-area">
        <div class="avatar"><i class="fas fa-user-circle"></i></div>
        <h4 style="color: gold; margin-bottom: 15px;"><?php echo htmlspecialchars($admin['full_name']); ?></h4>
        
        <div class="info-grid">
            <div class="info-item"><span class="label">Username</span><span class="value"><?php echo htmlspecialchars($admin['username']); ?></span></div>
            <div class="info-item"><span class="label">Status</span><span class="value status-active"><?php echo ucfirst($admin['account_status']); ?></span></div>
            <div class="info-item" style="grid-column: span 2;"><span class="label">Email</span><span class="value"><?php echo htmlspecialchars($admin['email']); ?></span></div>
            <div class="info-item" style="grid-column: span 2;"><span class="label">Phone</span><span class="value"><?php echo htmlspecialchars($admin['country_code'] . ' ' . $admin['phone_number']); ?></span></div>
        </div>
    </div>

    <div class="title-border">DASHBOARD MENU</div>

    <div class="menu-box">
        <a href="free/channels.php" class="btn-menu">FREE CHANNELS</a>
        <a href="profile.php" class="btn-menu">PROFILE</a>
        <a href="settings.php" class="btn-menu">SETTINGS</a>
        <a href="licence.php" class="btn-menu">DEVICE LICENCE</a>
    </div>

    <a href="logout.php" class="logout-box">LOG OUT</a>

</body>
</html>
