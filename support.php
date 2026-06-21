<?php
require_once 'config/db.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডাটা গ্রহণ
    $full_name      = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name    = mysqli_real_escape_string($conn, $_POST['father']);
    $mother_name    = mysqli_real_escape_string($conn, $_POST['mother']);
    $address        = mysqli_real_escape_string($conn, $_POST['address']);
    $district       = mysqli_real_escape_string($conn, $_POST['district']);
    $package        = mysqli_real_escape_string($conn, $_POST['package']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['paymentMethod']);
    $sender_number  = mysqli_real_escape_string($conn, $_POST['senderNumber']);
    $trx_id         = mysqli_real_escape_string($conn, $_POST['trxId']);

    // ডাটাবেসে ইনসার্ট করার কুয়েরি
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
