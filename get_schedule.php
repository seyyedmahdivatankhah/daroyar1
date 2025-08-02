<?php
// اتصال به پایگاه داده
$host = 'localhost';
$username = 'root'; // نام کاربری دیتابیس
$password = ''; // رمز عبور دیتابیس
$dbname = 'medicine_reminder';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("اتصال به دیتابیس ناموفق بود: " . $conn->connect_error);
}

// دریافت برنامه داروها
$sql = "SELECT medicine_name, dose, time, days FROM medicines";
$result = $conn->query($sql);

$medicines = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
}

// ارسال داده‌ها به فرمت JSON
header('Content-Type: application/json');
echo json_encode($medicines);

$conn->close();
?>
