<?php
session_start();
require '../../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['OutletID'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$outletID = $_SESSION['OutletID'];
$requestID = $_POST['IndustrialRequestID'];
$newStatus = $_POST['Status'];

// Validate the inputs
if (!in_array($newStatus, ['pending', 'confirmed', 'completed', 'cancelled'])) {
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

// Update the request status in the database
$query = "UPDATE industrialrequests SET Status = ? WHERE IndustrialRequestID = ? AND OutletID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('sii', $newStatus, $requestID, $outletID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update status']);
}

$stmt->close();
$conn->close();
?>
