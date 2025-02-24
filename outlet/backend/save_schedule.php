<?php
require '../../db.php';
header('Content-Type: application/json');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userID = $_SESSION['userID'];

// Fetch the user's assigned outlet
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

if (!$outletName) {
    echo json_encode(['success' => false, 'message' => 'You are not assigned to an outlet.']);
    exit;
}

// Check if form data is received via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gasType = $_POST['gasType'] ?? null;
    $requestAmount = $_POST['requestAmount'] ?? null;
    $requestDate = $_POST['requestDate'] ?? null;

    if (!$gasType || !$requestAmount || !$requestDate) {
        echo json_encode(['success' => false, 'message' => 'Gas type, request amount, and date are required.']);
        exit;
    }

    // Check if the requested gas type is available in the main stock
    $stmt = $conn->prepare("SELECT quantity FROM mainstock WHERE type = ?");
    $stmt->bind_param("s", $gasType);
    $stmt->execute();
    $stmt->bind_result($availableStock);
    $stmt->fetch();
    $stmt->close();

    if ($availableStock === null) {
        echo json_encode(['success' => false, 'message' => 'Requested gas type is not available in the main stock.']);
        exit;
    } elseif ($availableStock < $requestAmount) {
        echo json_encode(['success' => false, 'message' => "Not enough stock available at the moment please re-submmit your request after 24 Hours.<br> Or you can make a request below {$availableStock} gas<br>Currently available stocks amount: {$availableStock}."]);
        exit;
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert data into the outletrequests table
        $query = "INSERT INTO outletrequests (OutletID, GasType, RequestAmount, RequestDate, Status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isds", $outletID, $gasType, $requestAmount, $requestDate);
        $stmt->execute();
        $stmt->close();

        // Reduce the stock in the mainstock table
        $updateStockQuery = "UPDATE mainstock SET quantity = quantity - ? WHERE type = ?";
        $stmt = $conn->prepare($updateStockQuery);
        $stmt->bind_param("ds", $requestAmount, $gasType);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Request submitted successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to submit the request: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
?>
