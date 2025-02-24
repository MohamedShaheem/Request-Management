<?php
session_start();
require '../../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['OrganizationID'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$organizationID = $_SESSION['OrganizationID'];

$query = "
    SELECT 
        r.IndustrialRequestID,
        r.Token,
        r.RequestedAmount,
        r.Status,
        r.PaymentStatus,
        r.PaymentAmount,
        r.RequestDate,
        r.ExpectedPickupDate,
        r.GasType,
        o.OutletName,
        org.Name AS OrganizationName
    FROM industrialrequests r
    JOIN outlets o ON r.OutletID = o.OutletID
    JOIN organizations org ON r.OrganizationID = org.OrganizationID
    WHERE r.OrganizationID = ?
    ORDER BY r.RequestDate DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $organizationID);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode(['requests' => $requests]);

$stmt->close();
$conn->close();
?>
