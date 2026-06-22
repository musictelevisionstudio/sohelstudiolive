<?php
// যদি আপনার প্রোফাইল বা সাপোর্ট পেজে ডাটাবেসের প্রয়োজন হয়, তবে কানেকশন ঠিক রাখুন
require_once 'config/db.php'; 

// যদি ডাটাবেস থেকে কিছু দেখাতে চান বা পোস্ট রিকোয়েস্ট হ্যান্ডেল করতে চান
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name      = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name    = mysqli_real_escape_string($conn, $_POST['father']);
    $mother_name    = mysqli_real_escape_string($conn, $_POST['mother']);
    $address        = mysqli_real_escape_string($conn, $_POST['address']);
    $district       = mysqli_real_escape_string($conn, $_POST['district']);
    $package        = mysqli_real_escape_string($conn, $_POST['package']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['paymentMethod']);
    $sender_number  = mysqli_real_escape_string($conn, $_POST['senderNumber']);
    $trx_id         = mysqli_real_escape_string($conn, $_POST['trxId']);

    // আপনার অরিজিনাল টেবিল অনুযায়ী ইনসার্ট কুয়েরি
    $sql = "INSERT INTO subscriptions 
            (full_name, father_name, mother_name, address, district, package, payment_method, sender_number, trx_id) 
            VALUES 
            ('$full_name', '$father_name', '$mother_name', '$address', '$district', '$package', '$payment_method', '$sender_number', '$trx_id')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "msg" => "সাবস্ক্রিপশন সফল হয়েছে!"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "ডাটাবেস এরর: " . mysqli_error($conn)]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support</title>
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; margin: 0; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input, select { width: 100%; padding: 10px; background: #222; border: 1px solid #444; color: #fff; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #1565d8; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .close-btn { background: red; margin-top: 10px; }
    </style>
</head>
<body>
    <div style="max-width: 400px; margin: auto;">
        <h2>সাপোর্ট রিকোয়েস্ট</h2>
        <form id="supportForm" method="POST">
            <div class="form-group"><input type="text" name="name" placeholder="আপনার নাম" required></div>
            <div class="form-group"><input type="text" name="father" placeholder="বাবার নাম" required></div>
            <div class="form-group"><input type="text" name="mother" placeholder="মায়ের নাম" required></div>
            <div class="form-group"><input type="text" name="address" placeholder="ঠিকানা" required></div>
            <div class="form-group"><input type="text" name="district" placeholder="জেলা" required></div>
            <div class="form-group">
                <select name="package">
                    <option value="Basic">বেসিক প্যাকেজ</option>
                    <option value="Premium">প্রিমিয়াম প্যাকেজ</option>
                </select>
            </div>
            <div class="form-group"><input type="text" name="paymentMethod" placeholder="পেমেন্ট মেথড (Bkash/Nagad)" required></div>
            <div class="form-group"><input type="text" name="senderNumber" placeholder="আপনার নাম্বার" required></div>
            <div class="form-group"><input type="text" name="trxId" placeholder="ট্রানজেকশন আইডি" required></div>
            <button type="submit">সাবমিট করুন</button>
        </form>
        <button class="close-btn" onclick="window.parent.closeSupport()">বন্ধ করুন</button>
        <p id="msg" style="text-align:center;"></p>
    </div>

    <script>
        document.getElementById('supportForm').onsubmit = async (e) => {
            e.preventDefault();
            let formData = new FormData(e.target);
            let res = await fetch('support.php', { method: 'POST', body: formData });
            let data = await res.json();
            document.getElementById('msg').innerText = data.msg;
            if(data.status === 'success') alert('সফল হয়েছে!');
        };
    </script>
</body>
</html>