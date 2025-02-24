<?php
require '../../db.php';

// Set JSON response header
header('Content-Type: application/json');

// Default response
$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestID = $_POST['requestID'] ?? null;
    $action = $_POST['action'] ?? null;
    $paymentStatus = $_POST['paymentStatus'] ?? null;

    if (!$requestID) {
        $response['message'] = 'Invalid request parameters.';
        echo json_encode($response);
        exit;
    }

    if ($action) {
        // Update Industrial Request Status
        if (!in_array($action, ['pending', 'confirm', 'delivered', 'complete', 'cancel'])) {
            $response['message'] = "Invalid action.";
        } else {
            $statusMap = [
                'pending' => 'pending',
                'confirm' => 'confirmed',
                'delivered' => 'delivered',
                'complete' => 'completed',
                'cancel' => 'cancelled'
            ];
            $newStatus = $statusMap[$action];

            $stmt = $conn->prepare("UPDATE industrialrequests SET Status = ? WHERE IndustrialRequestID = ?");
            $stmt->bind_param("si", $newStatus, $requestID);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => "Request status updated successfully.", 'newStatus' => $newStatus];
            } else {
                $response['message'] = "Failed to update request status.";
            }
            $stmt->close();
        }
    } elseif ($paymentStatus) {
        // Handle Payment Processing
        $stmt = $conn->prepare("SELECT OutletID, OrganizationID, GasType, RequestedAmount FROM industrialrequests WHERE IndustrialRequestID = ?");
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
        $stmt->bind_result($outletID, $userID, $gasType, $requestedAmount);
        $stmt->fetch();
        $stmt->close();

        if (!$outletID || !$gasType || !$requestedAmount) {
            $response['message'] = 'Request details not found.';
        } else {
            // Get the price per unit from outletstockprice
            $stmt = $conn->prepare("SELECT Price FROM outletstockprice WHERE Type = ?");
            $stmt->bind_param("s", $gasType);
            $stmt->execute();
            $stmt->bind_result($gasPrice);
            $stmt->fetch();
            $stmt->close();

            if (!$gasPrice) {
                $response['message'] = 'Gas price not found.';
            } else {
                // Calculate total amount
                $totalAmount = $requestedAmount * $gasPrice;

                // Update industrialrequests table
                $stmt = $conn->prepare("UPDATE industrialrequests SET PaymentStatus = ?, PaymentAmount = ? WHERE IndustrialRequestID = ?");
                $stmt->bind_param("sdi", $paymentStatus, $totalAmount, $requestID);

                if ($stmt->execute()) {
                    // Insert into payments table
                    $paymentMethod = 'cash'; // Modify as needed
                    $UserType = 'business';
                    $stmt = $conn->prepare("INSERT INTO payments (UserType, RequestID, UserID, OutletID, GasType, Amount, PaymentMethod) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("siiisis", $UserType, $requestID, $userID, $outletID, $gasType, $totalAmount, $paymentMethod);

                    if ($stmt->execute()) {
                        $response = [
                            'success' => true,
                            'message' => 'Payment status updated and recorded successfully.',
                            'paymentStatus' => $paymentStatus,
                            'newAmount' => $totalAmount
                        ];
                    } else {
                        $response['message'] = 'Payment status updated, but failed to record payment.';
                    }
                } else {
                    $response['message'] = 'Failed to update payment status.';
                }
                $stmt->close();
            }
        }
    }
}

// ✅ Send JSON Response
echo json_encode($response);
$conn->close();
?>
