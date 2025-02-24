<?php
// Initialize the user session and fetch outletID for the logged-in user
$userID = $_SESSION['userID'];
$outletID = null;

// Fetch outletID for the logged-in user (outlet manager)
$stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

// Check if the user has an outlet assigned
if ($outletID == 0) {
    echo '<div class="alert alert-danger">You do not have an outlet assigned.</div>';
    exit;
}

// Get the status filter (default to 'all' if not set)
$statusFilter = $_GET['status'] ?? 'pending';

// Get search token input (if provided)
$searchToken = $_GET['searchToken'] ?? '';

// Base query to fetch gas requests for the user's outlet, ensuring ReallocationDate is not NULL
$query = "SELECT r.RequestID, r.Token, r.GasType, r.RequestDate, r.ExpectedPickupDate, r.Status, r.PaymentStatus, r.Returned, r.ReallocationDate,
                 u.FullName AS NewConsumerName, old_u.FullName AS OldConsumerName, u.Email, u.PhoneNumber, u.NIC 
          FROM gasrequests r
          JOIN users u ON r.UserID = u.UserID
          LEFT JOIN users old_u ON r.OldUserID = old_u.UserID
          WHERE r.OutletID = ? AND r.ReallocationDate IS NOT NULL";

// Apply token search if provided
if (!empty($searchToken)) {
    $query .= " AND r.Token LIKE ?";
}

// Apply status filter if not 'all'
if ($statusFilter !== 'all') {
    $query .= " AND r.Status = ?";
}

$query .= " ORDER BY r.RequestDate DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if (!empty($searchToken) && $statusFilter !== 'all') {
    $searchToken = "%$searchToken%";
    $stmt->bind_param("iss", $outletID, $searchToken, $statusFilter);
} elseif (!empty($searchToken)) {
    $searchToken = "%$searchToken%";
    $stmt->bind_param("is", $outletID, $searchToken);
} elseif ($statusFilter !== 'all') {
    $stmt->bind_param("is", $outletID, $statusFilter);
} else {
    $stmt->bind_param("i", $outletID);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Gas Requests</h1>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover table-striped table-light">
    <thead class="table-dark">
        <tr>
            <th>Request ID</th>
            <th>Token</th>
            <th>New Consumer Name</th>
            <th>Old Consumer Name</th>
            <th>Gas Type</th>
            <th>Request Date</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Reallocation Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['RequestID']) ?></td>
                <td><?= htmlspecialchars($row['Token']) ?></td>
                <td><?= htmlspecialchars($row['NewConsumerName']) ?></td>
                <td><?= htmlspecialchars($row['OldConsumerName']) ?></td>
                <td><?= htmlspecialchars($row['GasType']) ?>Kg</td>
                <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                <td><?= htmlspecialchars($row['ReallocationDate']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-info">No gas requests found with reallocation data.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
