<?php
// Default query to get payment details
$sql = "SELECT * FROM payments";
$filterConditions = "";

// Check if form is submitted with date filters
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Add date filter condition to the query
    $filterConditions = " WHERE PaymentDate BETWEEN '$startDate' AND '$endDate'";
}

// Check if user type filter is set
if (isset($_POST['user_type']) && !empty($_POST['user_type'])) {
    $userType = $_POST['user_type'];
    // Add user type filter condition to the query
    $filterConditions .= $filterConditions ? " AND UserType = '$userType'" : " WHERE UserType = '$userType'";
}

// Check if outlet filter is set
if (isset($_POST['outlet_id']) && !empty($_POST['outlet_id'])) {
    $outletID = $_POST['outlet_id'];
    // Add outlet filter condition to the query
    $filterConditions .= $filterConditions ? " AND OutletID = '$outletID'" : " WHERE OutletID = '$outletID'";
}

// Query to get payment details with filters applied
$sql .= $filterConditions;
$result = $conn->query($sql);

// Calculate total sales amount
$totalSales = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalSales += $row['Amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
        }
        .form-label {
            font-weight: bold;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr {
            background-color: #f8f9fa;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .total-sales {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-section {
            background-color: #f1f3f5;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        @media (max-width: 1650px) {
            .container {
                max-width: 1200px;
            }

            .navbar-logo{
                width: 90px;
            }
            .nav-link{
                font-size: smaller;
            }

        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Sales Report</h2>

        <!-- Filter Form -->
        <div class="filter-section">
            <form method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($startDate) ? $startDate : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($endDate) ? $endDate : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select name="user_type" id="user_type" class="form-control">
                            <option value="">All</option>
                            <option value="consumer" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'consumer' ? 'selected' : ''; ?>>Consumer</option>
                            <option value="business" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'business' ? 'selected' : ''; ?>>Business</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="outlet_id" class="form-label">Outlet</label>
                        <select name="outlet_id" id="outlet_id" class="form-control">
                            <option value="">All</option>
                            <?php
                            // Fetch outlet options from the database
                            $outletQuery = "SELECT OutletID, OutletName FROM outlets";
                            $outletResult = $conn->query($outletQuery);
                            if ($outletResult->num_rows > 0) {
                                while ($outletRow = $outletResult->fetch_assoc()) {
                                    echo "<option value='" . $outletRow['OutletID'] . "' " . (isset($_POST['outlet_id']) && $_POST['outlet_id'] == $outletRow['OutletID'] ? 'selected' : '') . ">" . $outletRow['OutletName'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="admin-dashboard.php?page=report" class="btn btn-secondary">Clear Filter</a>
                </div>
            </form>
        </div>

        <!-- Total Sales -->
        <div class="total-sales">
            <h4>Total Sales: <?php echo number_format($totalSales, 2); ?> LKR</h4>
        </div>

        <!-- Sales Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>User Type</th>
                    <th>Request ID</th>
                    <th>Outlet Name</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reset the result pointer and display the data again
                $result->data_seek(0);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $paymentID = $row['PaymentID'];
                        $userType = $row['UserType'];
                        $requestID = $row['RequestID'];
                        $amount = $row['Amount'];
                        $paymentMethod = $row['PaymentMethod'];
                        $paymentDate = $row['PaymentDate'];

                        // Based on UserType, fetch details from gasrequests or industrialrequests
                        if ($userType == 'consumer') {
                            $query = "SELECT * FROM gasrequests WHERE RequestID = $requestID";
                            $detailsResult = $conn->query($query);
                            $detailsRow = $detailsResult->fetch_assoc();
                            $gasType = $detailsRow['GasType'];
                            $status = $detailsRow['Status'];
                            $outletID = $detailsRow['OutletID'];
                        } else {
                            $query = "SELECT * FROM industrialrequests WHERE IndustrialRequestID = $requestID";
                            $detailsResult = $conn->query($query);
                            $detailsRow = $detailsResult->fetch_assoc();
                            $gasType = $detailsRow['GasType'];
                            $status = $detailsRow['Status'];
                            $outletID = $detailsRow['OutletID'];
                        }

                        // Fetch outlet name from outlets table
                        $outletQuery = "SELECT OutletName FROM outlets WHERE OutletID = $outletID";
                        $outletResult = $conn->query($outletQuery);
                        $outletRow = $outletResult->fetch_assoc();
                        $outletName = $outletRow['OutletName'];

                        ?>
                        <tr>
                            <td><?php echo $paymentID; ?></td>
                            <td><?php echo $userType; ?></td>
                            <td><?php echo $requestID; ?></td>
                            <td><?php echo $outletName; ?></td>
                            <td><?php echo number_format($amount, 2); ?></td>
                            <td><?php echo $paymentMethod; ?></td>
                            <td><?php echo $paymentDate; ?></td>
                            <td>
                                <strong>Gas Type:</strong> <?php echo $gasType; ?> Kg<br>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
