<?php
/* File: admin/free/channels.php - FINAL OPTIMIZED VERSION */
session_start();
require_once '../../config/db.php'; 
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Channels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; touch-action: manipulation; }
        .control-panel { background: #111; padding: 15px; border-bottom: 3px solid gold; margin-bottom: 10px; }
        .btn-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        .btn-action { height: 50px; font-weight: bold; font-size: 16px; }
        .table { color: #fff; font-size: 14px; } /* ফন্ট সাইজ বাড়িয়েছি */
        .table td { vertical-align: middle; padding: 12px 6px; }
        .btn-gold { background: gold; color: #000; border: none; }
        .badge-status { font-size: 10px; padding: 4px 8px; border-radius: 4px; }
        /* মেসেজ বক্সের জন্য আলাদা স্টাইল */
        .msg-box { background: #28a745; color: white; padding: 15px; border-radius: 10px; margin: 10px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-box">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <form action="bulk_action.php" method="POST">
        <div class="control-panel">
            <div class="btn-row">
                <a href="../dashboard.php" class="btn btn-outline-light btn-action">BACK</a>
                <a href="add_channels.php" class="btn btn-gold btn-action">+ ADD</a>
                <button type="submit" name="action" value="delete" class="btn btn-danger btn-action" onclick="return confirm('নিশ্চিত ডিলিট করবেন?')">DELETE</button>
            </div>
            <div class="d-flex gap-2">
                <input type="number" name="start_number" class="form-control" placeholder="Start Order" required style="height: 50px;">
                <button type="submit" name="action" value="resort" class="btn btn-primary w-50" style="height: 50px;">AUTO-SORT</button>
            </div>
        </div>

        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 8%;"><input type="checkbox" id="selectAll" style="transform: scale(1.5);"></th>
                    <th style="width: 10%;">Ord</th>
                    <th style="width: 45%;">Channel Name</th>
                    <th style="width: 37%; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res = $conn->query("SELECT * FROM channels ORDER BY channel_order ASC");
                while($row = $res->fetch_assoc()): 
                    $sColor = $row['status'] == 1 ? 'bg-success' : 'bg-danger';
                    $aColor = $row['ad_enabled'] == 1 ? 'bg-warning text-dark' : 'bg-secondary';
                    $tColor = $row['ticker_enabled'] == 1 ? 'bg-info' : 'bg-dark';
                ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" style="transform: scale(1.5);"></td>
                    <td><?php echo $row['channel_order']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['channel_name']); ?><br>
                        <span class="badge <?php echo $sColor; ?> badge-status">Stat</span>
                        <span class="badge <?php echo $aColor; ?> badge-status">Ads</span>
                        <span class="badge <?php echo $tColor; ?> badge-status">Tick</span>
                    </td>
                    <td class="text-end">
                        <a href="edit_channels.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning px-3">Edit</a>
                        <a href="delete_channels.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger px-3" onclick="return confirm('ডিলিট?')">Del</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    document.getElementById('selectAll').onclick = function() {
        var checkboxes = document.querySelectorAll('input[name="ids[]"]');
        for (var checkbox of checkboxes) { checkbox.checked = this.checked; }
    }
</script>
</body>
</html>
