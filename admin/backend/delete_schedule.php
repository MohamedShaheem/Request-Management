<?php
require '../../db.php';

header('Content-Type: application/json');

$scheduleID = intval($_GET['scheduleID'] ?? 0);

if ($scheduleID > 0) {
    $stmt = $conn->prepare("DELETE FROM deliveryschedules WHERE ScheduleID = ?");
    $stmt->bind_param("i", $scheduleID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Schedule deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete schedule: ' . $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Schedule ID.']);
}
$conn->close();
?>
