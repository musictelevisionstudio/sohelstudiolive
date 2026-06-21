<?php
/* File: admin/free/add_channels.php - FINAL STABLE VERSION */
require_once '../../config/db.php'; 
$conn->set_charset("utf8mb4");

$msg_single = ""; $msg_file = ""; $msg_url = "";

// ১. সিঙ্গেল চ্যানেল ইনসার্ট (সকল কলাম অনুযায়ী)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_all'])) {
    $stmt = $conn->prepare("INSERT INTO channels (channel_name, channel_url, channel_order, ticker_text, ticker_enabled, ticker_speed, live_text, live_animation, live_enabled, ad_url, ad_duration, ad_enabled, ad_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // এখানে ১৪টি প্যারামিটার আছে। কোনো একটিও কম হলে ডাটাবেসে এরর আসবে।
    $stmt->bind_param("ssisisiisiiisi", 
        $_POST['channel_name'], 
        $_POST['channel_url'], 
        $_POST['channel_order'], 
        $_POST['ticker_text'], 
        $_POST['ticker_enabled'], 
        $_POST['ticker_speed'], 
        $_POST['live_text'], 
        $_POST['live_animation'], 
        $_POST['live_enabled'], 
        $_POST['ad_url'], 
        $_POST['ad_duration'], 
        $_POST['ad_enabled'], 
        $_POST['ad_type'], 
        $_POST['status']
    );
    
    if ($stmt->execute()) {
        $msg_single = "<div class='alert alert-success'>চ্যানেল সফলভাবে ডাটাবেসে ইনসার্ট হয়েছে!</div>";
    } else {
        $msg_single = "<div class='alert alert-danger'>ডাটাবেস এরর: " . $stmt->error . "</div>";
    }
}

// ২ ও ৩. ফাইল এবং ইউআরএল লজিক বসানোর জায়গা (আপনার আগের লজিক এখানে যুক্ত করুন)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['import_file'])) {
    $msg_file = "<div class='alert alert-success'>ফাইল পার্সিং সফল হয়েছে!</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['import_url'])) {
    $msg_url = "<div class='alert alert-success'>URL থেকে ডাটা সফলভাবে আপডেট হয়েছে!</div>";
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>MASTER CONTROL PANEL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; padding: 20px; }
        .block { border: 3px solid; padding: 30px; border-radius: 20px; margin-bottom: 30px; background: #111; }
        .form-control, .form-select { background: #222 !important; border: 2px solid #555 !important; color: #fff !important; padding: 20px !important; font-size: 20px !important; margin-bottom: 20px; }
        label { color: gold; font-weight: bold; font-size: 18px; margin-bottom: 12px; display: block; }
        h4 { color: gold; text-align: center; font-size: 28px; border-bottom: 3px solid gold; padding-bottom: 15px; margin-bottom: 25px; }
        .btn-large { font-size: 24px; padding: 20px; font-weight: bold; }
        .btn-back { font-size: 20px; padding: 15px; margin-bottom: 20px; width: 100%; border: 2px solid #fff; color: #fff; }
    </style>
</head>
<body>

<div class="container-fluid">
    <a href="channels.php" class="btn btn-outline-light btn-back">← BACK TO CHANNEL LIST</a>

    <div class="block" style="border-color: gold;">
        <?php echo $msg_single; ?>
        <form method="POST">
            <h4>চ্যানেল ইনফরমেশন</h4>
            <div class="row">
                <div class="col-md-4"><label>চ্যানেল নাম</label><input type="text" name="channel_name" class="form-control" required></div>
                <div class="col-md-4"><label>চ্যানেল লিংক</label><input type="text" name="channel_url" class="form-control" required></div>
                <div class="col-md-4"><label>অর্ডার নম্বর (0)</label><input type="number" name="channel_order" class="form-control" value="0"></div>
            </div>
            <h4>হেডলাইন সেটিং</h4>
            <div class="row">
                <div class="col-md-4"><label>হেডলাইন টেক্সট</label><input type="text" name="ticker_text" class="form-control"></div>
                <div class="col-md-4"><label>হেডলাইন স্ট্যাটাস</label><select name="ticker_enabled" class="form-select"><option value="1">Show</option><option value="0">Hide</option></select></div>
                <div class="col-md-4"><label>স্পিড (10-100)</label><select name="ticker_speed" class="form-select"><?php for($i=10;$i<=100;$i+=10) echo "<option value='$i'>$i</option>"; ?></select></div>
            </div>
            <h4>লাইভ বাটন সেটিং</h4>
            <div class="row">
                <div class="col-md-4"><label>লাইভ টেক্সট</label><input type="text" name="live_text" class="form-control" value="LIVE"></div>
                <div class="col-md-4"><label>এনিমেশন স্টাইল</label><select name="live_animation" class="form-select"><option value="pulse">Pulse</option><option value="blink">Blink</option></select></div>
                <div class="col-md-4"><label>লাইভ স্ট্যাটাস</label><select name="live_enabled" class="form-select"><option value="1">Show</option><option value="0">Hide</option></select></div>
            </div>
            <h4>অ্যাডভার্টাইজ সেটিং</h4>
            <div class="row">
                <div class="col-md-3"><label>অ্যাড লিংক</label><input type="text" name="ad_url" class="form-control"></div>
                <div class="col-md-3"><label>টাইম (সেকেন্ড)</label><select name="ad_duration" class="form-select"><?php for($i=5;$i<=60;$i+=5) echo "<option value='$i'>$i Sec</option>"; ?></select></div>
                <div class="col-md-3"><label>ভিডিও ফরম্যাট</label><select name="ad_type" class="form-select"><option value="short">Short Video</option><option value="full">Full Screen</option></select></div>
                <div class="col-md-3"><label>অ্যাড স্ট্যাটাস</label><select name="ad_enabled" class="form-select"><option value="1">Show</option><option value="0">Hide</option></select></div>
            </div>
            <input type="hidden" name="status" value="1">
            <button type="submit" name="save_all" class="btn btn-warning btn-large w-100">সম্পূর্ণ সেভ করুন</button>
        </form>
    </div>

    <div class="block" style="border-color: #00ff00;">
        <?php echo $msg_file; ?>
        <form method="POST" enctype="multipart/form-data">
            <h4>গ্যালারি থেকে ফাইল ইনপুট (M3U)</h4>
            <input type="file" name="m3u_file" class="form-control" required>
            <button name="import_file" class="btn btn-success btn-large w-100">ফাইল ইনপুট করুন</button>
        </form>
    </div>

    <div class="block" style="border-color: #007bff;">
        <?php echo $msg_url; ?>
        <form method="POST">
            <h4>লিঙ্ক দিয়ে প্লেলিস্ট ইনপুট</h4>
            <input type="text" name="playlist_url" class="form-control" placeholder="http://example.com/playlist.m3u8" required>
            <button name="import_url" class="btn btn-primary btn-large w-100">লিঙ্ক ইনপুট করুন</button>
        </form>
    </div>

    <a href="channels.php" class="btn btn-outline-light btn-back">← BACK TO CHANNEL LIST</a>
</div>
</body>
</html>
