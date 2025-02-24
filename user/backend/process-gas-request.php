<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'consumer') {
    echo 'You must be logged in as a consumer to make a request.';
    exit;
}

require '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $pickupDate = $_POST['pickupDate'];
    $gasType = $_POST['gasType']; // Gas type: '22.5', '12.5', '5', or '2.3'

    // Validate gas type
    $validGasTypes = ['22.5', '12.5', '5', '2.3'];
    if (!in_array($gasType, $validGasTypes)) {
        echo '<div class="alert alert-danger">Invalid gas type selected.</div>';
        exit;
    }

    // LIMITING REQUESTS 2 PER MONTH //
    $query5 = "SELECT COUNT(*) AS requestCount FROM gasrequests WHERE UserID = ? AND Status = 'pending' AND MONTH(RequestDate) = MONTH(NOW()) AND YEAR(RequestDate) = YEAR(NOW())";
    $stmt5 = $conn->prepare($query5);
    $stmt5->bind_param("i", $userID);
    $stmt5->execute();
    $result5 = $stmt5->get_result();
    $row5 = $result5->fetch_assoc();

    if ($row5['requestCount'] >= 2) {
        echo '<div class="alert alert-danger">You can only make 2 requests per month.</div>';
        exit;
    }
    // LIMITING REQUEST END //


    // Fetch the user's assigned outlet and stock for the selected gas type
    $stmt = $conn->prepare("
        SELECT o.OutletID, o.OutletName, o.`$gasType` 
        FROM users u
        INNER JOIN outlets o ON u.outletID = o.OutletID
        WHERE u.UserID = ?
    ");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($outletID, $outletName, $currentStock);
    $stmt->fetch();
    $stmt->close();

    if (!$outletID || !$outletName) {
        echo '<div class="alert alert-danger">You are not assigned to a valid outlet. Please contact an administrator.</div>';
        exit;
    }

    if ($currentStock <= 0) {
        echo '<div class="alert alert-danger">Sorry, your assigned outlet is out of stock for this gas type.</div>';
        exit;
    }

    // Validate the pickup date (must be within the next two weeks)
    $currentDate = new DateTime();
    $maxPickupDate = (clone $currentDate)->modify('+2 weeks');
    $pickupDateObj = new DateTime($pickupDate);

    if ($pickupDateObj < $currentDate || $pickupDateObj > $maxPickupDate) {
        echo '<div class="alert alert-danger">Pickup date must be within the next two weeks.</div>';
        exit;
    }

    // Generate a unique token for the request
    $tokenPart = 'TOKEN';
    $randomNumbers = mt_rand(10000, 99999);
    $token = strtoupper($outletName) . $tokenPart . $randomNumbers;

    // Use a transaction to ensure atomicity
    $conn->begin_transaction();
    try {
        // Insert the gas request
        $stmt = $conn->prepare("
            INSERT INTO gasrequests (UserID, OutletID, Token, GasType, ExpectedPickupDate) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $userID, $outletID, $token, $gasType, $pickupDate);
        $stmt->execute();
        $stmt->close();

        // Deduct stock from the relevant gas type
        $stmt = $conn->prepare("
            UPDATE outlets 
            SET `$gasType` = `$gasType` - 1 
            WHERE OutletID = ? AND `$gasType` > 0
        ");
        $stmt->bind_param("i", $outletID);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception('Stock update failed. Please try again.');
        }

        $stmt->close();
        $conn->commit();

        echo '<div class="alert alert-success">Gas request created successfully. Your token is: ' . htmlspecialchars($token) . '</div>';
    } catch (Exception $e) {
        $conn->rollback();
        echo '<div class="alert alert-danger">An error occurred: ' . $e->getMessage() . '</div>';
    }

    $conn->close();
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
