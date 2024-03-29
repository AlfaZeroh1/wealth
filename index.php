<?php
include "DB.php";
$liabilities = array();
$total_gross = 0;

// Get all liabilities
$stmt = $connection->query("SELECT name FROM liabilities");
$liabilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Stores liability total
$liabilities_total = array();
foreach ($liabilities as $liability) {
    $liabilities_total[$liability] = 0;
}

$months = array();

// Get all months
$stmt = $connection->query("SELECT name FROM months");
$months = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Financial Overview</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="add_gross.php">Add Income</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_liability.php">Add Liability</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <table class="table table-bordered" id="financialTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Month</th>
                    <th>Gross Income</th>
                    <th>Total Liabilities</th>
                    <?php
                    foreach ($liabilities as $liability) {
                        echo "<th>$liability</th>";
                    }
                    ?>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i=0;
                foreach ($months as $month) {
                    $i++;
                    $this_month_gross = 0;
                    $monthly_liability = array();
                    foreach ($liabilities as $liability) {
                        // Prepare and execute the SELECT query
                        $stmt = $connection->prepare("SELECT amount FROM monthly_liabilities WHERE month = :month AND liability = :liability");
                        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
                        $stmt->bindParam(':liability', $liability, PDO::PARAM_STR);
                        $stmt->execute();
                    
                        // Fetch the result
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                        // Assign the amount to $monthly_liability[$liability] or 0 if not found
                        $monthly_liability[$liability] = $result ? $result['amount'] : 0;
                        $liabilities_total[$liability] += $monthly_liability[$liability];
                    }
                    
                    // Get the months Gross
                    // Prepare and execute the SELECT query
                    $stmt = $connection->prepare("SELECT amount FROM monthly_income WHERE month = :month");
                    $stmt->bindParam(':month', $month, PDO::PARAM_STR);
                    $stmt->execute();

                    // Fetch the result
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Assign the amount to $this_month_gross
                    $this_month_gross = $result ? $result['amount'] : 0;

                    // Get the months Total Liabilities
                    // Prepare and execute the SELECT query
                    $stmt = $connection->prepare("SELECT SUM(amount) as amount FROM monthly_liabilities WHERE month = :month");
                    $stmt->bindParam(':month', $month, PDO::PARAM_STR);
                    $stmt->execute();

                    // Fetch the result
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Assign the amount to $this_month_gross
                    $this_month_ttl_liabilities = $result ? $result['amount'] : 0;
                    $this_month_net = $this_month_gross - $this_month_ttl_liabilities;
                    $this_month_net_formatted = number_format($this_month_net);
                    $total_gross += $this_month_gross;

                    echo "<tr>";
                    echo "<td>$i</td>"; // Add row number if needed
                    echo "<td>$month</td>";
                    echo "<td>$this_month_gross</td>";
                    echo "<td>$this_month_ttl_liabilities</td>";
                    foreach ($monthly_liability as $liability_amount) {
                        echo "<td>$liability_amount</td>";
                    }
                    echo "<td>$this_month_net_formatted</td>"; // Add Net amount if needed
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>#</th>
                    <th>Month</th>
                    <th>Gross Income</th>
                    <th>Total Liabilities</th>
                    <?php
                    foreach ($liabilities as $liability) {
                        echo "<th>$liability</th>";
                    }
                    ?>
                    <th>Net</th>
                </tr>
                <?php
                    echo "<tr>";
                    echo "<td>TOTAL</td>"; // Add row number if needed
                    echo "<td>&nbsp;</td>";
                    echo "<td>$total_gross</td>";
                    echo "<td>&nbsp;</td>";
                    foreach ($liabilities_total as $liability=>$amount) {
                        $amount_formatted = number_format($amount);
                        echo "<td>$amount</td>";
                    }
                    echo "<td>&nbsp;</td>"; // Add Net amount if needed
                    echo "</tr>";
                ?>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#financialTable').DataTable();
        });
    </script>
</body>
</html>
