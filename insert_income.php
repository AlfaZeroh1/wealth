<?php

// Include the database connection file
include 'DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $month = $_POST['month'];
    $amount = $_POST['amount'];

    try {
        // Check if a record exists for the specified month
        $stmt_check = $connection->prepare("SELECT id FROM monthly_income WHERE month = :month");
        $stmt_check->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt_check->execute();

        // If a record exists, update it; otherwise, insert a new record
        if ($stmt_check->rowCount() > 0) {
            $stmt_update = $connection->prepare("UPDATE monthly_income SET amount = :amount WHERE month = :month");
            $stmt_update->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt_update->bindParam(':month', $month, PDO::PARAM_STR);
            $stmt_update->execute();
        } else {
            $stmt_insert = $connection->prepare("INSERT INTO monthly_income (month, amount) VALUES (:month, :amount)");
            $stmt_insert->bindParam(':month', $month, PDO::PARAM_STR);
            $stmt_insert->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt_insert->execute();
        }

        // Redirect back to the form or another page if needed
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        echo "Database operation failed: " . $e->getMessage();
    }
}
?>
