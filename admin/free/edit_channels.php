<?php
/* File: admin/free/edit_channels.php - ALL COLUMNS INCLUDED */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);
$msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ডাটাবেস এক্সপোর্টের সবগুলো কলাম এখানে আপডেট করা হবে
    $update = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, channel_order=?, ticker_text=?, ticker_enabled=?, ticker_speed=?, live_text=?, live_animation=?, ad_url=?, ad_enabled=?, ad_duration=?, ads_status=?, status=? WHERE id=?");
    
    $update->bind_param("ssisisiisiiisi", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['channel_order'], 
        $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], 
        $_POST['live_text'], $_POST['live_animation'], 
        $_POST['ad_url'], $_POST['ad_enabled'], $_POST['ad_duration'], $_POST['ads_status'], 
        $_POST['status'], $id
    );
    
    if ($update->execute()) {
        $msg = "<div class='alert alert-success text-center'>সফলভাবে আপডেট হয়েছে!</div>";
    } else {
        $msg = "<div class='alert alert-danger text-center'>আপডেট ব্যর্থ: " . $conn->error . "</div>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; margin: 0; padding: 10px; }
        .edit-container { width: 100%; max-width: 600px; margin: auto; border: 2px solid gold; padding: 15px; border-radius: 15px; background: #111; }
        .form-control, .form-select { background: #222 !important; border: 1px solid #555 !important; color: #fff !important; margin-bottom: 8px; }
        label { color: gold; font-weight: bold; font-size: 0.8rem; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="channels.php" class="text-warning">← Back</a>
    <h4 class="text-warning text-center">EDIT CHANNEL</h4>
    <?php echo $msg; ?>
    <form method="POST">
        <label>Channel Name</label><input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name'] ?? ''); ?>">
        <label>URL</label><input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url'] ?? ''); ?>">
        
        <div class="row">
            <div class="col-4"><label>Order</label><input type="number" name="channel_order" class="form-control" value="<?php echo $channel['channel_order'] ?? 0; ?>"></div>
            <div class="col-4"><label>Ticker Speed</label><input type="number" name="ticker_speed" class="form-control" value="<?php echo $channel['ticker_speed'] ?? 50; ?>"></div>
            <div class="col-4"><label>Ad Duration</label><input type="number" name="ad_duration" class="form-control" value="<?php echo $channel['ad_duration'] ?? 30; ?>"></div>
        </div>

        <label>Ticker Text</label><input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>">
        <label>Ad URL</label><input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url'] ?? ''); ?>">

        <div class="row">
            <div class="col-6"><label>Ticker Status</label><select name="ticker_enabled" class="form-select"><option value="1" <?php if(($channel['ticker_enabled']??'')==1) echo 'selected';?>>Show</option><option value="0" <?php if(($channel['ticker_enabled']??'')==0) echo 'selected';?>>Hide</option></select></div>
            <div class="col-6"><label>Ad Enabled</label><select name="ad_enabled" class="form-select"><option value="1" <?php if(($channel['ad_enabled']??'')==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['ad_enabled']??'')==0) echo 'selected';?>>Inactive</option></select></div>
        </div>

        <div class="row">
            <div class="col-6"><label>Ads Status</label><select name="ads_status" class="form-select"><option value="1" <?php if(($channel['ads_status']??'')==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['ads_status']??'')==0) echo 'selected';?>>Inactive</option></select></div>
            <div class="col-6"><label>Status</label><select name="status" class="form-select"><option value="1" <?php if(($channel['status']??'')==1) echo 'selected';?>>Active</option><option value="0" <?php if(($channel['status']??'')==0) echo 'selected';?>>Inactive</option></select></div>
        </div>

        <button type="submit" class="btn btn-gold py-2 mt-2">UPDATE CHANNEL</button>
    </form>
</div>
</body>
</html>
