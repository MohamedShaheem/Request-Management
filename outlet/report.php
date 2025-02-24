<?php
// Initialize the user session and fetch outletID for the logged-in user

$userID = $_SESSION['userID'];
$outletID = null;

$stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

// Default query to get payment details for the logged-in user's outlet
$sql = "SELECT * FROM payments WHERE OutletID = ?";
$params = [$outletID];
$paramTypes = "i";

// Check if form is submitted with date filters
if (isset($_POST['start_date']) && isset($_POST['end_date']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $sql .= " AND PaymentDate BETWEEN ? AND ?";
    $params[] = $_POST['start_date'];
    $params[] = $_POST['end_date'];
    $paramTypes .= "ss";
}

// Check if user type filter is set
if (isset($_POST['user_type']) && !empty($_POST['user_type'])) {
    $sql .= " AND UserType = ?";
    $params[] = $_POST['user_type'];
    $paramTypes .= "s";
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total sales amount
$totalSales = 0;
while ($row = $result->fetch_assoc()) {
    $totalSales += $row['Amount'];
}
?>

<!-- HTML Report Display -->
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
    <div class="container">
        <h2 class="text-center mb-4">Sales Report</h2>
        <!-- Filter Form -->
        <form method="POST">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="user_type" class="form-label">User Type</label>
                    <select name="user_type" id="user_type" class="form-control">
                        <option value="">All</option>
                        <option value="consumer">Consumer</option>
                        <option value="business">Business</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="admin-dashboard.php?page=report" class="btn btn-secondary">Clear Filter</a>
            </div>
        </form>

        <!-- Total Sales -->
        <div class="alert alert-success mt-4">
            <h4>Total Sales: <?php echo number_format($totalSales, 2); ?> LKR</h4>
        </div>

        <!-- Sales Table -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>User Type</th>
                    <th>Request ID</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['PaymentID']}</td>
                        <td>{$row['UserType']}</td>
                        <td>{$row['RequestID']}</td>
                        <td>" . number_format($row['Amount'], 2) . "</td>
                        <td>{$row['PaymentMethod']}</td>
                        <td>{$row['PaymentDate']}</td>
                    </tr>";
                }
                if ($result->num_rows === 0) {
                    echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
