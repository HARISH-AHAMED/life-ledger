<?php
// google-login.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';        // your MySQL connection (defines $conn)
session_start();

if (!isset($_POST['credential'])) {
    header('Location: login.html');
    exit;
}

$idToken = $_POST['credential'];

try {
    // 1) Create the Google_Client
    $client = new Google_Client([
      'client_id' => '500847702976-do2ikg0c88oabjke5o0jmvrsq058inh0.apps.googleusercontent.com'
    ]);

    // 2) Override its HTTP client to use PHP streams instead of cURL
    //    this avoids the Guzzle CurlFactory bug you're seeing.
    $handlerStack = \GuzzleHttp\HandlerStack::create(
      new \GuzzleHttp\Handler\StreamHandler()
    );
    $guzzle = new \GuzzleHttp\Client([
      'handler' => $handlerStack,
      // (optional) you can increase timeouts here if you like:
      // 'timeout' => 10,
      // 'connect_timeout' => 5,
    ]);
    $client->setHttpClient($guzzle);

    // 3) Verify the ID token
    $payload = $client->verifyIdToken($idToken);
    if (!$payload) {
        throw new Exception('Invalid ID token');
    }

    // 4) Extract user info
    $email = $payload['email'];
    $name  = $payload['name'];

    // 5) Check if user exists in MySQL
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // 6) New user → insert
        $randomPassword = bin2hex(random_bytes(8));
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        $insert = $conn->prepare(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
        );
        $insert->bind_param('sss', $name, $email, $hashedPassword);
        $insert->execute();
        $userId = $insert->insert_id;
        $insert->close();
    } else {
        // 7) Existing user → fetch ID & (stored) name
        $row    = $result->fetch_assoc();
        $userId = $row['id'];
        $name   = $row['name'];
    }
    $stmt->close();

    // 8) Log in via session
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;

    // 9) Redirect to dashboard
    header('Location: dashboard.php');
    exit;

} catch (Exception $e) {
    // On any error, show a friendly message
    echo '<h3>Google login failed:</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="login.html">Back to Login</a></p>';
    exit;
}
