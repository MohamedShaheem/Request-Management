<?php
require '../../db.php';
session_start();

// Ensure the content type is JSON
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestID = isset($_POST['requestID']) ? intval($_POST['requestID']) : null;
    $action = $_POST['action'] ?? null;
    $paymentStatus = $_POST['paymentStatus'] ?? null;

    // Validate session and user role
    if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'outlet_manager') {
        $response['message'] = 'Unauthorized access.';
        echo json_encode($response);
        exit;
    }

    // Fetch user ID and validate
    $userID = $_SESSION['userID'] ?? null;
    if (!$userID) {
        $response['message'] = 'Invalid user session.';
        echo json_encode($response);
        exit;
    }

    // Fetch the outletID for the logged-in user
    $stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
    if (!$stmt) {
        $response['message'] = 'Database error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($outletID);
    $stmt->fetch();
    $stmt->close();

    if (!$outletID) {
        $response['message'] = 'You do not have an outlet assigned.';
        echo json_encode($response);
        exit;
    }

    // Fetch the current status of the request
    $stmt = $conn->prepare("SELECT r.Status, r.PaymentStatus, r.Returned FROM gasrequests r WHERE r.RequestID = ? AND r.OutletID = ?");
    if (!$stmt) {
        $response['message'] = 'Database error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("ii", $requestID, $outletID);
    $stmt->execute();
    $stmt->bind_result($currentStatus, $currentPaymentStatus, $currentReturnedStatus);
    $stmt->fetch();
    $stmt->close();

    if (!$currentStatus) {
        $response['message'] = 'Request not found or does not belong to your outlet.';
        echo json_encode($response);
        exit;
    }
// Handle Returned Gas
    if ($action === 'mark_returned') {
        if ($currentStatus === 'pending' || $currentStatus === 'confirmed') {
            $stmt = $conn->prepare("UPDATE gasrequests SET Returned = 'yes' WHERE RequestID = ?");
            $stmt->bind_param("i", $requestID);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Cylinder marked as returned.';
                $response['newReturnStatus'] = 'yes';
            } else {
                $response['message'] = 'Failed to update return status.';
            }
            $stmt->close();
        } else {
            $response['message'] = 'Conditions not met for marking return.';
        }
        echo json_encode($response);
        exit;
    }
    

// Update Payment Status
    if ($paymentStatus) {
        // Fetch required details for inserting into payments table
        $stmt = $conn->prepare("SELECT UserID, OutletID, GasType FROM gasrequests WHERE RequestID = ?");
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
        $stmt->bind_result($userID, $outletID, $gasType);
        $stmt->fetch();
        $stmt->close();

        // Get price for the gas type
        $stmt = $conn->prepare("SELECT Price FROM outletstockprice WHERE Type = ?");
        $stmt->bind_param("s", $gasType);
        $stmt->execute();
        $stmt->bind_result($gasPrice);
        $stmt->fetch();
        $stmt->close();

        if (!$gasPrice) {
            $response['message'] = 'Gas price not found.';
            echo json_encode($response);
            exit;
        }

        // Update payment status in gasrequests table
        $stmt = $conn->prepare("UPDATE gasrequests SET PaymentStatus = ?, PaymentAmount = ? WHERE RequestID = ?");
        $stmt->bind_param("ssi", $paymentStatus, $gasPrice, $requestID);

         
        
        if ($stmt->execute()) {

             // Update Status to 'confirmed' after payment update
             $ManualStatus = "confirmed";
             $stmtconfirm = $conn->prepare("UPDATE gasrequests SET Status = ? WHERE RequestID = ?");
             $stmtconfirm->bind_param("si", $ManualStatus, $requestID);
   
             if (!$stmtconfirm->execute()) {
                 $response['message'] = 'Failed to update request status.';
                 echo json_encode($response);
                 exit;
             }
             $stmtconfirm->close();

            // Insert into payments table
            $paymentMethod = 'cash'; // Set this dynamically if needed
            $stmt = $conn->prepare("INSERT INTO payments (UserType, RequestID, UserID, OutletID, GasType, Amount, PaymentMethod) VALUES ('consumer',?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisds", $requestID, $userID, $outletID, $gasType, $gasPrice, $paymentMethod);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Payment status updated and recorded successfully.';
                $response['paymentStatus'] = $paymentStatus;
            } else {
                $response['message'] = 'Payment status updated, but failed to record payment.';
            }
        } else {
            $response['message'] = 'Failed to update payment status.';
        }
        
        $stmt->close();
        echo json_encode($response);
        exit;
    }

    

    // Handle Reallocation
    function isEligibleForReallocation($conn, $requestID) {
        $query = "SELECT Status, ExpectedPickupDate, Returned 
                  FROM gasrequests 
                  WHERE RequestID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    
        $currentDate = new DateTime();
        $expectedDate = new DateTime($result['ExpectedPickupDate']);
        $toleranceDate = $expectedDate->modify('+14 days');
    
        if ($currentDate > $toleranceDate && $result['Returned'] == 'no' && $result['Status'] == 'confirmed' || $result['Status'] == 'pending') {
            return true;
        }
        return false;
    }
    

    // Handle actions (confirm, complete, etc.)
    if ($action === 'confirm' && $currentStatus === 'pending' && $currentPaymentStatus !== 'unpaid') {
        $stmt = $conn->prepare("UPDATE gasrequests SET Status = 'confirmed' WHERE RequestID = ?");
        $stmt->bind_param("i", $requestID);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Request confirmed successfully.';
            $response['newStatus'] = 'Confirmed';
        } else {
            $response['message'] = 'Failed to confirm request.';
        }
        $stmt->close();
    } elseif ($action === 'complete' && $currentPaymentStatus !== 'unpaid' && $currentStatus === 'confirmed' && $currentReturnedStatus === 'yes') {
        $stmt = $conn->prepare("UPDATE gasrequests SET Status = 'completed' WHERE RequestID = ?");
        $stmt->bind_param("i", $requestID);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Request marked as completed.';
            $response['newStatus'] = 'Completed';
        } else {
            $response['message'] = 'Failed to complete request.';
        }
        $stmt->close();

    } elseif($action === 'reallocate' && isEligibleForReallocation($conn, $requestID)){
        $stmt = $conn->prepare("UPDATE gasrequests SET Status = 'reallocated' WHERE RequestID = ?");
        $stmt->bind_param("i", $requestID);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Request marked as reallocated.';
            $response['newStatus'] = 'Reallocated';
        } else {
            $response['message'] = 'Failed to reallocate request.';
        }
        $stmt->close();
    } elseif ($action === 'cancel' && isEligibleForReallocation($conn, $requestID)){
        $stmt = $conn->prepare("UPDATE gasrequests SET Status = 'Cancelled' WHERE RequestID = ?");
        $stmt->bind_param("i", $requestID);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Request marked as Cancelled.';
            $response['newStatus'] = 'Reallocated';
        } else {
            $response['message'] = 'Failed to cancel request.';
        }
        $stmt->close();
    }
    else {
        $response['message'] = 'Invalid action. Please check consumer payment or empty gas cylinder Status';
    }

    echo json_encode($response);


}
