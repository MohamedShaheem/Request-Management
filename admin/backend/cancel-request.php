<?php
require '../../db.php';
require '../../vendor/autoload.php'; // Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Validate input
$requestID = $_POST['requestID'] ?? null;
$outletID = $_POST['outletID'] ?? null;

if (!$requestID || !$outletID) {
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

// Update the request status to 'cancelled'
$updateQuery = "UPDATE outletrequests SET Status = 'cancelled' WHERE RequestID = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("i", $requestID);

if ($updateStmt->execute()) {
    // Send email to Outlet about cancellation
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = ''; // Use environment variables in production -> need to add an email
        $mail->Password = ''; // Replace with env variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Send email to Outlet
        $mail->clearAddresses();
        $mail->setFrom('', 'Gas Delivery System');
        $mail->addAddress($outletEmail, $outletName);
        $mail->isHTML(true);
        $mail->Subject = "Request Cancellation Confirmation";
        $mail->Body = "
            <h2>Request Cancellation</h2>
            <p>Hello $outletName,</p>
            <p>Your gas delivery request has been cancelled.</p>
            <p><strong>Request ID:</strong> $requestID</p>
            <p>For further information please contact the dispatch office.</p>
            <p>Thank you for your understanding.</p>
        ";
        $mail->send();

        echo json_encode(['success' => true, 'message' => 'Request cancelled and email sent.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Request cancelled, but email sending failed: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update request status to cancelled.']);
}

$updateStmt->close();
$conn->close();
?>
