<?php
require '../../db.php';

$location = $_POST['location'] ?? '';
$token = $_POST['token'] ?? '';

// Build the query based on the parameters
$query = "SELECT * FROM gasrequests WHERE 1";

if ($location) {
    $query .= " AND Location = ?";
}

if ($token) {
    $query .= " AND Token LIKE ?";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);

if ($location && $token) {
    $stmt->bind_param("ss", $location, "%$token%");
} elseif ($location) {
    $stmt->bind_param("s", $location);
} elseif ($token) {
    $stmt->bind_param("s", "%$token%");
}

$stmt->execute();
$result = $stmt->get_result();
$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
$stmt->close();
?>
