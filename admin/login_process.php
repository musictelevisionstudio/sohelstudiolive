<?php
session_start();
require_once '../config/db.php';
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identity = $_POST['user_identity'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? OR email = ? OR phone_number = ?");
    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if ($password == $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_logged_in'] = true;
            
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['msg'] = "ERROR: INVALID PASSWORD!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['msg'] = "ERROR: USER NOT FOUND!";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>