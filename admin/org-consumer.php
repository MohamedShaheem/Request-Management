<?php
// Fetch the role of the logged-in user (dispatch_officer)
$stmt = $conn->prepare("SELECT Role FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Check if the user is a dispatch officer
$isDispatchOfficer = ($role === 'dispatch_office');

// Get search name input
$searchName = $_GET['searchName'] ?? '';

// Base query
$query = "SELECT OrganizationID, Name, Certification, ContactPerson, ContactNumber, Email, RegistrationDate FROM organizations WHERE 1=1";

// Apply search filter if provided
if (!empty($searchName)) {
    $query .= " AND Name LIKE ?";
}

// Order by registration date
$query .= " ORDER BY RegistrationDate DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

// Bind parameters if searching by name
if (!empty($searchName)) {
    $searchName = "%$searchName%";
    $stmt->bind_param("s", $searchName);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<div class="row d-flex align-items-center justify-content-between">
<h1 class="col-auto">Organization List</h1>
    <ul class="col-auto list-unstyled mr-2">
        <li class="btn btn-warning">
            <a class="nav-link <?= $page === 'users' ? 'active' : '' ?>" href="admin-dashboard.php?page=users">Normal users</a>
        </li>
    </ul>
</div>

<div class="row w-50"> 
    <div class="col-md-5 mb-3">
        <form method="GET" action="admin-dashboard.php" class="d-flex flex-column align-items-start">
            <input type="hidden" name="page" value="org-consumer"> <!-- Ensure 'organizations' remains the active page -->

            <label for="searchName" class="mb-2">Search by Organization Name:</label>
            <input type="text" name="searchName" id="searchName" class="form-control" 
                   placeholder="Enter Organization Name" value="<?= htmlspecialchars($searchName) ?>">

            <button type="submit" class="btn btn-primary mt-2 mb-2 w-100">Search</button>
            

            <?php if (!empty($searchName)): ?>
                <a href="admin-dashboard.php?page=organizations" class="btn btn-secondary mt-2 w-100">Clear Search</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover table-striped table-light">
        <thead class="table-dark">
            <tr>
                <th>Organization ID</th>
                <th>Name</th>
                <th>Certification</th>
                <th>Contact Person</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['OrganizationID']) ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Certification'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['ContactPerson'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['RegistrationDate']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No organizations found.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

<style>
    .btn-warning{
        font-weight: 600;
        color: white;
        background-color: rgb(65, 15, 245);
        border: none;
        outline: none;
        padding: 10px;
        transition: 0.3s ease;
    }
    .btn-warning:hover{
        background-color: rgb(49, 15, 170);
        color: white;
    }
</style>