<?php
require '../../db.php';

header('Content-Type: application/json');

$scheduleID = intval($_GET['scheduleID'] ?? 0);

if ($scheduleID > 0) {
    $stmt = $conn->prepare("SELECT * FROM deliveryschedules WHERE ScheduleID = ?");
    $stmt->bind_param("i", $scheduleID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'schedule' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Schedule not found.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Schedule ID.']);
}
$conn->close();
?>
