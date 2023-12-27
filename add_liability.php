<?php
include "DB.php";

// Fetch month names from the database
$stmtMonths = $connection->query("SELECT name FROM months");
$months = $stmtMonths->fetchAll(PDO::FETCH_COLUMN);

// Fetch liability names from the database
$stmtLiabilities = $connection->query("SELECT name FROM liabilities");
$liabilities = $stmtLiabilities->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $selectedMonth = $_POST['month'];
    $selectedLiability = $_POST['liability'];
    $amount = $_POST['amount'];

    try {
        // Check if a record exists for the specified month and liability
        $stmtCheck = $connection->prepare("SELECT id FROM monthly_liabilities WHERE month = :month AND liability = :liability");
        $stmtCheck->bindParam(':month', $selectedMonth, PDO::PARAM_STR);
        $stmtCheck->bindParam(':liability', $selectedLiability, PDO::PARAM_STR);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Update the existing record
            $stmtUpdate = $connection->prepare("UPDATE monthly_liabilities SET amount = :amount WHERE month = :month AND liability = :liability");
            $stmtUpdate->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':month', $selectedMonth, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':liability', $selectedLiability, PDO::PARAM_STR);
            $stmtUpdate->execute();
        } else {
            // Insert a new record
            $stmtInsert = $connection->prepare("INSERT INTO monthly_liabilities (month, liability, amount) VALUES (:month, :liability, :amount)");
            $stmtInsert->bindParam(':month', $selectedMonth, PDO::PARAM_STR);
            $stmtInsert->bindParam(':liability', $selectedLiability, PDO::PARAM_STR);
            $stmtInsert->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmtInsert->execute();
        }

        // Redirect back to the form or another page if needed
        // header("Location: add_liability.php");
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Database operation failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add or Update Liabilities</title>
</head>
<body>

<div class="container mt-5">
    <h2>Add or Update Liabilities</h2>
    <form action="" method="post">
        <div class="mb-3">
            <label for="month" class="form-label">Select Month:</label>
            <select class="form-select" id="month" name="month" required>
                <option selected disabled>Choose a month</option>
                <?php
                foreach ($months as $month) {
                    echo "<option value=\"$month\">$month</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="liability" class="form-label">Select Liability:</label>
            <select class="form-select" id="liability" name="liability" required>
                <option selected disabled>Choose a liability</option>
                <?php
                foreach ($liabilities as $liability) {
                    echo "<option value=\"$liability\">$liability</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Enter Amount:</label>
            <input type="text" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
