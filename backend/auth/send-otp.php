<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';  // Change this
        $mail->Password = 'your-email-password';  // Use an App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Rydo Authentication');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP for Rydo Login';
        $mail->Body = "Your OTP is: $otp";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "OTP Sent Successfully"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}
?>
