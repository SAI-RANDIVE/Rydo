<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('885696492604-cqnuj7fnj7oaplnr48jf165u9jr6oj0h.apps.googleusercontent.com');
$client->setClientSecret('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'); // Your Client Secret
$client->setRedirectUri('http://localhost/Rydo/backend/auth/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['role'])) {
    $_SESSION['selected_role'] = $_GET['role'];
}

header("Location: " . $client->createAuthUrl());
exit();
?>
