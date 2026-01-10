<?php

// Example function to get expenses
function getExpenses($user_id) {
    global $conn;
    $sql = "SELECT * FROM expenses WHERE user_id = ?";  // Use user_id instead of id
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    return $expenses;
}

// Example function to get total expenses
function getTotalExpenses($user_id) {
    global $conn;
    $sql = "SELECT SUM(amount) AS total FROM expenses WHERE user_id = ?";  // Corrected user_id
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?: 0;  // Return 0 if no expenses
}

// Example function to get income (Replace with actual logic to fetch income)
function getIncome($user_id) {
    // Assume this function fetches the user's income from the database
    return 5000; // Replace with actual income data from the database
}

?>
