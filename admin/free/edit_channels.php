<?php
/* File: admin/free/edit_channels.php - UPDATED WITH ORDER NUMBER */
ob_start();
session_start();
require_once '../../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // অর্ডার নাম্বার আপডেট করার জন্য লজিক ঠিক রাখা হয়েছে
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; padding: 10px; }
        .app-container { max-width: 600px; margin: auto; }
        .section-box { border: 1px solid gold; padding: 15px; border-radius: 10px; margin-bottom: 15px; background: #111; }
        .section-title { color: gold; font-weight: bold; font-size: 16px; margin-bottom: 10px; display: block; text-align: center; }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; margin-bottom: 10px; }
        label { color: #ccc; font-size: 13px; margin-bottom: 2px; }
        .btn-main { background: gold; color: #000; font-weight: bold; width: 100%; padding: 12px; border: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="app-container">
    <a href="channels.php" class="btn btn-outline-secondary btn-sm mb-2">← Back</a>
    
    <form method="POST">
        <div class="section-box">
            <span class="section-title">চ্যানেল ইনফরমেশন</span>
            <label>চ্যানেল নাম</label>
            <input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars($channel['channel_name']); ?>" required>
            <label>চ্যানেল লিঙ্ক</label>
            <input type="text" name="channel_url" class="form-control" value="<?php echo htmlspecialchars($channel['channel_url']); ?>" required>
            
            <label>অর্ডার নম্বর (0, 1, 5 ইত্যাদি)</label>
            <input type="number" name="channel_order" class="form-control" value="<?php echo htmlspecialchars($channel['channel_order']); ?>" placeholder="অর্ডার লিখুন">
        </div>

        <div class="section-box">
            <span class="section-title">হেডলাইন সেটিং</span>
            <label>হেডলাইন টেক্সট</label>
            <input type="text" name="ticker_text" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_text']); ?>">
            <div class="row">
                <div class="col-6">
                    <label>হেডলাইন স্ট্যাটাস</label>
                    <select name="ticker_enabled" class="form-select">
                        <option value="1" <?php echo ($channel['ticker_enabled'] == 1) ? 'selected' : ''; ?>>Show</option>
                        <option value="0" <?php echo ($channel['ticker_enabled'] == 0) ? 'selected' : ''; ?>>Hide</option>
                    </select>
                </div>
                <div class="col-6">
                    <label>স্পিড (10-100)</label>
                    <input type="number" name="ticker_speed" class="form-control" value="<?php echo htmlspecialchars($channel['ticker_speed']); ?>">
                </div>
            </div>
        </div>

        <div class="section-box">
            <span class="section-title">লাইভ বাটন সেটিং</span>
            <label>লাইভ টেক্সট</label>
            <input type="text" name="live_text" class="form-control" value="<?php echo htmlspecialchars($channel['live_text']); ?>">
            <label>লাইভ স্ট্যাটাস</label>
            <select name="status" class="form-select">
                <option value="1" <?php echo ($channel['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo ($channel['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="section-box">
            <span class="section-title">অ্যাডভার্টাইজ সেটিং</span>
            <label>অ্যাড লিঙ্ক</label>
            <input type="text" name="ad_url" class="form-control" value="<?php echo htmlspecialchars($channel['ad_url']); ?>">
            <div class="row">
                <div class="col-6">
                    <label>টাইম (সেকেন্ড)</label>
                    <input type="number" name="ad_duration" class="form-control" value="<?php echo htmlspecialchars($channel['ad_duration']); ?>">
                </div>
                <div class="col-6">
                    <label>অ্যাড স্ট্যাটাস</label>
                    <select name="ad_enabled" class="form-select">
                        <option value="1" <?php echo ($channel['ad_enabled'] == 1) ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($channel['ad_enabled'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-main mb-4">সম্পূর্ণ সেভ করুন</button>
    </form>
</div>

</body>
</html>
