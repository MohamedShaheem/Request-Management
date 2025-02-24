<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'consumer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require '../../db.php';

// Ensure the request is made through POST and contains the required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestID'], $_POST['token'])) {
    $requestID = intval($_POST['requestID']);
    $token = $_POST['token'];
    $userID = $_SESSION['userID'];

    // Check if the provided token matches the request's token and the status is pending
    $stmt = $conn->prepare("
        SELECT Token, Status
        FROM gasrequests
        WHERE RequestID = ? AND UserID = ? AND Status = 'pending'
    ");
    $stmt->bind_param("ii", $requestID, $userID);
    $stmt->execute();
    $stmt->bind_result($dbToken, $status);
    $stmt->fetch();
    
    if ($dbToken !== $token) {
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }

    // Update the request status to 'cancelled'
    $stmt->close();
    $stmt = $conn->prepare("
        UPDATE gasrequests
        SET Status = 'cancelled'
        WHERE RequestID = ? AND UserID = ?
    ");
    $stmt->bind_param("ii", $requestID, $userID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel the request']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
