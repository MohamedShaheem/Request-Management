<?php
require '../../db.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['fullName']) && isset($_POST['nic']) && isset($_POST['phoneNumber']) && isset($_POST['outletID'])) {
        // Adding a new customer logic
        $fullName = trim($_POST['fullName']);
        $nic = trim($_POST['nic']);
        $phoneNumber = trim($_POST['phoneNumber']);
        $outletID = intval($_POST['outletID']);
        
        // Validate inputs
        if (empty($fullName) || empty($nic) || empty($phoneNumber) || $outletID <= 0) {
            $response['message'] = 'All fields are required and OutletID must be a positive integer.';
            echo json_encode($response);
            exit;
        }

        // Sanitize inputs
        $fullName = htmlspecialchars($fullName);
        $nic = htmlspecialchars($nic);
        $phoneNumber = htmlspecialchars($phoneNumber);

        // Check if NIC or phone number already exists
        $stmt = $conn->prepare("SELECT UserID FROM users WHERE NIC = ? OR PhoneNumber = ?");
        $stmt->bind_param("ss", $nic, $phoneNumber);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response['message'] = 'A user with this NIC or phone number already exists.';
            $stmt->close();
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        // Insert new user with role 'physical_consumer'
        $stmt = $conn->prepare("INSERT INTO users (FullName, NIC, PhoneNumber, OutletID, Role) VALUES (?, ?, ?, ?, 'physical_consumer')");

        if ($stmt) {
            $stmt->bind_param("sssi", $fullName, $nic, $phoneNumber, $outletID);

            if ($stmt->execute()) {
                $newUserID = $stmt->insert_id;
                $response['success'] = true;
                $response['message'] = 'New customer added successfully.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Failed to add the new customer: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to prepare the statement: ' . $conn->error;
        }

    }

    $conn->close();
    echo json_encode($response);
}
?>
