<?php
/* File: admin/free/edit_channels.php - SUCCESS MESSAGE INTEGRATED */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);
$msg = ""; // মেসেজ ভেরিয়েবল

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, channel_order=?, ticker_text=?, ticker_enabled=?, ticker_speed=?, live_text=?, live_animation=?, live_enabled=?, ad_url=?, ad_duration=?, ad_enabled=?, ad_type=?, status=? WHERE id=?");
    
    $update->bind_param("ssisisiisiiisii", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['channel_order'], 
        $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], 
        $_POST['live_text'], $_POST['live_animation'], $_POST['live_enabled'], 
        $_POST['ad_url'], $_POST['ad_duration'], $_POST['ad_enabled'], $_POST['ad_type'], 
        $_POST['status'], $id
    );
    
    if ($update->execute()) {
        $msg = "<div class='alert alert-success text-center'>চ্যানেল সফলভাবে আপডেট হয়েছে!</div>";
    } else {
        $msg = "<div class='alert alert-danger text-center'>আপডেট ব্যর্থ হয়েছে!</div>";
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
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; padding: 20px; }
        .edit-container { max-width: 600px; margin: auto; border: 3px solid gold; padding: 25px; border-radius: 20px; background: #111; }
        .form-control, .form-select { background: #222 !important; border: 1px solid #555 !important; color: #fff !important; margin-bottom: 15px; }
        label { color: gold; font-weight: bold; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; font-size: 20px; }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="channels.php" class="text-warning">← Back</a>
    <h3 class="text-warning text-center mb-4">EDIT CHANNEL</h3>
    
    <?php echo $msg; ?>

    <form method="POST">
        <div class="row">
            <div class="col-md-6"><label>Name</label><input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name']); ?>" required></div>
            <div class="col-md-6"><label>Order</label><input type="number" name="channel_order" class="form-control" value="<?php echo $channel['channel_order']; ?>"></div>
        </div>
        <label>URL</label><input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url']); ?>">
        
        <div class="row">
            <div class="col-md-4"><label>Live Text</label><input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text']); ?>"></div>
            <div class="col-md-4"><label>Animation</label><select name="live_animation" class="form-select"><option value="pulse" <?php if($channel['live_animation']=='pulse') echo 'selected';?>>Pulse</option><option value="blink" <?php if($channel['live_animation']=='blink') echo 'selected';?>>Blink</option></select></div>
            <div class="col-md-4"><label>Live Status</label><select name="live_enabled" class="form-select"><option value="1" <?php if($channel['live_enabled']==1) echo 'selected';?>>Show</option><option value="0" <?php if($channel['live_enabled']==0) echo 'selected';?>>Hide</option></select></div>
        </div>

        <label>Ticker Text</label><input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text']); ?>">
        
        <div class="row">
            <div class="col-md-6"><label>Ticker Speed</label><select name="ticker_speed" class="form-select"><?php for($s=10;$s<=100;$s+=10) echo "<option value='$s' ".($channel['ticker_speed']==$s?'selected':'').">$s</option>"; ?></select></div>
            <div class="col-md-6"><label>Ticker Status</label><select name="ticker_enabled" class="form-select"><option value="1" <?php if($channel['ticker_enabled']==1) echo 'selected';?>>Show</option><option value="0" <?php if($channel['ticker_enabled']==0) echo 'selected';?>>Hide</option></select></div>
        </div>

        <label>Ad URL</label><input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url']); ?>">
        
        <div class="row">
            <div class="col-md-4"><label>Ad Duration</label><select name="ad_duration" class="form-select"><?php for($i=5;$i<=60;$i+=5) echo "<option value='$i' ".($channel['ad_duration']==$i?'selected':'').">$i Sec</option>"; ?></select></div>
            <div class="col-md-4"><label>Ad Type</label><select name="ad_type" class="form-select"><option value="short" <?php if($channel['ad_type']=='short') echo 'selected';?>>Short</option><option value="full" <?php if($channel['ad_type']=='full') echo 'selected';?>>Full</option></select></div>
            <div class="col-md-4"><label>Ad Status</label><select name="ad_enabled" class="form-select"><option value="1" <?php if($channel['ad_enabled']==1) echo 'selected';?>>Active</option><option value="0" <?php if($channel['ad_enabled']==0) echo 'selected';?>>Inactive</option></select></div>
        </div>

        <label>Status</label><select name="status" class="form-select"><option value="1" <?php if($channel['status']==1) echo 'selected';?>>Active</option><option value="0" <?php if($channel['status']==0) echo 'selected';?>>Inactive</option></select>
        
        <button type="submit" class="btn btn-gold py-2">UPDATE CHANNEL</button>
    </form>
</div>
</body>
</html>
