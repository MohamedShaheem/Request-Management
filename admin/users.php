<?php
// Fetch the role of the logged-in user (dispatch_officer)
$stmt = $conn->prepare("SELECT Role, outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($role, $outletID);
$stmt->fetch();
$stmt->close();

// Check if the user is a dispatch officer
$isDispatchOfficer = ($role === 'dispatch_office');

// Get search phone number input and role filter
$searchPhone = $_GET['searchPhone'] ?? '';
$roleFilter = $_GET['role'] ?? 'consumer';  // Default to 'consumer' if no role is selected

// Base query
$query = "SELECT UserID, FullName, Email, PhoneNumber, NIC, Role, CreatedAt FROM users";

// If the logged-in user is a dispatch officer, exclude 'dispatch_officer' and 'outlet_manager'
if ($isDispatchOfficer) {
    $query .= " WHERE Role NOT IN ('dispatch_officer', 'outlet_manager')";
} else {
    // If it's not a dispatch officer, filter by outletID
    $query .= " WHERE outletID = ?";
}

// Apply role filter if provided (check for 'all' and exclude it in the query)
if ($roleFilter !== 'all') {
    $query .= " AND Role = ?";
}

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

if ($isDispatchOfficer) {
    // Bind parameters for dispatch officer case (no need to bind outletID)
    if (!empty($roleFilter) && !empty($searchPhone) && $roleFilter !== 'all') {
        $searchPhone = "%$searchPhone%";
        $stmt->bind_param("ss", $roleFilter, $searchPhone);
    } elseif (!empty($roleFilter) && $roleFilter !== 'all') {
        $stmt->bind_param("s", $roleFilter);
    } elseif (!empty($searchPhone)) {
        $searchPhone = "%$searchPhone%";
        $stmt->bind_param("s", $searchPhone);
    }
} else {
    // Bind parameters for non-dispatch officer case (bind outletID first)
    if (!empty($roleFilter) && !empty($searchPhone) && $roleFilter !== 'all') {
        $searchPhone = "%$searchPhone%";
        $stmt->bind_param("iss", $outletID, $roleFilter, $searchPhone);
    } elseif (!empty($roleFilter) && $roleFilter !== 'all') {
        $stmt->bind_param("is", $outletID, $roleFilter);
    } elseif (!empty($searchPhone)) {
        $searchPhone = "%$searchPhone%";
        $stmt->bind_param("is", $outletID, $searchPhone);
    } else {
        $stmt->bind_param("i", $outletID);
    }
}

$stmt->execute();
$result = $stmt->get_result();

?>
<div class="row d-flex align-items-center justify-content-between">
    <h1 class="col-auto">User List</h1>
    <ul class="col-auto list-unstyled mr-2">
        <li class="btn btn-warning">
            <a class="nav-link <?= $page === 'org-consumer' ? 'active' : '' ?>" href="admin-dashboard.php?page=org-consumer">Business users</a>
        </li>
    </ul>
</div>



<div class="row w-50"> 
    <div class="col-md-5 mb-3">
        <form method="GET" action="admin-dashboard.php" class="d-flex flex-column align-items-start">
            <input type="hidden" name="page" value="users"> <!-- Ensure 'users' remains the active page -->
            
            <label for="searchPhone" class="mb-2">Search by Phone Number:</label>
            <input type="text" name="searchPhone" id="searchPhone" class="form-control" 
                   placeholder="Enter Phone Number" value="<?= htmlspecialchars($searchPhone) ?>">

            <label for="role" class="mt-3 mb-2">Filter by Role:</label>
            <select name="role" id="role" class="form-control">
                <option value="all" <?= ($roleFilter === 'all') ? 'selected' : '' ?>>All</option>
                <option value="consumer" <?= ($roleFilter == 'consumer') ? 'selected' : '' ?>>Consumer</option>
                <option value="physical_consumer" <?= ($roleFilter == 'physical_consumer') ? 'selected' : '' ?>>Physical Consumer</option>
            </select>
            
            <button type="submit" class="btn btn-primary mt-2 mb-2 w-100">Search</button>
            
            
            <?php if (!empty($searchPhone)): ?>
                <a href="admin-dashboard.php?page=users" class="btn btn-secondary mt-2 w-100">Clear Search</a>
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
    <div class="alert alert-info">No users found.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
<style>
    .btn-warning{
        font-weight: 600;
        color: white;
        background-color: rgb(26, 171, 58);
        border: none;
        outline: none;
        padding: 10px;
        transition: 0.3s ease;
    }
    .btn-warning:hover{
        background-color: rgb(14, 151, 42);
        color: white;
    }
</style>