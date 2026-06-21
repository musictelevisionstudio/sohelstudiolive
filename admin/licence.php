<?php
/* File: admin/licence.php - FIXED HEADER ISSUE */
ob_start(); // আউটপুট বাফারিং অন করলাম যাতে Header এরর না আসে
session_start();
require_once '../config/db.php';

// ডাটাবেস ক্যারেক্টার সেট
$conn->set_charset("utf8mb4");

// লগইন চেক
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Action হ্যান্ডলিং (আউটপুট বাফারিংয়ের জন্য এটি একদম নিরাপদ)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if($_GET['action'] == 'toggle') {
        $new_status = (isset($_GET['current']) && $_GET['current'] == 1) ? 0 : 1;
        $stmt = $conn->prepare("UPDATE devices SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_status, $id);
        $stmt->execute();
    } elseif($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM devices WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif($_GET['action'] == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("UPDATE devices SET name=?, fname=?, mname=?, addr=?, mobile=?, email=? WHERE id=?");
        $stmt->bind_param("ssssssi", $_POST['name'], $_POST['fname'], $_POST['mname'], $_POST['addr'], $_POST['mobile'], $_POST['email'], $id);
        $stmt->execute();
    }
    header("Location: licence.php");
    exit;
}

// ডাটা ফেচিং
$search = isset($_GET['search']) ? "%" . $conn->real_escape_string($_GET['search']) . "%" : null;
$query = $search ? "SELECT * FROM devices WHERE device_id LIKE ? OR name LIKE ? ORDER BY id DESC" : "SELECT * FROM devices ORDER BY id DESC";
$stmt = $conn->prepare($query);
if ($search) { $stmt->bind_param("ss", $search, $search); }
$stmt->execute();
$result = $stmt->get_result();
ob_end_flush(); // বাফার শেষ করলাম
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Device Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; }
        .modal-content input { width: 100%; margin-bottom: 10px; padding: 8px; background: #222; border: 1px solid #444; color: #fff; border-radius: 4px; }
        .btn-sm-custom { font-size: 11px; padding: 4px 8px; }
        .date-text { font-size: 10px; color: #ffc107; display: block; margin-top: 2px; }
        .id-text { font-size: 11px; color: #aaa; display: block; }
    </style>
</head>
<body>
<div class="container py-3">
    <a href="dashboard.php" class="btn btn-secondary btn-sm mb-3">← Back</a>
    <h2 class="text-center text-warning">DEVICE CONTROL</h2>
    
    <form method="GET" class="d-flex gap-2 mb-3">
        <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Search ID or Name...">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="table-responsive bg-dark rounded">
        <table class="table table-dark table-hover table-striped mb-0 text-center">
            <thead>
                <tr><th>Device Info</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="text-start ps-3">
                        <div class="text-warning fw-bold"><?php echo htmlspecialchars($row['name'] ?: 'Unnamed'); ?></div>
                        <div class="id-text"><?php echo htmlspecialchars($row['device_id']); ?></div>
                        <div class="date-text">📅 <?php echo $row['last_visit'] ? date("d M Y, h:i A", strtotime($row['last_visit'])) : 'N/A'; ?></div>
                    </td>
                    <td class="align-middle">
                        <span class="badge <?php echo $row['status'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $row['status'] ? 'ACTIVE' : 'BLOCKED'; ?>
                        </span>
                    </td>
                    <td class="align-middle">
                        <a href="?action=toggle&id=<?php echo $row['id']; ?>&current=<?php echo $row['status']; ?>" 
                           class="btn btn-sm btn-<?php echo $row['status'] ? 'danger' : 'success'; ?> btn-sm-custom">
                           <?php echo $row['status'] ? 'Block' : 'Active'; ?>
                        </a>
                        <button class="btn btn-sm btn-info btn-sm-custom" onclick='openProfile(<?php echo json_encode($row); ?>)'>Edit</button>
                        <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary btn-sm-custom" onclick="return confirm('Are you sure?')">Del</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="profileModal" class="modal" style="display:none; background:rgba(0,0,0,0.85);">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-white p-3">
        <h3 class="mb-3">Edit Profile</h3>
        <form method="POST" id="editForm">
            <input type="text" name="name" id="name" placeholder="Name">
            <input type="text" name="fname" id="fname" placeholder="Father's Name">
            <input type="text" name="mname" id="mname" placeholder="Mother's Name">
            <input type="text" name="addr" id="addr" placeholder="Address">
            <input type="text" name="mobile" id="mobile" placeholder="Mobile">
            <input type="text" name="email" id="email" placeholder="Email">
            <button type="submit" class="btn btn-success w-100">Save Changes</button>
            <button type="button" class="btn btn-danger w-100 mt-2" onclick="document.getElementById('profileModal').style.display='none'">Close</button>
        </form>
    </div></div>
</div>

<script>
    function openProfile(user) {
        document.getElementById('editForm').action = "?action=update&id=" + user.id;
        document.getElementById('name').value = user.name || '';
        document.getElementById('fname').value = user.fname || '';
        document.getElementById('mname').value = user.mname || '';
        document.getElementById('addr').value = user.addr || '';
        document.getElementById('mobile').value = user.mobile || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('profileModal').style.display = 'block';
    }
</script>
</body>
</html>
