<?php
include "DB.php";

// You need to fetch month names from your database here
// Select names from the months table
$stmt = $connection->query("SELECT name FROM months");
// Fetch all rows as an associative array
$months = $stmt->fetchAll(PDO::FETCH_COLUMN);

// print_r($months);die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Monthly Income Form</title>
</head>
<body>

<div class="container mt-5">
    <h2>Monthly Income Form</h2>
    <form action="insert_income.php" method="post">
        <div class="mb-3">
            <label for="month" class="form-label">Select Month:</label>
            <select class="form-select" id="month" name="month" required>
                <option selected disabled>Choose a month<option>
                <?php
                
                foreach ($months as $month) {
                    echo "<option value=\"$month\">$month</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Enter Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
