<?php
require '../../db.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestID = intval($_POST['requestID'] ?? 0);

    if (isset($_POST['fullName']) && isset($_POST['nic']) && isset($_POST['phoneNumber']) && isset($_POST['outletID'])) {
        // Adding a new customer logic
        $fullName = trim($_POST['fullName']);
        $nic = trim($_POST['nic']);
        $phoneNumber = trim($_POST['phoneNumber']);
        $outletID = intval($_POST['outletID']);

        if (!$fullName || !$nic || !$phoneNumber || !$outletID) {
            $response['message'] = 'All fields are required.';
        } else {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Check if NIC exists
                $stmt = $conn->prepare("SELECT UserID FROM users WHERE NIC = ?");
                $stmt->bind_param("s", $nic);
                $stmt->execute();
                $nicResult = $stmt->get_result();
                $stmt->close();

                // Check if phone number exists
                $stmt = $conn->prepare("SELECT UserID FROM users WHERE PhoneNumber = ?");
                $stmt->bind_param("s", $phoneNumber);
                $stmt->execute();
                $phoneResult = $stmt->get_result();
                $stmt->close();

                if ($nicResult->num_rows > 0) {
                    throw new Exception('A user with this NIC already exists.');
                }
                
                if ($phoneResult->num_rows > 0) {
                    throw new Exception('A user with this phone number already exists.');
                }

                // Insert new user with role 'physical_consumer'
                $stmt = $conn->prepare("INSERT INTO users (FullName, NIC, PhoneNumber, OutletID, Email, PasswordHash, Role) VALUES (?, ?, ?, ?, '', 'None', 'physical_consumer')");
                $stmt->bind_param("sssi", $fullName, $nic, $phoneNumber, $outletID);

                if (!$stmt->execute()) {
                    throw new Exception('Failed to add the new customer: ' . $stmt->error);
                }

                $stmt->close();
                $conn->commit();
                
                $response['success'] = true;
                $response['message'] = 'New customer added successfully.';
                
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = $e->getMessage();
            }
        }
    } elseif (isset($_POST['userID'])) {
        // Reallocating the request logic
        $userID = intval($_POST['userID']);

        if (!$requestID || !$userID) {
            $response['message'] = 'Invalid input data.';
        } else {
            $conn->begin_transaction();
            
            try {
                // Fetch the current user ID for the request
                $stmt = $conn->prepare("SELECT UserID FROM gasrequests WHERE RequestID = ? FOR UPDATE");
                $stmt->bind_param("i", $requestID);
                $stmt->execute();
                $result = $stmt->get_result();
                $oldUserID = $result->fetch_assoc()['UserID'] ?? null;
                $stmt->close();

                if (!$oldUserID) {
                    throw new Exception('Request not found.');
                }

                // Fetch the new user's details
                $stmt = $conn->prepare("SELECT FullName FROM users WHERE UserID = ?");
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $result = $stmt->get_result();
                $userData = $result->fetch_assoc();
                $stmt->close();

                if (!$userData) {
                    throw new Exception('User not found.');
                }

                // Calculate the new pickup date (5 days from today)
                $newPickupDate = (new DateTime())->modify('+5 days')->format('Y-m-d');
                $reallocationDate = (new DateTime())->format('Y-m-d H:i:s');

                // Update the request with the new user
                $stmt = $conn->prepare("UPDATE gasrequests 
                                      SET UserID = ?, OldUserID = ?, ReallocationDate = ?, 
                                          Status = 'pending', ExpectedPickupDate = ? 
                                      WHERE RequestID = ?");
                $stmt->bind_param("iissi", $userID, $oldUserID, $reallocationDate, $newPickupDate, $requestID);

                if (!$stmt->execute()) {
                    throw new Exception('Failed to update the request: ' . $stmt->error);
                }

                $stmt->close();
                $conn->commit();

                $response['success'] = true;
                $response['message'] = 'Request updated successfully.';
                $response['newPickupDate'] = $newPickupDate;
                $response['newUserName'] = $userData['FullName'];
                
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = $e->getMessage();
            }
        }
    } else {
        $response['message'] = 'Invalid request data.';
    }
}

$conn->close();
echo json_encode($response);
?>