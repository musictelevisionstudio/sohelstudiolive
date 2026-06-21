
<?php
/* File: admin/profile_edit.php - FINAL UPDATED & SYNCED */
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username  = $_POST['username'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone_number'];
    $password  = $_POST['new_password'];
    
    // ডাটাবেসে আপডেট করা
    $stmt = $conn->prepare("UPDATE admins SET full_name=?, username=?, email=?, phone_number=?, password=? WHERE id=?");
    $stmt->bind_param("sssssi", $full_name, $username, $email, $phone, $password, $admin_id);
    
    if ($stmt->execute()) {
        $_SESSION['msg'] = "SUCCESS: PROFILE UPDATED SUCCESSFULLY!";
    } else {
        $_SESSION['msg'] = "ERROR: FAILED TO UPDATE PROFILE.";
    }
    
    header("Location: profile_edit.php");
    exit();
}

// অ্যাডমিন ডাটা নিয়ে আসা
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #111; border: 2px solid gold; border-radius: 12px; padding: 20px; width: 95%; max-width: 400px; }
        .form-control { background: #222; border: 1px solid #444; color: #fff; padding: 10px; margin-bottom: 10px; }
        .btn-gold { background: gold; color: #000; font-weight: bold; border: none; padding: 10px; }
        label { color: gold; font-weight: bold; font-size: 12px; margin-bottom: 5px; }
        h2 { font-size: 20px; text-align: center; color: gold; margin-bottom: 20px; }
        .msg-alert { background: gold; color: #000; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>

<div class="card">
    <h2>EDIT PROFILE</h2>
    
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="msg-alert">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" required>
        
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" required>
        
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
        
        <label>Phone Number</label>
        <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($admin['phone_number'] ?? ''); ?>" required>
        
        <label>Password</label>
        <input type="text" name="new_password" class="form-control" value="<?php echo htmlspecialchars($admin['password'] ?? ''); ?>">
        
        <button type="submit" class="btn btn-gold w-100 mt-3">UPDATE PROFILE</button>
        <a href="dashboard.php" class="btn btn-outline-light w-100 mt-2">BACK</a>
    </form>
</div>

</body>
</html>