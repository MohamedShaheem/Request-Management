<?php
// Fetch available outlets
$outlets = [];
$outletResult = $conn->query("SELECT OutletID, OutletName FROM outlets");

if ($outletResult->num_rows > 0) {
    while ($row = $outletResult->fetch_assoc()) {
        $outlets[] = $row;
    }
}

// Fetch data from outletstockprice table
$sql = "SELECT Type, Price FROM outletstockprice";
$result = $conn->query($sql);
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" id="alert-message"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php elseif (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" id="alert-message"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div id="responseMessage" class="mt-3"></div>
    <h1>Request Industrial Gas</h1>

    <?php if (empty($outlets)): ?>
        <div class="alert alert-danger">No outlets available. Please try again later.</div>
    <?php else: ?>
        <form id="industrialRequestForm" method="POST">
            <div class="mb-3">
                <label for="outlet" class="form-label">Select Outlet</label>
                <select class="form-select" id="outlet" name="outlet" required>
                    <option value="" disabled selected>Choose an outlet</option>
                    <?php foreach ($outlets as $outlet): ?>
                        <option value="<?= htmlspecialchars($outlet['OutletID']) ?>">
                            <?= htmlspecialchars($outlet['OutletName']) ?> Outlet
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="gasType" class="form-label">Gas Type</label>
                <select class="form-select" id="gasType" name="gasType" required>
                    <option value="22.5">22.5 Kg</option>
                    <option value="12.5">12.5 Kg</option>
                    <option value="5">5 Kg</option>
                    <option value="2.3">2.3 Kg</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="requestedAmount" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="requestedAmount" name="requestedAmount" min="1" required>
            </div>

            <div class="mb-3">
                <label for="pickupDate" class="form-label">Expected Pickup Date</label>
                <input type="date" class="form-control" id="pickupDate" name="pickupDate" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>

        

<!-- Price List -->
<div class="price-list">
    <h4>Gas Type Price List</h4>
    <table>
        <thead>
            <tr>
                <th class="gas-type">Gas Type</th>
                <th>Price (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output each row from the query result
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='gas-type'>" . htmlspecialchars($row["Type"]) . " Kg</td>";
                    echo "<td>" . number_format($row["Price"]) . " LKR</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
    <?php endif; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#industrialRequestForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        var formData = $(this).serialize(); // Get form data

        $.ajax({
            url: 'backend/process-industrial-request.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#responseMessage').html(response); // Display the response

                // Clear the form fields if the request is successful
                $('#industrialRequestForm')[0].reset();
            },
            error: function() {
                $('#responseMessage').html('<div class="alert alert-danger">An error occurred while processing your request. Please try again.</div>');
            }
        });
    });
});
</script>
<script>
// this is for msg dispear
$(document).ready(function() {
        // If there's an alert message, hide it after 3 seconds and reload the page
        if ($('#alert-message').length) {
            setTimeout(function() {
                $('#alert-message').fadeOut('slow', function() {
                    // After fading out, reload the page
                    location.reload();
                });
            }, 200); // 3000 milliseconds = 3 seconds
        }
    });
</script>