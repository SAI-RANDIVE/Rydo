<?php
session_start();
require '../config.php'; // Database connection

header("Content-Type: application/json");

// Get the Google user details
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['name'])) {
    echo json_encode(["success" => false, "message" => "Google login failed."]);
    exit();
}

$email = $data['email'];
$name = $data['name'];
$google_id = $data['google_id'];
$profile_picture = $data['profile_picture']; 

// Check if the email exists in customers table
$stmt = $conn->prepare("SELECT id, role FROM customers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $role);
    $stmt->fetch();
    $stmt->close();

    $_SESSION['user_id'] = $id;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'customer';
    $_SESSION['profile_picture'] = $profile_picture;

    echo json_encode(["success" => true, "redirect" => "http://localhost/Rydo/frontend/customer_dashboard.html"]);
    exit();
}

// Check if the email exists in drivers table
$stmt = $conn->prepare("SELECT id, role FROM drivers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $role);
    $stmt->fetch();
    $stmt->close();

    $_SESSION['user_id'] = $id;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'driver';
    $_SESSION['profile_picture'] = $profile_picture;

    echo json_encode(["success" => true, "redirect" => "http://localhost/Rydo/frontend/driver_dashboard.html"]);
    exit();
}

// If the email is not found in either table, register the user in the correct table
$userRole = $_SESSION['selected_role'] ?? '';

if ($userRole === 'customer') {
    $stmt = $conn->prepare("INSERT INTO customers (name, email, google_id, profile_picture) VALUES (?, ?, ?, ?)");
} elseif ($userRole === 'driver') {
    $stmt = $conn->prepare("INSERT INTO drivers (name, email, google_id, profile_picture) VALUES (?, ?, ?, ?)");
} else {
    echo json_encode(["success" => false, "message" => "Invalid role selection."]);
    exit();
}

$stmt->bind_param("ssss", $name, $email, $google_id, $profile_picture);
if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $userRole;

    $redirectUrl = ($userRole === 'customer') ? "http://localhost/Rydo/frontend/customer_dashboard.html" : "http://localhost/Rydo/frontend/driver_dashboard.html";
    echo json_encode(["success" => true, "redirect" => $redirectUrl]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to register user."]);
}

$stmt->close();
$conn->close();
?>
