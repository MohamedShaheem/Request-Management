<?php
// Fetch the user's assigned outlet
$userID = $_SESSION['userID'];

$stmt = $conn->prepare("
    SELECT o.OutletID, o.OutletName
    FROM users u
    INNER JOIN outlets o ON u.outletID = o.OutletID
    WHERE u.UserID = ?
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID, $outletName);
$stmt->fetch();
$stmt->close();

if (!$outletName) {
    echo '<div class="alert alert-danger">You are not assigned to an outlet. Please contact an administrator.</div>';
    exit;
}

// Fetch data from outletstockprice table
$sql = "SELECT Type, Price FROM outletstockprice";
$result = $conn->query($sql);
?>
<div class="container">
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" id="alert-message"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php elseif (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" id="alert-message"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div id="responseMessage" class="mt-3"></div>

    <h1>Request Gas</h1>
    <form id="gasRequestForm" method="POST">
        <div class="mb-3">
            <label for="outlet" class="form-label">Assigned Outlet</label>
            <input type="hidden" class="form-control" id="outletID" name="outletID" value="<?= htmlspecialchars($outletID) ?>" disabled>
            <input type="text" class="form-control" id="outlet" name="outlet" value="<?= htmlspecialchars($outletName) ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="gasType" class="form-label">Gas Type</label>
            <select class="form-control" id="gasType" name="gasType" required>
                <option value="22.5">22.5 Kg</option>
                <option value="12.5">12.5 Kg</option>
                <option value="5">5 Kg</option>
                <option value="2.3">2.3 Kg</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="pickupDate" class="form-label">Expected Pickup Date</label>
            <input type="date" class="form-control" id="pickupDate" name="pickupDate" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
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
    </form>

 
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// AJAX form submission
$(document).ready(function() {
    $('#gasRequestForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = $(this).serialize();

        $.ajax({
            url: 'backend/process-gas-request.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#responseMessage').html(response).fadeIn();

                // Hide the message after 3 seconds and reload
                setTimeout(function() {
                    $('#responseMessage').fadeOut('slow', function() {
                        location.reload();
                    });
                }, 2000);
            },
            error: function() {
                $('#responseMessage').html('<div class="alert alert-danger">An error occurred while processing your request. Please try again.</div>').fadeIn();
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
