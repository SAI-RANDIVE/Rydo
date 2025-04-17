<?php
session_start();
require '../config.php'; // Database connection
require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use Google\Service\Oauth2;

$client = new Client();
$client->setClientId('569447237841-fb0m5m39mrefrl9pgh6r4v78f3m6js7o.apps.googleusercontent.com');
$client->setClientSecret('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'); // ⚠️ Replace manually
$client->setRedirectUri('http://localhost/Rydo/backend/auth/google-callback.php');
$client->addScope(Oauth2::USERINFO_EMAIL);
$client->addScope(Oauth2::USERINFO_PROFILE);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth2 = new Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $google_id = $userInfo->id;
        $name = $userInfo->name;
        $email = $userInfo->email;
        $profile_picture = $userInfo->picture;

        $_SESSION['user_id'] = $google_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['profile_picture'] = $profile_picture;

        // Check if the email exists in customers or drivers table
        $stmt = $conn->prepare("
            SELECT role FROM users WHERE email = ? 
            UNION ALL 
            SELECT role FROM drivers WHERE email = ?
        ");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($role);
            $stmt->fetch();
            $_SESSION['role'] = $role; // Store role in session

            // Redirect to the correct dashboard
            if ($role === 'users') {
                header("Location: http://localhost/Rydo/frontend/customer_dashboard.html");
            } else {
                header("Location: http://localhost/Rydo/frontend/driver_dashboard.html");
            }
        } else {
            // If user is new, send them to index.html for role selection
            $_SESSION['google_id'] = $google_id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['profile_picture'] = $profile_picture;

            header("Location: http://localhost/Rydo/frontend/index.html");
        }

        exit();
    }
}

// Redirect to login if authentication fails
header("Location: http://localhost/Rydo/frontend/login.html");
exit();
?>
