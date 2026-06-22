<?php
/* File: admin/free/bulk_action.php - FIXED VERSION */
// লক্ষ্য করুন: <?php এর আগে কোনো খালি লাইন বা স্পেস নেই।
ob_start(); // আউটপুট বাফারিং চালু করছি যাতে হেডার এরর না হয়
session_start();
require_once '../../config/db.php';

// লগইন চেক
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];

    if ($action == 'delete' && !empty($ids)) {
        $idList = implode(',', $ids);
        $conn->query("DELETE FROM channels WHERE id IN ($idList)");
        $_SESSION['msg'] = "SUCCESS: " . count($ids) . " CHANNELS DELETED.";
    } 
    
    elseif ($action == 'resort' && isset($_POST['start_number'])) {
        $order = intval($_POST['start_number']);
        $res = $conn->query("SELECT id FROM channels ORDER BY channel_order ASC, id ASC");
        
        if ($res) {
            $stmt = $conn->prepare("UPDATE channels SET channel_order = ? WHERE id = ?");
            while($row = $res->fetch_assoc()) {
                $stmt->bind_param("ii", $order, $row['id']);
                $stmt->execute();
                $order++;
            }
            $stmt->close();
            $_SESSION['msg'] = "SUCCESS: CHANNELS RE-SORTED.";
        }
    }
    
    header("Location: channels.php");
    ob_end_flush(); // আউটপুট বাফার শেষ
    exit();
} else {
    header("Location: channels.php");
    ob_end_flush();
    exit();
}
?>
