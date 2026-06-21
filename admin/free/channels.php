<?php
/* File: admin/free/channels.php - FINAL MASTER LIST */
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
        .control-panel { background: #000; padding: 10px; border-bottom: 2px solid gold; }
        .btn-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 10px; }
        .btn-action { height: 45px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .table { color: #fff; font-size: 12px; }
        .table td { vertical-align: middle; padding: 8px 4px; }
        .btn-gold { background: gold; color: #000; border: none; }
        .badge-status { font-size: 9px; padding: 3px 6px; border-radius: 4px; margin-right: 2px; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-warning text-center p-2 mb-0"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <form action="bulk_action.php" method="POST">
        <div class="control-panel">
            <div class="btn-row">
                <a href="../dashboard.php" class="btn btn-outline-light btn-action">BACK</a>
                <a href="add_channels.php" class="btn btn-gold btn-action">+ ADD</a>
                <button type="submit" name="action" value="delete" class="btn btn-danger btn-action" onclick="return confirm('নিশ্চিত ডিলিট করবেন?')">DELETE</button>
            </div>
            <div class="d-flex gap-2">
                <input type="number" name="start_number" class="form-control" placeholder="Start Order" required>
                <button type="submit" name="action" value="resort" class="btn btn-primary w-50">AUTO-SORT</button>
            </div>
        </div>

        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;"><input type="checkbox" id="selectAll"></th>
                    <th style="width: 10%;">Ord</th>
                    <th style="width: 50%;">Channel Name</th>
                    <th style="width: 35%; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res = $conn->query("SELECT * FROM channels ORDER BY channel_order ASC");
                while($row = $res->fetch_assoc()): 
                    // স্ট্যাটাস এবং অ্যাডভান্সড সেটিংসের ইন্ডিকেটর
                    $sColor = $row['status'] == 1 ? 'bg-success' : 'bg-danger';
                    $aColor = $row['ads_status'] == 1 ? 'bg-warning text-dark' : 'bg-secondary';
                    $tColor = $row['ticker_enabled'] == 1 ? 'bg-info' : 'bg-dark';
                ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>"></td>
                    <td><?php echo $row['channel_order']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['channel_name']); ?><br>
                        <span class="badge <?php echo $sColor; ?> badge-status">Stat</span>
                        <span class="badge <?php echo $aColor; ?> badge-status">Ads</span>
                        <span class="badge <?php echo $tColor; ?> badge-status">Tick</span>
                    </td>
                    <td class="text-end">
                        <a href="edit_channels.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_channels.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('ডিলিট করবেন?')">Del</a>
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

