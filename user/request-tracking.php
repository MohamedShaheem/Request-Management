<?php
// Fetch user's gas requests
$userID = $_SESSION['userID'];
$stmt = $conn->prepare("
    SELECT gr.RequestID, gr.Token, gr.ExpectedPickupDate, gr.gasType, gr.Status, gr.PaymentStatus, gr.RequestDate
    FROM gasrequests gr
    WHERE gr.UserID = ?
    ORDER BY gr.RequestDate DESC
    LIMIT 3
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user has any requests
if ($result->num_rows > 0) {
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
} else {
    $requests = [];
}

$stmt->close();
$conn->close();
?>

    <h1>Your Gas Requests Status</h1>

    <?php if (empty($requests)): ?>
        <div class="alert alert-warning">You have no gas requests yet.</div>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Gas Type</th>
                    <th>Expected Pickup Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Request Date</th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr id="request-<?php echo $request['RequestID']; ?>">
                        <td><?php echo htmlspecialchars($request['Token']); ?></td>
                        <td><?php echo htmlspecialchars($request['gasType']); ?> Kg</td>
                        <td><?php echo htmlspecialchars($request['ExpectedPickupDate']); ?></td>
                        <td id="status-<?php echo $request['RequestID']; ?>">
                            <?php echo ucfirst(htmlspecialchars($request['Status'])); ?>
                        </td>
                        <td><?php echo ucfirst(htmlspecialchars($request['PaymentStatus'])); ?></td>
                        <td><?php echo htmlspecialchars($request['RequestDate']); ?></td>
                        <!-- <td>
                            <?php if ($request['Status'] === 'pending'): ?>
                                <button class="btn btn-danger cancel-request" data-requestid="<?php echo $request['RequestID']; ?>" data-token="<?php echo $request['Token']; ?>">
                                    Cancel
                                </button>
                            <?php else: ?>
                                <span class="text-muted">Cancelled</span>
                            <?php endif; ?>
                        </td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle the cancel button click event
    $('.cancel-request').on('click', function() {
        var requestID = $(this).data('requestid');
        var token = $(this).data('token');

        // Confirm cancellation
        if (confirm('Are you sure you want to cancel this request?')) {
            // Perform AJAX request to cancel the gas request
            $.ajax({
                url: 'backend/cancel-request.php',
                type: 'POST',
                data: {
                    requestID: requestID,
                    token: token // Send the token to verify
                },
                success: function(response) {
                    // Parse the response and handle the result
                    var result = JSON.parse(response);
                    if (result.success) {
                        // Update the status and disable the cancel button
                        $('#status-' + requestID).text('Cancelled');
                        $('#request-' + requestID + ' .cancel-request').prop('disabled', true).text('Cancelled');
                    } else {
                        alert('Error: ' + result.message);
                    }
                },
                error: function() {
                    alert('An error occurred while trying to cancel the request.');
                }
            });
        }
    });
});
</script>

<style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            background-color: #fff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #3498db;
            color: white;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #ecf0f1;
        }

        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .alert {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .text-muted {
            color: #95a5a6 !important;
        }

        .cancel-request:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
