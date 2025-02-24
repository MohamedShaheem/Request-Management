<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'dispatch_office') {
    header('Location: ../login.php');
    exit;
}
require '../../db.php';

// Fetch user information
$userID = $_SESSION['userID'];
$stmt = $conn->prepare("SELECT FullName FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();
$stmt->close();

// Fetch gas types from database
$gasTypes = [];
$result = $conn->query("SELECT id, type, quantity FROM mainstock");
while ($row = $result->fetch_assoc()) {
    $gasTypes[$row['id']] = $row;
}

// Get selected gas ID from URL if present
$selectedGasID = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedGasType = $selectedGasID && isset($gasTypes[$selectedGasID]) ? $gasTypes[$selectedGasID]['type'] : '';
$selectedQuantity = $selectedGasID && isset($gasTypes[$selectedGasID]) ? $gasTypes[$selectedGasID]['quantity'] : '';

// Handle Stock Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gas_id'], $_POST['quantity'])) {
    $id = $_POST['gas_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE mainstock SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Stock updated successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating stock: " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    }
    $stmt->close();
    header("Location: ../stock-management.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: hsla(240, 2%, 12%, 0.178);">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="template.php">Stock Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="../admin-dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="../stock-management.php">View Stocks</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="add-stock.php">Add Stocks</a></li> -->
                <!-- <li class="nav-item"><a class="nav-link active" href="update-stock.php">Update Stocks</a></li> -->
            </ul>
            <span class="navbar-text me-3">
                Welcome, <span class="fw-bolder text-light"><?php echo htmlspecialchars($fullName); ?></span>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
   
    <h2>Update Stock</h2>
    <form method="POST" class="border p-4 rounded bg-white shadow-sm">
        <div class="mb-3">
            <label for="gas_id" class="form-label">Select Gas Type</label>
            <select id="gas_id" name="gas_id" class="form-select" required>
                <option value="">-- Select Gas Type --</option>
                <?php foreach ($gasTypes as $id => $gas): ?>
                    <option value="<?php echo $id; ?>" <?php echo ($selectedGasID == $id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($gas['type']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">New Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="<?php echo $selectedQuantity; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Stock</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
