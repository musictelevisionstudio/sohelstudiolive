<?php
/* File: admin/free/edit_channels.php - FINAL DYNAMIC MASTER */
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    // ১৯টি কলাম আপডেট লজিক
    if ($action == 'basic') {
        $stmt = $conn->prepare("UPDATE channels SET channel_name=?, channel_url=?, status=?, channel_order=? WHERE id=?");
        $stmt->bind_param("ssiii", $_POST['channel_name'], $_POST['channel_url'], $_POST['status'], $_POST['channel_order'], $id);
    } elseif ($action == 'live') {
        $stmt = $conn->prepare("UPDATE channels SET live_text=?, live_animation=?, live_enabled=? WHERE id=?");
        $stmt->bind_param("ssii", $_POST['live_text'], $_POST['live_animation'], $_POST['live_enabled'], $id);
    } elseif ($action == 'ticker') {
        $stmt = $conn->prepare("UPDATE channels SET ticker_text=?, ticker_enabled=?, ticker_speed=?, ticker_direction=? WHERE id=?");
        $stmt->bind_param("sisis", $_POST['ticker_text'], $_POST['ticker_enabled'], $_POST['ticker_speed'], $_POST['ticker_direction'], $id);
    } elseif ($action == 'ads') {
        $stmt = $conn->prepare("UPDATE channels SET ad_url=?, ad_enabled=?, ads_status=?, ad_type=?, ad_size=?, ad_duration=? WHERE id=?");
        $stmt->bind_param("sissiii", $_POST['ad_url'], $_POST['ad_enabled'], $_POST['ads_status'], $_POST['ad_type'], $_POST['ad_size'], $_POST['ad_duration'], $id);
    }
    if (isset($stmt)) $stmt->execute();
    header("Location: edit_channels.php?id=$id&msg=saved"); exit();
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
        body{background:#000;color:#fff;padding:10px;font-family:sans-serif;}
        .box{border:2px solid gold;padding:15px;margin-bottom:15px;border-radius:12px;background:#111;}
        .btn-gold{background:gold;color:#000;font-weight:bold;width:100%;padding:12px;margin-top:10px;border-radius:8px;}
        .form-select, .form-control{background:#222;color:#fff;border:1px solid #555;padding:10px;margin-bottom:8px;}
        label{color:gold;font-weight:bold;margin-bottom:5px;display:block;}
    </style>
</head>
<body>
<div style="max-width:600px; margin:auto;">
    <a href="channels.php" class="text-warning text-decoration-none">← Back</a>
    <h3 class="text-center text-warning my-3">EDIT: <?php echo htmlspecialchars($c['channel_name']); ?></h3>

    <form method="POST" class="box">
        <input type="hidden" name="action" value="basic">
        <label>Name & URL</label>
        <input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($c['channel_name']); ?>">
        <input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($c['channel_url']); ?>">
        <label>Status (Active/Inactive)</label>
        <select name="status" class="form-select"><option value="1" <?php echo ($c['status']==1?'selected':'');?>>Active</option><option value="0" <?php echo ($c['status']==0?'selected':'');?>>Inactive</option></select>
        <label>Channel Order (Manual Input)</label>
        <input type="number" name="channel_order" class="form-control" value="<?php echo $c['channel_order']; ?>">
        <button type="submit" class="btn btn-gold">UPDATE BASIC</button>
    </form>

    <form method="POST" class="box">
        <input type="hidden" name="action" value="live">
        <label>Live Display Text (Current: <?php echo htmlspecialchars($c['live_text']); ?>)</label>
        <input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($c['live_text']); ?>">
        <label>Animation Style</label>
        <select name="live_animation" class="form-select">
            <?php foreach(['pulse','blink','fade','slide','zoom','shake','bounce','flip','rotate','wobble'] as $anim): ?>
            <option value="<?php echo $anim; ?>" <?php echo ($c['live_animation']==$anim?'selected':''); ?>><?php echo ucfirst($anim);?></option>
            <?php endforeach; ?>
        </select>
        <select name="live_enabled" class="form-select"><option value="1" <?php echo ($c['live_enabled']==1?'selected':'');?>>Live ON</option><option value="0" <?php echo ($c['live_enabled']==0?'selected':'');?>>Live OFF</option></select>
        <button type="submit" class="btn btn-gold">UPDATE LIVE</button>
    </form>

    <form method="POST" class="box">
        <input type="hidden" name="action" value="ticker">
        <label>Headline Text (Current: <?php echo htmlspecialchars($c['ticker_text']); ?>)</label>
        <input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($c['ticker_text']); ?>">
        <label>Headline Enable/Disable</label>
        <select name="ticker_enabled" class="form-select">
            <option value="1" <?php echo ($c['ticker_enabled']==1?'selected':''); ?>>Show Headline</option>
            <option value="0" <?php echo ($c['ticker_enabled']==0?'selected':''); ?>>Hide Headline</option>
        </select>
        <label>Speed (%)</label>
        <select name="ticker_speed" class="form-select"><?php for($s=10;$s<=100;$s+=10):?><option value="<?php echo $s;?>" <?php echo ($c['ticker_speed']==$s?'selected':'');?>>Speed <?php echo $s;?>%</option><?php endfor;?></select>
        <select name="ticker_direction" class="form-select"><option value="left" <?php echo ($c['ticker_direction']=='left'?'selected':'');?>>Left</option><option value="right" <?php echo ($c['ticker_direction']=='right'?'selected':'');?>>Right</option></select>
        <button type="submit" class="btn btn-gold">UPDATE HEADLINE</button>
    </form>

    <form method="POST" class="box">
        <input type="hidden" name="action" value="ads">
        <label>Ad URL</label>
        <input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($c['ad_url']); ?>">
        <label>Ad Enable/Disable</label>
        <select name="ad_enabled" class="form-select">
            <option value="1" <?php echo ($c['ad_enabled']==1?'selected':''); ?>>Ads ON</option>
            <option value="0" <?php echo ($c['ad_enabled']==0?'selected':''); ?>>Ads OFF</option>
        </select>
        <label>Ad Duration (Seconds)</label>
        <select name="ad_duration" class="form-select"><?php for($i=5;$i<=60;$i+=5):?><option value="<?php echo $i;?>" <?php echo ($c['ad_duration']==$i?'selected':'');?>>Time <?php echo $i;?> Sec</option><?php endfor;?></select>
        <button type="submit" class="btn btn-gold">UPDATE ADS</button>
    </form>
</div>
</body>
</html>
