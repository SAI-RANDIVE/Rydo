<?php
// Accept JSON input
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$name = $data['name'];
$profile_pic = $data['profile_pic'];

// You can verify the token with Google here (optional but recommended)
// Then check user type from your DB

// Connect to database
$conn = new mysqli("localhost", "root", "", "rydo_db");

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Check in customers table
$customerQuery = $conn->prepare("SELECT * FROM users WHERE email = ?");
$customerQuery->bind_param("s", $email);
$customerQuery->execute();
$customerResult = $customerQuery->get_result();

if ($customerResult->num_rows > 0) {
    echo json_encode(["success" => true, "role" => "customer"]);
    exit;
}

// Check in drivers table
$driverQuery = $conn->prepare("SELECT * FROM drivers WHERE email = ?");
$driverQuery->bind_param("s", $email);
$driverQuery->execute();
$driverResult = $driverQuery->get_result();

if ($driverResult->num_rows > 0) {
    echo json_encode(["success" => true, "role" => "driver"]);
    exit;
}

// If user not found in either table
echo json_encode(["success" => false, "message" => "User not registered as customer or driver"]);
?>
