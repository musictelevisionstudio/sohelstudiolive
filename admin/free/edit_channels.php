<?php
/* File: admin/free/edit_channels.php - FULL FUNCTIONAL VERSION */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);
$msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // এখানে সব ফাংশন ও কলামের আপডেট কুয়েরি দেওয়া হলো
    $update = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, channel_order=?, ticker_text=?, ticker_enabled=?, ticker_speed=?, ticker_direction=?, live_text=?, live_animation=?, live_enabled=?, ad_url=?, ad_duration=?, ad_type=?, ad_size=?, ad_enabled=?, ads_status=?, status=? WHERE id=?");
    
    $update->bind_param("ssisisiisssisssssi", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['channel_order'], 
        $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], $_POST['ticker_direction'],
        $_POST['live_text'], $_POST['live_animation'], $_POST['live_enabled'], 
        $_POST['ad_url'], $_POST['ad_duration'], $_POST['ad_type'], $_POST['ad_size'], $_POST['ad_enabled'], $_POST['ads_status'], 
        $_POST['status'], $id
    );
    
    if ($update->execute()) { $msg = "<div class='alert alert-success text-center'>সফলভাবে আপডেট হয়েছে!</div>"; }
    else { $msg = "<div class='alert alert-danger text-center'>এরর: " . $conn->error . "</div>"; }
}

$stmt = $conn->prepare("SELECT * FROM channels WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$channel = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; margin: 0; padding: 10px; }
        .edit-container { width: 100%; border: 2px solid gold; padding: 20px; background: #111; border-radius: 10px; }
        .form-control, .form-select { background: #222 !important; border: 1px solid #555 !important; color: #fff !important; }
        label { color: gold; font-weight: bold; margin-top: 10px; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; font-size: 20px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="channels.php" class="text-warning">← ব্যাক</a>
    <h3 class="text-center text-warning">সম্পূর্ণ কন্ট্রোল প্যানেল</h3>
    <?php echo $msg; ?>
    <form method="POST">
        <label>চ্যানেল নাম</label><input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name'] ?? ''); ?>">
        
        <label>লাইভ টেক্সট ও অ্যানিমেশন</label>
        <div class="row">
            <div class="col-6"><input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text'] ?? ''); ?>"></div>
            <div class="col-6"><select name="live_animation" class="form-select">
                <option value="pulse" <?php if(($channel['live_animation']??'')=='pulse') echo 'selected';?>>Pulse</option>
                <option value="blink" <?php if(($channel['live_animation']??'')=='blink') echo 'selected';?>>Blink</option>
            </select></div>
        </div>

        <label>হেডলাইন (Ticker) সেটিংস</label>
        <div class="row">
            <div class="col-4"><select name="ticker_speed" class="form-select"><?php for($i=10; $i<=100; $i+=10) echo "<option value='$i' ".((($channel['ticker_speed']??50)==$i)?'selected':'').">$i% Speed</option>"; ?></select></div>
            <div class="col-4"><select name="ticker_direction" class="form-select"><option value="left" <?php if(($channel['ticker_direction']??'')=='left') echo 'selected';?>>বাম দিকে</option><option value="right" <?php if(($channel['ticker_direction']??'')=='right') echo 'selected';?>>ডান দিকে</option></select></div>
            <div class="col-4"><select name="ticker_enabled" class="form-select"><option value="1">Show</option><option value="0">Hide</option></select></div>
        </div>
        <input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>">

        <label>বিজ্ঞাপন (Ad) সেটিংস</label>
        <div class="row">
            <div class="col-6"><select name="ad_type" class="form-select"><option value="short" <?php if(($channel['ad_type']??'')=='short') echo 'selected';?>>Short Video</option><option value="long" <?php if(($channel['ad_type']??'')=='long') echo 'selected';?>>Long Video</option></select></div>
            <div class="col-6"><select name="ad_size" class="form-select"><?php for($i=20; $i<=100; $i+=10) echo "<option value='$i' ".((($channel['ad_size']??100)==$i)?'selected':'').">$i% Size</option>"; ?></select></div>
        </div>
        <input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url'] ?? ''); ?>">

        <button type="submit" class="btn btn-gold">সেভ করুন</button>
    </form>
</div>
</body>
</html>
