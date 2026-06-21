<?php
/* File: admin/header.php - FULLY SYNCED WITH ADMINS TABLE */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once '../config/db.php';
}

// ডিফল্ট মান
$admin = ['full_name' => 'Admin', 'email' => 'N/A', 'country_code' => '', 'phone_number' => 'N/A'];

if (isset($_SESSION['admin_id'])) {
    // টেবিলের প্রতিটি কলাম সঠিকভাবে নির্বাচন করা হলো
    $stmt = $conn->prepare("SELECT full_name, email, country_code, phone_number FROM admins WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $admin = $row;
    }
}
?>

<div style="background:#000; padding:15px; border-bottom:2px solid gold; position:sticky; top:0; z-index:1000; text-align:center; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
    <div style="color:gold; font-weight:bold; font-size:1.4rem; margin-bottom:10px; display: flex; justify-content: center; align-items: center; gap: 8px;">
        <i class="fas fa-user-tie"></i> 
        <?php echo htmlspecialchars($admin['full_name']); ?>
    </div>
    
    <div style="color:#fff; font-size:0.85rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; border-top: 1px solid #333; padding-top: 10px;">
        <span><i class="fas fa-envelope" style="color:gold;"></i> <?php echo htmlspecialchars($admin['email']); ?></span>
        <span>
            <i class="fas fa-phone" style="color:gold;"></i> 
            <?php echo htmlspecialchars($admin['country_code'] . $admin['phone_number']); ?>
        </span>
    </div>

    <div style="margin-top: 12px;">
        <a href="../logout.php" style="color: #ff4444; font-size: 0.8rem; text-decoration: none; border: 1px solid #ff4444; padding: 3px 10px; border-radius: 4px;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
