<?php
require '../../db.php'; // Include database connection
session_start();

header('Content-Type: text/html; charset=UTF-8');

// Ensure user is logged in
if (!isset($_SESSION['OrganizationID'])) {
    echo '<div class="alert alert-danger">Session expired. Please log in again.</div>';
    exit;
}

$organizationID = $_SESSION['OrganizationID']; // Organization ID from session

// Get form data
$gasType = $_POST['gasType'];
$pickupDate = $_POST['pickupDate'];
$outletID = isset($_POST['outlet']) ? intval($_POST['outlet']) : null;
$requestedAmount = isset($_POST['requestedAmount']) ? floatval($_POST['requestedAmount']) : null;

if (!$outletID || !$requestedAmount || !$gasType || !$pickupDate) {
    echo '<div class="alert alert-danger">All fields are required. Please provide valid data.</div>';
    exit;
}

// Fetch current stock of the selected outlet for the requested gas type
$stockQuery = "SELECT `$gasType` FROM outlets WHERE OutletID = ?";
$stmt = $conn->prepare($stockQuery);
$stmt->bind_param("i", $outletID);
$stmt->execute();
$stmt->bind_result($currentStock);
$stmt->fetch();
$stmt->close();

// Check if outlet exists and stock is available
if ($currentStock === null) {
    echo '<div class="alert alert-danger">Invalid outlet selected.</div>';
    exit;
} elseif ($requestedAmount > $currentStock) {
    echo '<div class="alert alert-danger">Request amount exceeds available stock. Current stock amount: ' . $currentStock . '.</div>';
    exit;
}

// Generate a unique token in the format "BTOKEN12345"
$randomNumbers = mt_rand(10000, 99999);
$token = "BTOKEN" . $randomNumbers;

// Start transaction
$conn->begin_transaction();

try {
    // Insert the request into `industrialrequests` table
    $query = "INSERT INTO industrialrequests (OrganizationID, OutletID, RequestedAmount, Status, RequestDate, ExpectedPickupDate, GasType, Token) 
              VALUES (?, ?, ?, 'pending', NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        throw new Exception('Failed to prepare the request.');
    }

    $stmt->bind_param('iidsss', $organizationID, $outletID, $requestedAmount, $pickupDate, $gasType, $token);
    $stmt->execute();
    $stmt->close();

    // Reduce the stock in `outlets` table for the requested gas type
    $updateStockQuery = "UPDATE outlets SET `$gasType` = `$gasType` - ? WHERE OutletID = ?";
    $stmt = $conn->prepare($updateStockQuery);
    $stmt->bind_param("di", $requestedAmount, $outletID);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo '<div class="alert alert-success">Request submitted successfully. Your token is: ' . htmlspecialchars($token) . '</div>';
} catch (Exception $e) {
    // Rollback transaction on failure
    $conn->rollback();
    echo '<div class="alert alert-danger">Failed to process request: ' . $e->getMessage() . '</div>';
}

// Close connection
$conn->close();
?>