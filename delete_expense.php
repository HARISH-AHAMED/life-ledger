<?php
// Include your database connection
include 'db.php'; // Adjust this if needed

// Check if 'expense_id' is passed via POST
if (isset($_POST['expense_id'])) {
    // Sanitize the input
    $expense_id = intval($_POST['expense_id']);

    // Prepare and execute the SQL DELETE statement
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $expense_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Expense deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete expense."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No expense ID provided."]);
}
?>
