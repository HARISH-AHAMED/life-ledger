<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['expense_amount'];
    $category = 'General'; // Since you have no category input in the form, assign a default or handle this differently
    $description = $_POST['expense_description'];
    $date = $_POST['expense_date'];
    

    // Validate fields (optional: add stricter validation)
    if (empty($amount) || empty($category) || empty($date)) {
        die("Please fill all required fields.");
    }

    // Connect to MySQL
    $conn = new mysqli("localhost", "root", "", "lifeledger");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert expense
    $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category, description, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $user_id, $amount, $category, $description, $date);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=expense_added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
