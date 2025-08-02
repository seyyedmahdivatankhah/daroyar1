<?php
// تنظیمات اتصال به دیتابیس
$host = "localhost";
$username = "your_username";
$password = "your_password";
$database = "medicine_reminder";

// اتصال به دیتابیس
$conn = new mysqli($host, $username, $password, $database);

// بررسی اتصال
if ($conn->connect_error) {
    die("اتصال ناموفق: " . $conn->connect_error);
}

// تنظیم charset به utf8 برای پشتیبانی از زبان فارسی
$conn->set_charset("utf8");
?>
