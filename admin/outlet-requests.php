<?php

// Query to fetch outlet requests
$query = "
    SELECT r.RequestID, r.GasType, r.RequestAmount, r.RequestDate, r.Status, 
           o.OutletID, o.OutletName
    FROM outletrequests r
    JOIN outlets o ON r.OutletID = o.OutletID
    ORDER BY r.RequestDate DESC
";

// Execute the query
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Outlet Requests</h1>

<!-- Filter Dropdown -->
<div class="filter">
    <label for="statusFilter">Filter by Status: </label>
    <select id="statusFilter" class="form-select">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="scheduled">Scheduled</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<!-- Table for displaying requests -->
<?php if ($result->num_rows > 0): ?>
    <table class="table table-light table-striped">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Outlet Name</th>
                <th>Gas Type</th>
                <th>Requested Amount</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="requestTableBody">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="requestRow" data-status="<?= strtolower($row['Status']) ?>">
                    <td><?= htmlspecialchars($row['RequestID']) ?></td>
                    <td><?= htmlspecialchars($row['OutletName']) ?></td>
                    <td><?= htmlspecialchars($row['GasType']) ?></td>
                    <td><?= htmlspecialchars($row['RequestAmount']) ?></td>
                    <td class="status <?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                    <td>
                        <form class="delivery-schedule-form" action="backend/make-delivery-schedule.php" method="POST" style="display:inline;">
                            <input type="hidden" name="requestID" value="<?= $row['RequestID'] ?>">
                            <input type="hidden" name="outletID" value="<?= $row['OutletID'] ?>">
                            <input type="hidden" name="gastype" value="<?= $row['GasType'] ?>">
                            <input type="hidden" name="requestedAmount" value="<?= $row['RequestAmount'] ?>">
                            <button type="submit" class="btn <?= strtolower($row['Status']) === 'pending' ? 'btn-primary' : 'btn-success disabled' ?>"><?= strtolower($row['Status']) === 'pending' ? 'Make a Delivery Schedule' : 'Scheduled' ?></button>
                        </form>
                        <form class="cancel-request-form" action="backend/cancel-request.php" method="POST" style="display:inline;">
                            <input type="hidden" name="requestID" value="<?= $row['RequestID'] ?>">
                            <input type="hidden" name="outletID" value="<?= $row['OutletID'] ?>">
                            <button type="submit" class="btn <?= strtolower($row['Status']) === 'pending' ? 'btn-danger' : 'btn-danger disabled' ?>" <?= strtolower($row['Status']) !== 'pending' ? 'disabled' : '' ?>>Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No outlet requests found.</div>
<?php endif; ?>


<?php
$stmt->close();
$conn->close();
?>

<script>
$(document).ready(function() {
    // Handle delivery schedule creation
    $('form.delivery-schedule-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var form = $(this);
        var data = form.serialize(); // Serialize form data

        $.ajax({
            url: 'backend/process-outlet-requests.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert(response.message || 'Unknown error occurred.');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText); // Log the raw response for debugging
                alert('An error occurred: ' + error);
            }
        });
    });
});


$(document).ready(function() {
    // Handle cancellation of request
    $('form.cancel-request-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var form = $(this);
        var data = form.serialize(); // Serialize form data

        $.ajax({
            url: 'backend/cancel-request.php', // The PHP script that handles cancellation
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert(response.message || 'Unknown error occurred.');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText); // Log the raw response for debugging
                alert('An error occurred: ' + error);
            }
        });
    });
});

$(document).ready(function() {
    // Filter the table based on selected status
    $('#statusFilter').change(function() {
        var selectedStatus = $(this).val().toLowerCase();

        // Show all rows if "All" is selected
        if (selectedStatus === "") {
            $('.requestRow').show();
        } else {
            // Hide rows that don't match the selected status
            $('.requestRow').each(function() {
                var rowStatus = $(this).data('status').toLowerCase();
                if (rowStatus === selectedStatus) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Default action on page load (filter by "Pending" status)
    $('#statusFilter').val('pending').trigger('change');
});


</script>
