<?php
session_start();

// تنظیمات دیتابیس
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'medical_app';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "خطا در اتصال به دیتابیس: " . $e->getMessage()]));
}

// تابع ارسال پاسخ JSON
function sendResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// اعتبارسنجی ایمیل
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// اعتبارسنجی شماره موبایل
function isValidPhone($phone) {
    return preg_match("/^09[0-9]{9}$/", $phone);
}

// ثبت‌نام
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($full_name) || empty($phone) || empty($email) || empty($password)) {
        sendResponse("error", "لطفاً همه فیلدها را پر کنید.");
    }

    if (!isValidEmail($email)) {
        sendResponse("error", "ایمیل وارد شده معتبر نیست.");
    }

    if (!isValidPhone($phone)) {
        sendResponse("error", "شماره موبایل معتبر نیست. باید با 09 شروع شود.");
    }

    try {
        // بررسی وجود ایمیل یا شماره موبایل
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email OR phone_number = :phone");
        $stmt->execute(['email' => $email, 'phone' => $phone]);

        if ($stmt->rowCount() > 0) {
            sendResponse("error", "ایمیل یا شماره موبایل قبلاً ثبت شده است.");
        }

        // ثبت کاربر جدید
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, phone_number, email, password) VALUES (:full_name, :phone, :email, :password)");
        $stmt->execute(['full_name' => $full_name, 'phone' => $phone, 'email' => $email, 'password' => $hashedPassword]);

        sendResponse("success", "ثبت‌نام با موفقیت انجام شد!");
    } catch (PDOException $e) {
        sendResponse("error", "خطا در ثبت‌نام: " . $e->getMessage());
    }
}

// ورود
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $user_input = trim($_POST['user_input']);
    $password = $_POST['password'];

    if (empty($user_input) || empty($password)) {
        sendResponse("error", "لطفاً همه فیلدها را پر کنید.");
    }

    try {
        // بررسی ایمیل یا شماره موبایل
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :user_input OR phone_number = :user_input");
        $stmt->execute(['user_input' => $user_input]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            sendResponse("success", "خوش آمدید، {$user['full_name']}!");
        } else {
            sendResponse("error", "نام کاربری یا رمز عبور اشتباه است.");
        }
    } catch (PDOException $e) {
        sendResponse("error", "خطا در ورود: " . $e->getMessage());
    }
}

sendResponse("error", "درخواست نامعتبر.");
?>
