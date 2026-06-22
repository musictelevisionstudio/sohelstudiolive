<?php
/* File: admin/free/edit_channels.php - FULL SCREEN & CORRECTED */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'basic') {
        // 4টি কলাম + 1টি আইডি = 5টি (ssiii)
        $stmt = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, status=?, channel_order=? WHERE id=?");
        $stmt->bind_param("ssiii", $_POST['channel_name'], $_POST['channel_url'], $_POST['status'], $_POST['channel_order'], $id);
    } elseif ($action == 'live') {
        // 3টি কলাম + 1টি আইডি = 4টি (ssii)
        $stmt = $conn->prepare("UPDATE channels SET live_text=?, live_animation=?, live_enabled=? WHERE id=?");
        $stmt->bind_param("ssii", $_POST['live_text'], $_POST['live_animation'], $_POST['live_enabled'], $id);
    } elseif ($action == 'ticker') {
        // 4টি কলাম + 1টি আইডি = 5টি (sisii)
        $stmt = $conn->prepare("UPDATE channels SET ticker_text=?, ticker_enabled=?, ticker_speed=?, ticker_direction=? WHERE id=?");
        $stmt->bind_param("sisii", $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], $_POST['ticker_direction'], $id);
    } elseif ($action == 'ads') {
        // 6টি কলাম + 1টি আইডি = 7টি (siisiii)
        $stmt = $conn->prepare("UPDATE channels SET ad_url=?, ad_enabled=?, ads_status=?, ad_type=?, ad_size=?, ad_duration=? WHERE id=?");
        $stmt->bind_param("siisiii", $_POST['ad_url'], $_POST['ad_enabled'], $_POST['ads_status'], $_POST['ad_type'], $_POST['ad_size'], $_POST['ad_duration'], $id);
    }
    
    if (isset($stmt)) {
        if($stmt->execute()) {
            header("Location: edit_channels.php?id=$id&msg=saved"); exit();
        } else {
            echo "Error: " . $stmt->error; exit();
        }
    }
}

$c = $conn->query("SELECT * FROM channels WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#000;color:#fff;padding:20px;}
        .box{border:2px solid gold;padding:15px;margin-bottom:15px;border-radius:12px;background:#111;}
        .btn-gold{background:gold;color:#000;font-weight:bold;width:100%;padding:12px;border-radius:8px;}
        .form-control, .form-select{background:#222;color:#fff;border:1px solid #555;}
        label{color:gold;font-weight:bold;}
    </style>
</head>
<body>
<div class="container-fluid">
    <a href="channels.php" class="text-warning">← Back</a>
    <h3 class="text-center text-warning">EDIT: <?php echo htmlspecialchars($c['channel_name']); ?></h3>
    <div class="row">
        <div class="col-md-6"><form method="POST" class="box">
            <input type="hidden" name="action" value="basic">
            <label>Name</label><input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($c['channel_name']); ?>">
            <label>URL</label><input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($c['channel_url']); ?>">
            <label>Status</label><select name="status" class="form-select"><option value="1" <?php echo ($c['status']==1?'selected':'');?>>Active</option><option value="0" <?php echo ($c['status']==0?'selected':'');?>>Inactive</option></select>
            <label>Order</label><input type="number" name="channel_order" class="form-control" value="<?php echo $c['channel_order']; ?>">
            <button type="submit" class="btn btn-gold">UPDATE BASIC</button>
        </form></div>
        <div class="col-md-6"><form method="POST" class="box">
            <input type="hidden" name="action" value="live">
            <label>Live Text</label><input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($c['live_text']); ?>">
            <label>Animation</label><select name="live_animation" class="form-select"><?php foreach(['pulse','blink','fade','slide','zoom','shake','bounce','flip','rotate','wobble'] as $anim):?><option value="<?php echo $anim; ?>" <?php echo ($c['live_animation']==$anim?'selected':''); ?>><?php echo ucfirst($anim);?></option><?php endforeach;?></select>
            <label>Live Enabled</label><select name="live_enabled" class="form-select"><option value="1" <?php echo ($c['live_enabled']==1?'selected':'');?>>ON</option><option value="0" <?php echo ($c['live_enabled']==0?'selected':'');?>>OFF</option></select>
            <button type="submit" class="btn btn-gold">UPDATE LIVE</button>
        </form></div>
        <div class="col-md-6"><form method="POST" class="box">
            <input type="hidden" name="action" value="ticker">
            <label>Headline</label><input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($c['ticker_text']); ?>">
            <label>Status</label><select name="ticker_enabled" class="form-select"><option value="1" <?php echo ($c['ticker_enabled']==1?'selected':''); ?>>Show</option><option value="0" <?php echo ($c['ticker_enabled']==0?'selected':''); ?>>Hide</option></select>
            <label>Speed (Sec)</label><input type="number" name="ticker_speed" class="form-control" value="<?php echo $c['ticker_speed']; ?>">
            <label>Direction</label><select name="ticker_direction" class="form-select"><option value="left" <?php echo ($c['ticker_direction']=='left'?'selected':'');?>>Left</option><option value="right" <?php echo ($c['ticker_direction']=='right'?'selected':'');?>>Right</option></select>
            <button type="submit" class="btn btn-gold">UPDATE HEADLINE</button>
        </form></div>
        <div class="col-md-6"><form method="POST" class="box">
            <input type="hidden" name="action" value="ads">
            <label>Ad URL</label><input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($c['ad_url']); ?>">
            <label>Ad Enabled</label><select name="ad_enabled" class="form-select"><option value="1" <?php echo ($c['ad_enabled']==1?'selected':''); ?>>ON</option><option value="0" <?php echo ($c['ad_enabled']==0?'selected':''); ?>>OFF</option></select>
            <label>Ads Status</label><input type="number" name="ads_status" class="form-control" value="<?php echo $c['ads_status']; ?>">
            <label>Ad Type</label><select name="ad_type" class="form-select"><option value="short" <?php echo ($c['ad_type']=='short'?'selected':''); ?>>Short</option><option value="full" <?php echo ($c['ad_type']=='full'?'selected':''); ?>>Full</option></select>
            <label>Ad Size (%)</label><input type="number" name="ad_size" class="form-control" value="<?php echo $c['ad_size']; ?>">
            <label>Duration (Sec)</label><input type="number" name="ad_duration" class="form-control" value="<?php echo $c['ad_duration']; ?>">
            <button type="submit" class="btn btn-gold">UPDATE ADS</button>
        </form></div>
    </div>
</div>
</body>
</html>