<?php
require '../../db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleID = intval($_POST['scheduleID'] ?? 0);
    $outletID = intval($_POST['outletID']);
    $deliveryDate = $_POST['deliveryDate'];
    $scheduledStock = intval($_POST['scheduledStock']);

    if ($outletID <= 0 || empty($deliveryDate) || $scheduledStock <= 0) {
        $response['message'] = 'Invalid input. Please provide valid data.';
    } else {
        if ($scheduleID > 0) {
            // Update existing schedule
            $stmt = $conn->prepare("
                UPDATE deliveryschedules 
                SET OutletID = ?, DeliveryDate = ?, ScheduledStock = ?
                WHERE ScheduleID = ?
            ");
            $stmt->bind_param("isii", $outletID, $deliveryDate, $scheduledStock, $scheduleID);
        } else {
            // Add new schedule
            $stmt = $conn->prepare("
                INSERT INTO deliveryschedules (OutletID, DeliveryDate, ScheduledStock, Status) 
                VALUES (?, ?, ?, 'scheduled')
            ");
            $stmt->bind_param("isi", $outletID, $deliveryDate, $scheduledStock);
        }

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = $scheduleID > 0 ? 'Schedule updated successfully.' : 'Schedule added successfully.';
        } else {
            $response['message'] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>
