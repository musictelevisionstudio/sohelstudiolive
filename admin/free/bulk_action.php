<?php
/* File: admin/free/bulk_action.php - MASTER UPDATED */
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];

    // ১. বাল্ক ডিলিট অ্যাকশন
    if ($action == 'delete' && !empty($ids)) {
        $idList = implode(',', $ids);
        $conn->query("DELETE FROM channels WHERE id IN ($idList)");
        $_SESSION['msg'] = "SUCCESS: " . count($ids) . " CHANNELS DELETED.";
    } 
    
    // ২. অটো-সর্টিং অ্যাকশন (অর্ডার ঠিক করা)
    elseif ($action == 'resort' && isset($_POST['start_number'])) {
        $order = intval($_POST['start_number']);
        
        // সব চ্যানেলের আইডি সিলেকশন
        $res = $conn->query("SELECT id FROM channels ORDER BY channel_order ASC, id ASC");
        
        $stmt = $conn->prepare("UPDATE channels SET channel_order = ? WHERE id = ?");
        
        while($row = $res->fetch_assoc()) {
            $stmt->bind_param("ii", $order, $row['id']);
            $stmt->execute();
            $order++;
        }
        $stmt->close();
        $_SESSION['msg'] = "SUCCESS: CHANNELS RE-SORTED.";
    }
    
    header("Location: channels.php");
    exit();
} else {
    // সরাসরি এক্সেস করলে ফেরত পাঠান
    header("Location: channels.php");
    exit();
}
?>

