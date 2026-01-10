<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "lifeledger");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get POST data safely
$name = trim($_POST['name']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = trim($_POST['password']);
$monthly_limit = isset($_POST['monthly_limit']) ? floatval($_POST['monthly_limit']) : null;

// Basic validation
if (empty($name) || empty($email) || empty($username)) {
  die("Name, Email, and Username are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die("Invalid email format.");
}

// Update query (with or without password)
if (!empty($password)) {
  // Hash the new password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, phone=?, monthly_limit=?, password=? WHERE id=?");
  $stmt->bind_param("ssssdsi", $name, $username, $email, $phone, $monthly_limit, $hashed_password, $user_id);
} else {
  $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, phone=?, monthly_limit=? WHERE id=?");
  $stmt->bind_param("ssssdi", $name, $username, $email, $phone, $monthly_limit, $user_id);
}

if ($stmt->execute()) {
  header("Location: settings.php?status=success");
  exit();
} else {
  echo "Error updating profile: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
