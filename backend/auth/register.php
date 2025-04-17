<?php
session_start();
require '../config.php'; // Database connection

if (!isset($_SESSION['email']) || !isset($_GET['role'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$email = $_SESSION['email'];
$name = $_SESSION['name'];
$google_id = $_SESSION['user_id'];
$profile_picture = $_SESSION['profile_picture'];
$role = $_GET['role'];

// Ensure no duplicate in the other table
if ($role === "users") {
    $check_driver = $conn->prepare("SELECT id FROM drivers WHERE email = ?");
    $check_driver->bind_param("s", $email);
    $check_driver->execute();
    $check_driver->store_result();
    if ($check_driver->num_rows > 0) {
        die("Error: Email already registered as a driver.");
    }

    $stmt = $conn->prepare("INSERT INTO users (google_id, name, email, profile_picture, role) VALUES (?, ?, ?, ?, 'customer')");
    $stmt->bind_param("ssss", $google_id, $name, $email, $profile_picture);
    $stmt->execute();

    $_SESSION['role'] = "users";
    header("Location: ../frontend/customer_dashboard.html");
} else {
    $check_customer = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $check_customer->bind_param("s", $email);
    $check_customer->execute();
    $check_customer->store_result();
    if ($check_customer->num_rows > 0) {
        die("Error: Email already registered as a customer.");
    }

    $stmt = $conn->prepare("INSERT INTO Drivers (google_id, name, email, profile_picture, role) VALUES (?, ?, ?, ?, 'driver')");
    $stmt->bind_param("ssss", $google_id, $name, $email, $profile_picture);
    $stmt->execute();

    $_SESSION['role'] = "driver";
    header("Location: ../frontend/driver_dashboard.html");
}
exit();
?>
