<?php
require '../../db.php';
require '../../vendor/autoload.php'; // Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Enable error reporting (for debugging purposes)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
$requestID = $_POST['requestID'] ?? null;
$outletID = $_POST['outletID'] ?? null;
$gastype = $_POST['gastype'] ?? null;
$requestedAmount = $_POST['requestedAmount'] ?? null;

if (!$requestID || !$outletID || !$requestedAmount) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// Fetch outlet details (including email)
$query = "SELECT OutletName, Email FROM outlets WHERE OutletID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $outletID);
$stmt->execute();
$outletResult = $stmt->get_result();
$outletData = $outletResult->fetch_assoc();
$stmt->close();

if (!$outletData) {
    echo json_encode(['success' => false, 'message' => 'Outlet not found.']);
    exit;
}

$outletName = $outletData['OutletName'];
$outletEmail = $outletData['Email']; // Ensure the outlets table has an 'Email' column

// Insert new schedule into deliveryschedules table
$deliveryDate = date('Y-m-d', strtotime('+7 days')); // Schedule delivery 7 days from today
$createdAt = date('Y-m-d H:i:s');
$status = 'scheduled';

$query = "
    INSERT INTO deliveryschedules (OutletID, RequestID, DeliveryDate, GasType, ScheduledStock, DeliveredStock, Status, CreatedAt)
    VALUES (?, ?, ?, ?, ?, 0, ?, ?)
";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement.']);
    exit;
}

$stmt->bind_param("iississ", $outletID, $requestID, $deliveryDate, $gastype, $requestedAmount, $status, $createdAt);

// Fetch additional users' emails for the outlet
// $query2 = "SELECT Email FROM users WHERE outletID = ?";
// $stmt2 = $conn->prepare($query2);
// $stmt2->bind_param("i", $outletID);
// $stmt2->execute();
// $result2 = $stmt2->get_result();

// $userEmails = [];
// while ($row = $result2->fetch_assoc()) {
//     $userEmails[] = $row['Email'];
// }


// Fetch users' emails for the outlet where the gas request status is 'pending'
$query2 = "SELECT u.Email 
           FROM users u
           INNER JOIN gasrequests g ON u.outletID = g.OutletID
           WHERE g.Status = 'pending' AND u.outletID = ?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("i", $outletID);
$stmt2->execute();
$result2 = $stmt2->get_result();

$userEmails = [];
while ($row = $result2->fetch_assoc()) {
    $userEmails[] = $row['Email'];
}


$stmt2->close();

if ($stmt->execute()) {
    // Update the status of the outlet request to 'scheduled'
    $updateQuery = "UPDATE outletrequests SET Status = 'scheduled' WHERE RequestID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $requestID);

    if ($updateStmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Use environment variables in production
            $mail->Password = ''; // Replace with env variable
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Send email to Outlet
            $mail->clearAddresses();
            $mail->setFrom('', 'Gas Delivery System');
            $mail->addAddress($outletEmail, $outletName);
            $mail->isHTML(true);
            $mail->Subject = "Delivery Schedule Confirmed";
            $mail->Body = "
                <h2>Schedule Confirmation</h2>
                <p>Hello $outletName,</p>
                <p>Your gas delivery request has been confirmed for outlet $outletName.</p>
                <p><strong>Request ID:</strong> $requestID</p>
                <p><strong>Gas Type:</strong> $gastype Kg</p>
                <p><strong>Requested Amount:</strong> $requestedAmount</p>
                <p><strong>Delivery Date:</strong> $deliveryDate</p>
                <p>Thank you!</p>
            ";
            $mail->send();

            // Send email to Users
            if (!empty($userEmails)) {
                $mail->clearAddresses();
                foreach ($userEmails as $email) {
                    $mail->addAddress($email);
                }
                $mail->Subject = "Hand Over Gas & Collect Payment";
                $mail->Body = "
                    <h2>Delivery Instructions</h2>
                    <p>Please hand over the Empty gas cylinder and money to your outlet.</p>
                    <p><strong>Tokan No:</strong> KANDYTOKEN84899</p>
                    <p><strong>Gas Type:</strong> $gastype Kg</p>
                    <p><strong>Delivery Date:</strong> $deliveryDate</p>
                    <p>Thank you!</p>
                ";
                $mail->send();
            }

            echo json_encode(['success' => true, 'message' => 'Delivery schedule created, request status updated, and emails sent.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Delivery scheduled, but email sending failed: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update request status.']);
    }

    $updateStmt->close();
} else {
    error_log("MySQL error: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to create delivery schedule.']);
}

$stmt->close();
$conn->close();
?>
