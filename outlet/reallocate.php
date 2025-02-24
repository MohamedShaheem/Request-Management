<?php

// Initialize the user session and fetch outletID for the logged-in user
$userID = $_SESSION['userID'];
$outletID = null;

$stmt = $conn->prepare("SELECT outletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

if ($outletID == 0) {
    echo '<div class="alert alert-danger">You do not have an outlet assigned.</div>';
    exit;
}

// Fetch gas requests with status 'reallocated'
$query = "SELECT r.RequestID, r.Token, r.RequestDate, r.ExpectedPickupDate, r.Status, 
                 r.PaymentStatus, r.Returned, u.FullName, u.Email, u.PhoneNumber, u.NIC 
          FROM gasrequests r
          JOIN users u ON r.UserID = u.UserID
          WHERE r.OutletID = ? AND r.Status = 'reallocated'
          ORDER BY r.RequestDate DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $outletID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch users assigned to this outlet
$queryUsers = "SELECT UserID, FullName, PhoneNumber, NIC FROM users WHERE outletID = ? AND Role = 'physical_consumer'";
$stmt = $conn->prepare($queryUsers);
$stmt->bind_param("i", $outletID);
$stmt->execute();
$resultUsers = $stmt->get_result();
$users = $resultUsers->fetch_all(MYSQLI_ASSOC);
?>

<h1>Gas Requests (Reallocation)</h1>
<button id="addCustomerBtn" class="btn btn-primary mb-3">Add New Customer</button>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-striped table-hover table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Request ID</th>
                <th>Token</th>
                <th>Request Date</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Empty Returned</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>NIC</th>
                <th>Assign New Consumer</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['RequestID']) ?></td>
                    <td><?= htmlspecialchars($row['Token']) ?></td>
                    <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                    <td><?= htmlspecialchars($row['ExpectedPickupDate']) ?></td>
                    <td class="status <?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['PaymentStatus']) ?></td>
                    <td class="returned"><?= htmlspecialchars($row['Returned']) ?></td>
                    <td><?= htmlspecialchars($row['FullName']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                    <td><?= htmlspecialchars($row['NIC']) ?></td>
                    <td>
                        <select class="form-select change-user user-dropdown" data-request-id="<?= $row['RequestID'] ?>">
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['UserID'] ?>" data-phone="<?= $user['PhoneNumber'] ?>" data-nic="<?= $user['NIC'] ?>">
                                    <?= htmlspecialchars($user['FullName']) ?> - <?= htmlspecialchars($user['PhoneNumber']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No gas requests found with status 'Reallocated'.</div>
<?php endif; ?>

<!-- Modal -->
<div id="customerModal" class="modal fade" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name:</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nic" class="form-label">NIC:</label>
                        <input type="text" id="nic" name="nic" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number:</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                    </div>
                    <input type="hidden" id="outletID" name="outletID" value="<?= $outletID ?>">
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    $(".change-user").change(function () {
        const requestID = $(this).data("request-id");
        const userID = $(this).val();
        const userName = $(this).find("option:selected").text();

        if (userID && confirm(`Are you sure you want to assign this request to ${userName}?`)) {
            $.ajax({
                url: 'backend/reallocate-process.php',
                type: 'POST',
                data: { requestID, userID },
                success: function (response) {
                    alert(response.message);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function () {
                    alert("An error occurred while updating the request.");
                }
            });
        } else {
            $(this).val("");
        }
    });

    $("#addCustomerBtn").click(function () {
    $("#customerModal").modal("show");
});

$(".close").click(function () {
    $("#customerModal").modal("hide");
});

$("#customerForm").submit(function (event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("backend/reallocate-process.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            $("#customerModal").modal("hide");
            $("#customerForm")[0].reset();
            location.reload();
        }
    })
    .catch(error => console.error("Error:", error));
});

$(".user-dropdown").select2({
    placeholder: "Search by name, phone, or NIC",
    allowClear: true,
    templateResult: function (item) {
        if (!item.id) return item.text;

        const phone = $(item.element).data('phone') ? String($(item.element).data('phone')) : "N/A";
        const nic = $(item.element).data('nic') ? String($(item.element).data('nic')) : "N/A";

        return $('<span>' + item.text + ' (' + phone + ' - ' + nic + ')</span>');
    },
    matcher: function (params, data) {
        if ($.trim(params.term) === '') return data;

        const searchTerm = params.term.toLowerCase();
        const textMatch = data.text.toLowerCase().includes(searchTerm);

        // Ensure data is a string before using `.includes()`
        const phone = $(data.element).data('phone') ? String($(data.element).data('phone')) : "";
        const nic = $(data.element).data('nic') ? String($(data.element).data('nic')) : "";

        const phoneMatch = phone.includes(searchTerm);
        const nicMatch = nic.includes(searchTerm);

        return textMatch || phoneMatch || nicMatch ? data : null;
    }
});

});
</script>

<?php
$stmt->close();
$conn->close();
?>
