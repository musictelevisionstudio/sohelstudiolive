<?php
/* File: admin/free/edit_channels.php - FULL MASTER UPDATED */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ১৯টি কলামের আপডেট কুয়েরি (আপনার টেবিল স্কিমা অনুযায়ী)
    $sql = "UPDATE channels SET channel_name=?, channel_url=?, status=?, ads_status=?, channel_order=?, ticker_text=?, ticker_enabled=?, ad_url=?, ad_enabled=?, live_text=?, ticker_speed=?, ad_duration=?, live_animation=?, live_enabled=?, ticker_direction=?, ad_type=?, ad_size=? WHERE id=?";
    
    $update = $conn->prepare($sql);
    $update->bind_param("ssiiisissisississi", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['status'], $_POST['ads_status'], 
        $_POST['channel_order'], $_POST['ticker_text'], $_POST['ticker_enabled'], 
        $_POST['ad_url'], $_POST['ad_enabled'], $_POST['live_text'], 
        $_POST['ticker_speed'], $_POST['ad_duration'], $_POST['live_animation'], 
        $_POST['live_enabled'], $_POST['ticker_direction'], $_POST['ad_type'], 
        $_POST['ad_size'], $id
    );
    
    if ($update->execute()) { header("Location: channels.php?msg=updated"); } 
    exit();
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
        .edit-container { max-width: 500px; margin: auto; border: 2px solid gold; padding: 25px; border-radius: 15px; background: #111; }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; margin-bottom: 15px; }
        label { color: gold; font-weight: bold; margin-bottom: 5px; display: block; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="channels.php" class="text-warning text-decoration-none">← Back</a>
    <h3 class="text-warning text-center mb-4">CHANNEL DETAILS</h3>
    
    <form method="POST">
        <label>Channel Name</label>
        <input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name'] ?? ''); ?>" required>
        
        <label>Channel URL</label>
        <input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url'] ?? ''); ?>" required>

        <div class="row">
            <div class="col-6"><label>Status</label><select name="status" class="form-select"><option value="1" <?php echo ($channel['status'] == 1) ? 'selected' : ''; ?>>Active</option><option value="0" <?php echo ($channel['status'] == 0) ? 'selected' : ''; ?>>Inactive</option></select></div>
            <div class="col-6"><label>Ads Status</label><select name="ads_status" class="form-select"><option value="1" <?php echo ($channel['ads_status'] == 1) ? 'selected' : ''; ?>>Active</option><option value="0" <?php echo ($channel['ads_status'] == 0) ? 'selected' : ''; ?>>Inactive</option></select></div>
        </div>

        <label>Live Settings</label>
        <div class="row">
            <div class="col-4"><input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text'] ?? ''); ?>"></div>
            <div class="col-4"><select name="live_animation" class="form-select"><option value="pulse" <?php echo ($channel['live_animation'] == 'pulse') ? 'selected' : ''; ?>>Pulse</option><option value="blink" <?php echo ($channel['live_animation'] == 'blink') ? 'selected' : ''; ?>>Blink</option></select></div>
            <div class="col-4"><select name="live_enabled" class="form-select"><option value="1" <?php echo ($channel['live_enabled'] == 1) ? 'selected' : ''; ?>>ON</option><option value="0" <?php echo ($channel['live_enabled'] == 0) ? 'selected' : ''; ?>>OFF</option></select></div>
        </div>

        <label>Ticker Settings</label>
        <input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>">
        <div class="row">
            <div class="col-4"><select name="ticker_speed" class="form-select"><?php for($s=10; $s<=100; $s+=10): ?><option value="<?php echo $s; ?>" <?php echo ($channel['ticker_speed'] == $s) ? 'selected' : ''; ?>><?php echo $s; ?>%</option><?php endfor; ?></select></div>
            <div class="col-4"><select name="ticker_direction" class="form-select"><option value="left" <?php echo ($channel['ticker_direction'] == 'left') ? 'selected' : ''; ?>>Left</option><option value="right" <?php echo ($channel['ticker_direction'] == 'right') ? 'selected' : ''; ?>>Right</option></select></div>
            <div class="col-4"><select name="ticker_enabled" class="form-select"><option value="1" <?php echo ($channel['ticker_enabled'] == 1) ? 'selected' : ''; ?>>Show</option><option value="0" <?php echo ($channel['ticker_enabled'] == 0) ? 'selected' : ''; ?>>Hide</option></select></div>
        </div>

        <label>Ad Settings</label>
        <input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url'] ?? ''); ?>">
        <div class="row">
            <div class="col-4"><select name="ad_type" class="form-select"><option value="short" <?php echo ($channel['ad_type'] == 'short') ? 'selected' : ''; ?>>Short</option><option value="long" <?php echo ($channel['ad_type'] == 'long') ? 'selected' : ''; ?>>Long</option></select></div>
            <div class="col-4"><select name="ad_size" class="form-select"><?php for($i=20; $i<=100; $i+=10): ?><option value="<?php echo $i; ?>" <?php echo ($channel['ad_size'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?>%</option><?php endfor; ?></select></div>
            <div class="col-4"><select name="ad_duration" class="form-select"><?php for($i=10; $i<=120; $i+=10): ?><option value="<?php echo $i; ?>" <?php echo ($channel['ad_duration'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?>s</option><?php endfor; ?></select></div>
        </div>

        <button type="submit" class="btn btn-gold py-2 mt-3">UPDATE CHANNEL</button>
    </form>
</div>
</body>
</html>
