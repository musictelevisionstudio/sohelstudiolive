<?php
// রেন্ডারের এনভায়রনমেন্ট ভেরিয়েবল থেকে তথ্য নেওয়া
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

echo "<h2>ডাটাবেস কানেকশন টেস্ট</h2>";
echo "Host: " . $host . "<br>";
echo "User: " . $user . "<br>";
echo "Database: " . $db . "<br><br>";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<b style='color:red;'>ফেইল! কানেকশন করা সম্ভব হয়নি।</b><br>";
    echo "এরর মেসেজ: " . $conn->connect_error;
} else {
    echo "<b style='color:green;'>সাকসেস! ডাটাবেস সফলভাবে কানেক্ট হয়েছে।</b><br><br>";
    
    // টেবিলগুলো দেখাচ্ছে কি না তা যাচাই করা
    $query = $conn->query("SHOW TABLES");
    echo "<b>ডাটাবেসের টেবিল তালিকা:</b><br>";
    if ($query) {
        while($row = $query->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    } else {
        echo "কোনো টেবিল পাওয়া যায়নি।";
    }
}
$conn->close();
?>

