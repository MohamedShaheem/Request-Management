<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'dispatch_office') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Fetch user information
$userID = $_SESSION['userID']; 
$stmt = $conn->prepare("SELECT FullName FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();
$stmt->close();

// Fetch stock data
$result = $conn->query("SELECT * FROM mainstock");

// Handle Stock Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gas_id'], $_POST['quantity'], $_POST['action'])) {
    $id = $_POST['gas_id'];
    $quantity = (int)$_POST['quantity'];
    $action = $_POST['action'];

    // Fetch current stock
    $stmt = $conn->prepare("SELECT quantity FROM mainstock WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentQuantity);
    $stmt->fetch();
    $stmt->close();

    if ($action === "add") {
        $newQuantity = $currentQuantity + $quantity;
    } elseif ($action === "reduce" && $quantity <= $currentQuantity) {
        $newQuantity = $currentQuantity - $quantity;
    } else {
        $_SESSION['message'] = "Error: Cannot reduce stock below 0.";
        $_SESSION['msg_type'] = "danger";
        header("Location: stock-management.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE mainstock SET quantity = ?, last_updated = NOW() WHERE id = ?");
    $stmt->bind_param("ii", $newQuantity, $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Stock updated successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating stock: " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    }
    $stmt->close();
    header("Location: stock-management.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f9;
        }
        .navbar {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            margin-top: 20px;
        }
        .btn {
            transition: all 0.3s ease-in-out;
        }
        .btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Stock Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="stock-management.php">Manage Stocks</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="stock/gas-price.php">Gas Price</a></li> -->
                <!-- <li class="nav-item"><a class="nav-link" href="stock/add-stock.php">Add Type</a></li> -->
            </ul>
            <span class="navbar-text text-light me-3">Welcome, <?php echo htmlspecialchars($fullName); ?></span>
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <h2 class="text-center mb-4">Gas Cylinder Inventory</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['last_updated']; ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="gas_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="quantity" class="form-control d-inline w-50" placeholder="Qty" required>
                                <button type="submit" name="action" value="add" class="btn btn-sm btn-success">Add</button>
                                <button type="submit" name="action" value="reduce" class="btn btn-sm btn-danger">Reduce</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Price List -->
<div class="price-list container mt-5 mb-5">
    <h4>Per Gas Price List</h4>
    <table class="table table-bordered table-striped">
    <thead class="table-dark">
            <tr>
                <th class="gas-type">Gas Type</th>
                <th>Price (LKR)</th>
                <th>Last Updated</th>
                <th>Action</th> <!-- Added Action column -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch data from outletstockprice table
$sql = "SELECT id, Type, Price, last_updated FROM outletstockprice";
$result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // Output each row from the query result
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='gas-type'>" . htmlspecialchars($row["Type"]) . " Kg</td>";
                    echo "<td>" . number_format($row["Price"]) . " LKR</td>";
                    echo "<td>" . ($row["last_updated"]) . "</td>";
                    echo "<td>";
                    echo "<button class='btn btn-primary btn-sm btn-update' data-id='" . $row["id"] . "' data-price='" . $row["Price"] . "'>Update Price</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Price Update Modal (hidden by default) -->
<div id="update-modal" class="modal fade" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Gas Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="stock/gas-price.php">
                    <div class="mb-3">
                        <label for="new_price" class="form-label">New Price (LKR):</label>
                        <input type="number" class="form-control" id="new_price" name="new_price" required>
                        <input type="hidden" id="id" name="id">
                    </div>
                    <button type="submit" class="btn btn-success" name="update_price">Update</button>
                    <button type="button" class="btn btn-secondary" id="close-modal" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript to Handle Modal -->
<script>
    // Get elements
    const updateButtons = document.querySelectorAll('.btn-update');
    const modal = new bootstrap.Modal(document.getElementById('update-modal'));
    const newPriceInput = document.getElementById('new_price');
    const idInput = document.getElementById('id');

    // Show modal when update button is clicked
    updateButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            const price = e.target.getAttribute('data-price');

            // Populate modal with current price
            newPriceInput.value = price;
            idInput.value = id;

            // Show the modal
            modal.show();
        });
    });
</script>
</body>
</html>

