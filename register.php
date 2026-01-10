<?php
require 'db.php';

if (isset($_POST['register'])) {
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

  if ($conn->query($sql)) {
    echo "Registration successful! <a href='login.html'>Login here</a>";
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
