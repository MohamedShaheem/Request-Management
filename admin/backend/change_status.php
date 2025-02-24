<?php
require '../../db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$scheduleID = intval($data['scheduleID'] ?? 0);
$status = $data['status'] ?? '';

$validStatuses = ['scheduled', 'delivered', 'cancelled'];

if ($scheduleID > 0 && in_array($status, $validStatuses)) {
    if ($status === 'delivered') {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Get the scheduled stock, outlet, and gas type
            $stmt = $conn->prepare("SELECT OutletID, ScheduledStock, DeliveredStock, GasType FROM deliveryschedules WHERE ScheduleID = ?");
            $stmt->bind_param("i", $scheduleID);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedule = $result->fetch_assoc();

            if (!$schedule) {
                throw new Exception('Schedule not found.');
            }

            $outletID = $schedule['OutletID'];
            $scheduledStock = $schedule['ScheduledStock'];
            $deliveredStock = $schedule['DeliveredStock'];
            $gasType = floatval($schedule['GasType']); // Convert gas type to float to match column names

            if ($scheduledStock <= 0) {
                throw new Exception('No stock scheduled for delivery.');
            }

            // Check if the gas type column exists in the outlets table
            $validGasTypes = [22.5, 12.5, 5, 2.3]; // Define valid gas types matching column names
            if (!in_array($gasType, $validGasTypes)) {
                throw new Exception('Invalid gas type.');
            }

            // Update the stock values in deliveryschedules
            $newDeliveredStock = $deliveredStock + $scheduledStock;
            $stmt = $conn->prepare("
                UPDATE deliveryschedules 
                SET Status = ?, ScheduledStock = 0, DeliveredStock = ? 
                WHERE ScheduleID = ?
            ");
            $stmt->bind_param("sii", $status, $newDeliveredStock, $scheduleID);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update schedule: ' . $conn->error);
            }

            // Update the corresponding gas type column in the outlet stock
            $column = "`" . $gasType . "`"; // Ensure the column name is safely formatted
            $stmt = $conn->prepare("
                UPDATE outlets 
                SET $column = $column + ?, LastRestocked = NOW() 
                WHERE OutletID = ?
            ");
            $stmt->bind_param("ii", $scheduledStock, $outletID);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update outlet stock: ' . $conn->error);
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Schedule marked as delivered, and stock updated successfully.']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        // Handle other status changes (scheduled or cancelled)
        $stmt = $conn->prepare("UPDATE deliveryschedules SET Status = ? WHERE ScheduleID = ?");
        $stmt->bind_param("si", $status, $scheduleID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $conn->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}

$conn->close();
?>
