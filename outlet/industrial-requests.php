<?php
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
    echo '<div class="alert alert-danger">You do not have an outlet assigned.</div>';
    exit;
}

// Base query to fetch industrial requests for the user's outlet
$query = "SELECT r.IndustrialRequestID, r.RequestedAmount, r.PaymentStatus, r.PaymentAmount, r.Token, r.Status, r.RequestDate, r.ExpectedPickupDate,
                 o.OutletName, org.Name AS OrganizationName
          FROM industrialrequests r
          JOIN outlets o ON r.OutletID = o.OutletID
          JOIN organizations org ON r.OrganizationID = org.OrganizationID
          WHERE r.OutletID = ?
          ORDER BY r.RequestDate DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $outletID); // 'i' for integer (OutletID)
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Industrial Requests</h1>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-light table-striped">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Token</th>
                <th>Organization</th>
                <th>Outlet</th>
                <th>Requested Amount</th>
                <th>Payment Status</th>
                <th>Paid Amount</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>ExpectedPickupDate</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['IndustrialRequestID']) ?></td>
                    <td><?= htmlspecialchars($row['Token']) ?></td>
                    <td><?= htmlspecialchars($row['OrganizationName']) ?></td>
                    <td><?= htmlspecialchars($row['OutletName']) ?></td>
                    <td><?= htmlspecialchars($row['RequestedAmount']) ?></td>
                    <td><?= htmlspecialchars($row['PaymentStatus']) ?></td>
                    <td><?= ($row['PaymentAmount'] == '') ? 'Not Paid Yet' : '' ?><?= htmlspecialchars($row['PaymentAmount']) ?></td>
                    <td class="status <?= ($row['Status'] == 'Delivered') ? 'bg-success' : '' ?> <?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                    <td><?= htmlspecialchars($row['ExpectedPickupDate']) ?></td>
                    <td>
                        <form class="payment-status-form" action="backend/process-industrial-request.php" method="POST" style="display:inline;">
                            <input type="hidden" name="requestID" value="<?= $row['IndustrialRequestID'] ?>">
                            <select name="paymentStatus" class="form-select" required <?= ($row['PaymentStatus'] === 'paid') ? 'disabled' : '' ?>>
                                <option value="">Select Payment Status</option>
                                <option value="paid" <?= ($row['PaymentStatus'] === 'paid') ? 'selected' : '' ?>>Paid</option>
                                <option value="unpaid" <?= ($row['PaymentStatus'] === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                            </select>
                            <button type="submit" class="btn btn-success mt-2" <?= ($row['PaymentStatus'] === 'paid') ? 'disabled' : '' ?>>Update Payment</button>
                        </form>
                    </td>
                    <td>
                        <!-- Request Action Form -->
                        <form class="request-form" action="backend/process-industrial-request.php" method="POST" style="display:inline;">
                            <input type="hidden" name="requestID" value="<?= $row['IndustrialRequestID'] ?>">
                            <select name="action" required>
                                <option value="">Select Action</option>
                                <option value="pending">pending</option>
                                <option value="complete">Complete</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancel" <?= ($row['Status'] !== 'cancelled') ? '' : 'disabled' ?>>Cancel</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No industrial requests found for your outlet.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

<script>
    $(document).ready(function() {
        // Handle Payment Status Update
        $('form.payment-status-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this);
            var requestID = form.find('input[name="requestID"]').val();
            var paymentStatus = form.find('select[name="paymentStatus"]').val();

            if (!paymentStatus) {
                alert('Please select a payment status.');
                return;
            }

            var data = { requestID: requestID, paymentStatus: paymentStatus };

            $.ajax({
                url: 'backend/process-industrial-request.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);

                        // Dynamically update the payment status dropdown value
                        form.find('select[name="paymentStatus"]').val(response.paymentStatus);

                        // Find the closest row and update the status text in the table dynamically
                        var row = form.closest('tr');
                        row.find('.status').text(response.newStatus);  // Update the status column text dynamically

                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

        // Handle request actions (confirm, complete, cancel)
        $('form.request-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this);
            var requestID = form.find('input[name="requestID"]').val();
            var action = form.find('select[name="action"]').val();

            if (!action) {
                alert('Please select an action.');
                return;
            }

            var data = { requestID: requestID, action: action };

            $.ajax({
                url: 'backend/process-industrial-request.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);

                        // Update the request status in the table row
                        var row = form.closest('tr');
                        row.find('.status').text(response.newStatus);  // Update the status display
                    } else {
                        alert(response.message || 'Unknown error occurred.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);  // Log the raw response for debugging
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
</script>

