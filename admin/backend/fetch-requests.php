<?php
if (isset($_POST['location'])) {
    $location = $_POST['location'];
    require"../../db.php";
    $query = "
    SELECT 
        gr.RequestID AS GasRequestID, 
        gr.Token, 
        gr.RequestDate, 
        gr.ExpectedPickupDate, 
        gr.Status AS GasRequestStatus, 
        gr.PaymentStatus, 
        gr.Returned
    FROM 
        gasrequests gr
    WHERE 
        gr.OutletID IN (SELECT OutletID FROM outlets WHERE Location = ?)
    ORDER BY 
        gr.RequestDate
";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $location);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    echo json_encode($requests);
    $stmt->close();
    $conn->close();
    exit;
}
?>