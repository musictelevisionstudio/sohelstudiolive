<?php
/* File: admin/free/edit_channels.php - FULL SCREEN MOBILE APP STYLE */
ob_start();
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, status=?, channel_order=?, ticker_text=?, ticker_enabled=?, ticker_speed=?, ad_url=?, ad_enabled=?, ad_duration=?, live_text=? WHERE id=?");
    $update->bind_param("ssiisssisisi", 
        $_POST['channel_name'], $_POST['channel_url'], $_POST['status'], $_POST['channel_order'], 
        $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], 
        $_POST['ad_url'], $_POST['ad_enabled'], $_POST['ad_duration'], $_POST['live_text'], $id
    );
    if ($update->execute()) { header("Location: channels.php?msg=updated"); exit(); }
}

$stmt = $conn->prepare("SELECT * FROM channels WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$channel = $stmt->get_result()->fetch_assoc();
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; margin: 0; padding: 0; font-family: sans-serif; overflow-x: hidden; }
        .edit-container { min-height: 100vh; padding: 20px; background: #000; }
        .card { background: #111; border: 1px solid #333; border-radius: 15px; padding: 20px; }
        .form-control, .form-select { background: #222 !important; border: 1px solid #444 !important; color: #fff !important; margin-bottom: 15px; }
        label { color: gold; font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        .btn-gold { background: gold; color: #000; font-weight: bold; width: 100%; border: none; padding: 12px; border-radius: 8px; }
        h3 { color: gold; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
    </style>
</head>
<body>

<div class="edit-container">
    <a href="channels.php" class="btn btn-sm btn-outline-secondary mb-3">← Back</a>
    
    <div class="card">
        <h3 class="text-center">CHANNEL DETAILS</h3>
        <form method="POST">
            <label>Channel Name</label>
            <input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name']); ?>" required>
            
            <label>Channel URL</label>
            <input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url']); ?>" required>
            
            <label>Live Button Text</label>
            <input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text']); ?>">
            
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="1" <?php echo ($channel['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo ($channel['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
            </select>

            <label>Ticker Text</label>
            <input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text']); ?>">
            
            <label>Ticker Enabled</label>
            <select name="ticker_enabled" class="form-select">
                <option value="1" <?php echo ($channel['ticker_enabled'] == 1) ? 'selected' : ''; ?>>Show</option>
                <option value="0" <?php echo ($channel['ticker_enabled'] == 0) ? 'selected' : ''; ?>>Hide</option>
            </select>

            <label>Ticker Speed</label>
            <select name="ticker_speed" class="form-select">
                <?php for($s=10; $s<=100; $s+=10): ?>
                    <option value="<?php echo $s; ?>" <?php echo ($channel['ticker_speed'] == $s) ? 'selected' : ''; ?>><?php echo $s; ?> Speed</option>
                <?php endfor; ?>
            </select>

            <label>Ad Video URL</label>
            <input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url']); ?>">

            <label>Ad Duration (Seconds)</label>
            <select name="ad_duration" class="form-select">
                <?php for($i=10; $i<=100; $i+=10): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($channel['ad_duration'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> Seconds</option>
                <?php endfor; ?>
            </select>

            <label>Ads Status</label>
            <select name="ad_enabled" class="form-select">
                <option value="1" <?php echo ($channel['ad_enabled'] == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo ($channel['ad_enabled'] == 0) ? 'selected' : ''; ?>>Inactive</option>
            </select>
            
            <button type="submit" class="btn btn-gold">UPDATE CHANNEL</button>
        </form>
    </div>
</div>

</body>
</html>
