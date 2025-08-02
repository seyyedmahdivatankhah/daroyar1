<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = $_POST["medicine_name"];
    $dose = $_POST["dose"];
    $time = $_POST["time"];
    $duration = $_POST["duration"];
    $notes = isset($_POST["notes"]) ? $_POST["notes"] : "";

    // ذخیره اطلاعات در دیتابیس
    $stmt = $conn->prepare("INSERT INTO medicines (medicine_name, dose, time, duration, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisis", $medicine_name, $dose, $time, $duration, $notes);

    if ($stmt->execute()) {
        echo "دارو با موفقیت ذخیره شد!";
    } else {
        echo "خطا در ذخیره دارو: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
    