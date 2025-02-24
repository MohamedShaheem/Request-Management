<?php
if (!isset($_SESSION['userID'])) {
    echo 'You must be logged in to view the delivery schedule.';
    exit;
}


$userID = $_SESSION['userID'];

// Fetch the user's assigned outlet(s)
$stmt = $conn->prepare("
    SELECT o.OutletID, o.OutletName 
    FROM users u
    INNER JOIN outlets o ON u.outletID = o.OutletID
    WHERE u.UserID = ?
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID, $outletName);
$stmt->fetch();
$stmt->close();

if (!$outletID) {
    echo '<div class="alert alert-danger">No outlet assigned to this user.</div>';
    exit;
}

// Fetch delivery schedules for the user's outlet
$stmt = $conn->prepare("
    SELECT ScheduleID, DeliveryDate, GasType, ScheduledStock, DeliveredStock, Status, CreatedAt 
    FROM deliveryschedules
    WHERE OutletID = ?
    ORDER BY DeliveryDate ASC
");
$stmt->bind_param("i", $outletID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-warning">No delivery schedules found for your assigned outlet.</div>';
    exit;
}

echo "<h1>Delivery Schedules for Outlet: " . htmlspecialchars($outletName) . "</h1>";
echo "<table class='table table-light table-striped'>";
echo "<thead>
        <tr>
            <th>Schedule ID</th>
            <th>Delivery Date</th>
            <th>Gas Type</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
      </thead>";
echo "<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['ScheduleID']) . "</td>
            <td>" . htmlspecialchars($row['DeliveryDate']) . "</td>
            <td>" . htmlspecialchars($row['GasType']) . "</td>
            <td>" . htmlspecialchars($row['Status']) . "</td>
            <td>" . htmlspecialchars($row['CreatedAt']) . "</td>
          </tr>";
}

echo "</tbody>";
echo "</table>";

$stmt->close();
$conn->close();
?>
