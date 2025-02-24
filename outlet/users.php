<?php
// Fetch outletID for the logged-in user
$stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

// Check if the user has an outlet assigned
if (!isset($outletID) || $outletID == 0) {
    echo '<div class="alert alert-danger">You do not have an outlet assigned.</div>';
    exit;
}

// Get search phone number input and role filter
$searchPhone = $_GET['searchPhone'] ?? '';
$roleFilter = $_GET['role'] ?? 'consumer';  // Default to 'consumer' if no role is selected

// Base query
$query = "SELECT UserID, FullName, Email, PhoneNumber, NIC, Role, CreatedAt 
          FROM users WHERE outletID = ? AND Role = ?";

// Apply phone number search if provided
if (!empty($searchPhone)) {
    $query .= " AND PhoneNumber LIKE ?";
}

$query .= " ORDER BY CreatedAt DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

if (!empty($searchPhone)) {
    $searchPhone = "%$searchPhone%";
    $stmt->bind_param("iss", $outletID, $roleFilter, $searchPhone);
} else {
    $stmt->bind_param("is", $outletID, $roleFilter);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Outlet User List</h1>

<div class="row w-50"> 
    <div class="col-md-5 mb-3">
        <form method="GET" action="outlet-dashboard.php" class="d-flex flex-column align-items-start">
            <input type="hidden" name="page" value="users"> <!-- Ensure 'users' remains the active page -->
            
            <label for="searchPhone" class="mb-2">Search by Phone Number:</label>
            <input type="text" name="searchPhone" id="searchPhone" class="form-control" 
                   placeholder="Enter Phone Number" value="<?= htmlspecialchars($searchPhone) ?>">

            <label for="role" class="mt-3 mb-2">Filter by Role:</label>
            <select name="role" id="role" class="form-control">
                <option value="consumer" <?= ($roleFilter == 'consumer') ? 'selected' : '' ?>>Consumer</option>
                <option value="physical_consumer" <?= ($roleFilter == 'physical_consumer') ? 'selected' : '' ?>>Physical Consumer</option>
            </select>

            <button type="submit" class="btn btn-primary mt-2 w-100">Search</button>
            
            <?php if (!empty($searchPhone)): ?>
                <a href="outlet-dashboard.php?page=users" class="btn btn-secondary mt-2 w-100">Clear Search</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover table-striped table-light">
        <thead class="table-dark">
            <tr>
                <th>User ID</th>
                <th>User Type</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>NIC</th>
                <th>Registered Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['UserID']) ?></td>
                    <td><?= htmlspecialchars($row['Role']) ?></td>
                    <td><?= htmlspecialchars($row['FullName']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                    <td><?= htmlspecialchars($row['NIC']) ?></td>
                    <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No users found for this outlet.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
