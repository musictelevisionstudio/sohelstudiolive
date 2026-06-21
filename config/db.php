<?php
// রেন্ডারের Environment Variables ব্যবহার করে কানেকশন
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // এরর থাকলে এরর দেখাও, কিন্তু এটি ব্রাউজারে আউটপুট হিসেবে যাবে
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
// খেয়াল করবেন এখানে কোনো ?> ট্যাগ নেই
