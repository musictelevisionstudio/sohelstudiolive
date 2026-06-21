<?php
/* File: admin/free/edit_channels.php - FINAL VERSION */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);
$msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, channel_order=?, ticker_text=?, ticker_enabled=?, ticker_speed=?, live_text=?, live_animation=?, live_enabled=?, ad_url=?, ad_duration=?, ad_enabled=?, ads_status=?, status=? WHERE id=?");
    
    $update->bind_param("ssisisiisiiisii", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['channel_order'], 
        $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], 
        $_POST['live_text'], $_POST['live_animation'], $_POST['live_enabled'], 
        $_POST['ad_url'], $_POST['ad_duration'], $_POST['ad_enabled'], $_POST['ads_status'], 
        $_POST['status'], $id
    );
    
    if ($update->execute()) {
        $msg = "<div class='alert alert-success'>সফলভাবে আপডেট হয়েছে!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>এরর: " . $conn->error . "</div>";
    }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .edit-container { width: 100%; height: 100vh; border: 2px solid gold; padding: 15px; background: #111; overflow-y: auto; }
        .form-control, .form-select { background: #222 !important; border: 1px solid #555 !important; color: #fff !important; margin-bottom: 5px; }
        label { color: gold; font-weight: bold; font-size: 0.8rem; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; font-size: 1.2rem; margin-top: 10px; }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="channels.php" class="text-warning text-decoration-none">← ব্যাক</a>
    <h3 class="text-warning text-center">EDIT CHANNEL</h3>
    <?php echo $msg; ?>
    <form method="POST">
        <label>Name</label><input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name'] ?? ''); ?>">
        <label>URL</label><input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url'] ?? ''); ?>">
        <div class="row">
            <div class="col-6"><label>Order</label><input type="number" name="channel_order" class="form-control" value="<?php echo $channel['channel_order'] ?? 0; ?>"></div>
            <div class="col-6"><label>Speed</label><input type="number" name="ticker_speed" class="form-control" value="<?php echo $channel['ticker_speed'] ?? 50; ?>"></div>
        </div>
        <label>Live Text</label><input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text'] ?? ''); ?>">
        <div class="row">
            <div class="col-6"><label>Animation</label><select name="live_animation" class="form-select"><option value="pulse" <?php if(($channel['live_animation']??'')=='pulse') echo 'selected';?>>Pulse</option><option value="blink" <?php if(($channel['live_animation']??'')=='blink') echo 'selected';?>>Blink</option></select></div>
            <div class="col-6"><label>Live Status</label><select name="live_enabled" class="form-select"><option value="1" <?php if(($channel['live_enabled']??0)==1) echo 'selected';?>>Show</option><option value="0" <?php if(($channel['live_enabled']??0)==0) echo 'selected';?>>Hide</option></select></div>
        </div>
        <label>Ticker Text</label><input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>">
        <label>Ticker Status</label><select name="ticker_enabled" class="form-select"><option value="1" <?php if(($channel['ticker_enabled']??0)==1) echo 'selected';?>>Show</option><option value="0" <?php if(($channel['ticker_enabled']??0)==0) echo 'selected';?>>Hide</option></select>
        <label>Ad URL</label><input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url'] ?? ''); ?>">
        <div class="row">
            <div class="col-4"><label>Duration</label><input type="number" name="ad_duration" class="form-control" value="<?php echo $channel['ad_duration'] ?? 30; ?>"></div>
            <div class="col-4"><label>Ad Status</label><select name="ad_enabled" class="form-select"><option value="1" <?php if(($channel['ad_enabled']??0)==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['ad_enabled']??0)==0) echo 'selected';?>>Inactive</option></select></div>
            <div class="col-4"><label>Ads Status</label><select name="ads_status" class="form-select"><option value="1" <?php if(($channel['ads_status']??0)==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['ads_status']??0)==0) echo 'selected';?>>Inactive</option></select></div>
        </div>
        <label>Status</label><select name="status" class="form-select"><option value="1" <?php if(($channel['status']??0)==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['status']??0)==0) echo 'selected';?>>Inactive</option></select>
        <button type="submit" class="btn btn-gold">UPDATE CHANNEL</button>
    </form>
</div>
</body>
</html>
