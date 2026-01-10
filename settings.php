<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "lifeledger");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  echo "User not found.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Account Settings - LifeLedger</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to bottom right, #0c0c0c, #2a2a2a);
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    h4 {
      color: #DC143C;
    }
    .navbar {
      background-color: rgba(0, 0, 0, 0.85) !important;
    }
    .card {
      background: rgba(30, 30, 30, 0.9);
      padding: 20px;
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .form-control {
      background-color: rgba(60, 60, 60, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
      box-shadow: none;
    }
    .form-control:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
      background-color: rgba(70, 70, 70, 0.95);
      color: #fff;
    }
    .form-floating > label::after {
      background-color: transparent !important;
      color: #fff;
    }
    label {
      color: #fff;
    }
    .btn-primary {
      background-color: #DC143C;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #B01030;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-sm navbar-dark px-3">
  <a class="navbar-brand fw-bold text-light" href="#">LifeLedger</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <div class="navbar-nav">
      <a class="nav-link" href="index.html">Home</a>
      <a class="nav-link" href="dashboard.php">Dashboard</a>
      <a class="nav-link active" href="settings.php">Settings</a>
      <a class="nav-link" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<!-- Profile Update Form -->
<div class="container my-5">
  <div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body p-4">
      <h4 class="mb-4 fw-semibold">Account Settings</h4>

      <form method="POST" action="update_profile.php" class="row g-3">

        <!-- Full Name -->
        <div class="col-12 form-floating">
          <input type="text" name="name" id="name" class="form-control rounded-3" placeholder="Full Name" value="<?= htmlspecialchars($user['name']) ?>" required>
          <label for="name">Full Name</label>
        </div>

        <!-- Username -->
        <div class="col-12 form-floating">
          <input type="text" name="username" id="username" class="form-control rounded-3" placeholder="Username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
          <label for="username">Username</label>
        </div>

        <!-- Email -->
        <div class="col-12 form-floating">
          <input type="email" name="email" id="email" class="form-control rounded-3" placeholder="Email Address" value="<?= htmlspecialchars($user['email']) ?>" required>
          <label for="email">Email Address</label>
        </div>

        <!-- Phone -->
        <div class="col-12 form-floating">
          <input type="tel" name="phone" id="phone" class="form-control rounded-3" placeholder="Phone Number" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          <label for="phone">Phone Number</label>
        </div>

        <!-- Monthly Limit -->
        <div class="col-12 form-floating">
          <input type="number" step="0.01" name="monthly_limit" id="monthly_limit" class="form-control rounded-3" placeholder="Monthly Limit" value="<?= htmlspecialchars($user['monthly_limit'] ?? '') ?>">
          <label for="monthly_limit">Monthly Expense Limit ($)</label>
        </div>

        <!-- New Password -->
        <div class="col-12 form-floating">
          <input type="password" name="password" id="password" class="form-control rounded-3" placeholder="New Password">
          <label for="password">New Password (optional)</label>
        </div>

        <!-- Submit -->
        <div class="col-12">
          <button type="submit" class="btn btn-primary w-100 py-2 rounded-3">Save Changes</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
