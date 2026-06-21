<?php
require_once 'config/db.php'; // আপনার ডাটাবেস কানেকশন ফাইল
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_sub') {
    
    // ডাটাবেসে ইনসার্ট করার জন্য প্রস্তুত করা
    $stmt = $conn->prepare("INSERT INTO subscriptions (full_name, father_name, mother_name, address, district, package, payment_method, sender_number, trx_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssssss", 
        $_POST['name'], 
        $_POST['father'], 
        $_POST['mother'], 
        $_POST['address'], 
        $_POST['district'], 
        $_POST['package'], 
        $_POST['paymentMethod'], 
        $_POST['senderNumber'], 
        $_POST['trxId']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "msg" => "তথ্য সফলভাবে ডাটাবেসে সংরক্ষিত হয়েছে।"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "ডাটাবেস এরর: " . $stmt->error]);
    }
    exit;
}
?>
