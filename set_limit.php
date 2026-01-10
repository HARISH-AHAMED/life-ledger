<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include("db.php"); // ✅ Corrected include file name

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monthly_limit'])) {
    $monthly_limit = floatval($_POST['monthly_limit']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET monthly_limit = ? WHERE id = ?");
    $stmt->bind_param("di", $monthly_limit, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
