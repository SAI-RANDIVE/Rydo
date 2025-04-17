<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../db_connect.php'; // Make sure this path is correct

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # code...
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';

    
    if ( !$email || !$password) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }
    $stmt = $conn->prepare("SELECT email, password, role FROM $role WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user && password_verify($password, $user['password'])) {
    echo json_encode([
        "success" => true,
       "message" => "Logged in as $role"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
}
$stmt->close();
$conn->close();
}else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
}

?>
