<?php
require '../../db.php';

$response = ['success' => false, 'message' => 'Unknown error'];

if (isset($_POST['requestID']) && isset($_POST['action'])) {
    $requestID = $_POST['requestID'];
    $action = $_POST['action'];

    // Check for valid actions
    if (in_array($action, ['pending','confirm', 'Delivered', 'cancel'])) {
        // Update the status of the request based on the action
        $query = "UPDATE industrialrequests SET Status = ? WHERE IndustrialRequestID = ?";
        $stmt = $conn->prepare($query);

        // Set status based on action
        switch ($action) {
            case 'pending':
                $newStatus = 'pending';
                break;
            case 'confirm':
                $newStatus = 'confirmed';
                break;
            case 'Delivered':
                $newStatus = 'Delivered to Outlet';
                break;
            case 'cancel':
                $newStatus = 'cancelled';
                break;
            default:
                $newStatus = 'pending';
                break;
        }

        $stmt->bind_param("si", $newStatus, $requestID);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Request status updated successfully.";
            $response['newStatus'] = $newStatus;
        } else {
            $response['message'] = "Failed to update the request status.";
        }
        $stmt->close();
    } else {
        $response['message'] = "Invalid action.";
    }
}

echo json_encode($response);
$conn->close();
?>
