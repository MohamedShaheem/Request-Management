<?php
header('Content-Type: application/json');

session_start();
require '../../db.php';

// Initialize the user session and fetch outletID for the logged-in user
$userID = $_SESSION['userID'];
$outletID = null;

// Fetch outletID for the logged-in user (outlet manager)
$stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

// Check if the user has an outlet assigned
if ($outletID == 0) {
    echo json_encode(["success" => false, "message" => "You do not have an outlet assigned."]);
    exit;
}

$response = ["success" => false, "message" => "An unknown error occurred."];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $nic = $_POST['nic'];
    $email = $_POST['email'] ?: 'No Email';
    $phoneNumber = $_POST['phoneNumber'];
    $gasType = $_POST['gasType'];
    $pickupDate = $_POST['pickupDate'];
    $outletID = $_POST['outletID'];

    // Validate gas type
    $validGasTypes = ['22.5', '12.5', '5', '2.3'];
    if (!in_array($gasType, $validGasTypes)) {
        echo json_encode(["success" => false, "message" => "Invalid gas type selected."]);
        exit;
    }

    // Validate pickup date (must be within the next two weeks)
    $currentDate = new DateTime();
    $maxPickupDate = (clone $currentDate)->modify('+2 weeks');
    $pickupDateObj = new DateTime($pickupDate);

    if ($pickupDateObj < $currentDate || $pickupDateObj > $maxPickupDate) {
        echo json_encode(["success" => false, "message" => "Pickup date must be within the next two weeks."]);
        exit;
    }

    // Fetch outlet details
    $stmt = $conn->prepare("
        SELECT o.OutletID, o.OutletName, o.`$gasType` 
        FROM users u
        INNER JOIN outlets o ON u.outletID = o.OutletID
        WHERE u.UserID = ?
    ");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($outletttID, $outletName, $currentStockk);
    $stmt->fetch();
    $stmt->close();

    // Start transaction
    $conn->begin_transaction();
    try {

        if (empty($email)) {
            $email = 'No Email';
        }

        if (empty($nic)){
            $nic = "No NIC";
        }
        // Check if NIC already exists
        $stmt = $conn->prepare("SELECT UserID FROM users WHERE NIC = ?");
        $stmt->bind_param("s", $nic);
        $stmt->execute();
        $stmt->bind_result($existingUserID);
        $stmt->fetch();
        $stmt->close();

        if ($existingUserID) {
            // Update existing user's details
            $stmt = $conn->prepare("UPDATE users SET FullName = ?, outletID = ?, Email = ?, PhoneNumber = ? WHERE UserID = ?");
            $stmt->bind_param("sissi", $fullName, $outletID, $email, $phoneNumber, $existingUserID);
            $stmt->execute();
            $stmt->close();
            $userID = $existingUserID; // Use existing user ID
        } else {
            // Insert new customer
            $stmt = $conn->prepare("INSERT INTO users (FullName, outletID, Email, PhoneNumber, NIC, Role) VALUES (?, ?, ?, ?, ?, 'physical_consumer')");
            $stmt->bind_param("sisss", $fullName, $outletID, $email, $phoneNumber, $nic);
            $stmt->execute();
            $userID = $stmt->insert_id; // Get the newly inserted UserID
            $stmt->close();
        }

        // Generate a unique token for the request
        $tname = substr($outletName, 0, 1); // Gets the first letter
        $tokenPart = 'P-TOKEN';
        $randomNumbers = mt_rand(10000, 99999);
        $token = $tname . $tokenPart . $randomNumbers;

        // Insert gas request
        $stmt = $conn->prepare("INSERT INTO gasrequests (UserID, OutletID, Token, GasType, ExpectedPickupDate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $userID, $outletID, $token, $gasType, $pickupDate);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        $response = [
            "success" => true,
            "message" => "Customer record updated successfully. Gas request created with token: " . htmlspecialchars($token)
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $response = ["success" => false, "message" => "An error occurred: " . $e->getMessage()];
    }

    $conn->close();
} else {
    $response = ["success" => false, "message" => "Invalid request."];
}

echo json_encode($response);
?>
