<?php
// Base query to fetch industrial requests for the user's outlet
$query = "SELECT r.IndustrialRequestID, r.GasType, r.RequestedAmount, r.PaymentStatus, r.PaymentAmount, r.ExpectedPickupDate, r.Token, r.Status, r.RequestDate,
                 o.OutletName, org.Name AS OrganizationName
          FROM industrialrequests r
          JOIN outlets o ON r.OutletID = o.OutletID
          JOIN organizations org ON r.OrganizationID = org.OrganizationID
          ORDER BY r.RequestDate DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
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
                <th>Gas Type</th>
                <th>Requested Amount</th>
                <th>PaymentStatus</th>
                <th>Paid Amount</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>ExpectedPickupDate</th>
      
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['IndustrialRequestID']) ?></td>
                    <td><?= htmlspecialchars($row['Token']) ?></td>
                    <td><?= htmlspecialchars($row['OrganizationName']) ?></td>
                    <td><?= htmlspecialchars($row['OutletName']) ?></td>
                    <td><?= htmlspecialchars($row['GasType']) ?> Kg</td>
                    <td><?= htmlspecialchars($row['RequestedAmount']) ?></td>
                    <td><?= htmlspecialchars($row['PaymentStatus']) ?></td>
                    <td><?= ($row['PaymentAmount'] == '') ? 'Not Paid Yet' : '' ?> <?= htmlspecialchars($row['PaymentAmount']) ?> LKR</td>
                    <td class="status <?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                    <td><?= htmlspecialchars($row['ExpectedPickupDate']) ?></td>
                
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
