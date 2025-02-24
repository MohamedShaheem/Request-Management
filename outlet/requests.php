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

// Get the status filter (default to 'pending' if not set)
$statusFilter = $_GET['status'] ?? 'all';

// Get the search query (can be a Request ID or Token)
$searchQuery = $_GET['searchQuery'] ?? '';

// Base query to fetch gas requests for the user's outlet
$query = "SELECT r.RequestID, r.Token, r.GasType, r.RequestDate, r.ExpectedPickupDate, r.Status, r.PaymentStatus, r.PaymentAmount, r.Returned,
                 u.FullName, u.Email, u.PhoneNumber, u.NIC 
          FROM gasrequests r
          JOIN users u ON r.UserID = u.UserID
          WHERE r.OutletID = ?";

// Apply search by Request ID or Token if provided
$params = ["i", $outletID];

if (!empty($searchQuery)) {
    $query .= " AND (r.RequestID = ? OR r.Token LIKE ?)";
    $params[0] .= "ss";
    $params[] = $searchQuery;
    $params[] = "%$searchQuery%";
}

// Apply status filter if not 'all'
if ($statusFilter !== 'all') {
    $query .= " AND r.Status = ?";
    $params[0] .= "s";
    $params[] = $statusFilter;
}

$query .= " ORDER BY r.RequestDate DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);

// Dynamically bind parameters
$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();

// Display a message if no records are found
if ($result->num_rows === 0) {
    echo "<p>No matching records found.</p>";
}
?>


<h1>Gas Requests</h1>
<button id="addCustomerBtn" type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#customerModal">
    Create Token
</button>
<div class="row w-50">
    <!-- Search Form -->
    <div class="col-md-5 mb-3">
        <form method="GET" class="d-flex flex-column align-items-start xyz">
            <label for="searchQuery" class="mb-2">Search by Request ID or Token:</label>
            <input type="text" name="searchQuery" id="searchQuery" class="form-control" placeholder="Enter Request ID or Token" value="<?= isset($_GET['searchQuery']) ? htmlspecialchars($_GET['searchQuery']) : '' ?>">
            <button type="submit" class="btn btn-primary mt-2 w-100">Search</button>
            <?php if (!empty($_GET['searchQuery'])): ?>
                <a href="outlet-dashboard.php?page=requests" class="btn btn-secondary mt-2 w-100">Clear Search</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Status Filter Form -->
    <div class="col-md-5 mb-3">
        <form method="GET" class="d-flex flex-column align-items-start xyz" style="display:inline;">
            <label for="status" class="mb-2">Filter by Status:</label>
            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                <option value="all" <?= ($statusFilter === 'all') ? 'selected' : '' ?>>All</option>
                <option value="pending" <?= ($statusFilter === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= ($statusFilter === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                <option value="completed" <?= ($statusFilter === 'completed') ? 'selected' : '' ?>>Completed</option>
                <!-- <option value="expired" <?= ($statusFilter === 'expired') ? 'selected' : '' ?>>Expired</option> -->
                <option value="reallocated" <?= ($statusFilter === 'reallocated') ? 'selected' : '' ?>>Reallocated</option>
                <option value="cancelled" <?= ($statusFilter === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </form>
    </div>
</div>


<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover table-striped table-light">
    <thead class="table-dark">
        <tr>
            <th>Request ID</th>
            <th>Token</th>
            <th>Gas Type</th>
            <th>Request Date</th>
            <th>Pickup Date</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Paid Amount</th>
            <th>Empty Returned</th>
            <th>User Name</th>
            <!-- <th>Email</th> -->
            <th>Phone</th>
            <!-- <th>NIC</th> -->
            <th>Payment</th>
            <th>Empty Cylinder</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['RequestID']) ?></td>
                <td><?= htmlspecialchars($row['Token']) ?></td>
                <td><?= htmlspecialchars($row['GasType']) ?>Kg</td>
                <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                <td><?= htmlspecialchars($row['ExpectedPickupDate']) ?></td>
                <td class="status <?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></td>
                <td><?= htmlspecialchars($row['PaymentStatus']) ?></td>
                <td><?= ($row['PaymentAmount'] == '') ? 'Not Paid Yet' : '' ?><?= htmlspecialchars($row['PaymentAmount']) ?> LKR</td>
                <td class="returned"><?= htmlspecialchars($row['Returned']) ?></td>
                <td><?= htmlspecialchars($row['FullName']) ?></td>
                <!-- <td><?= htmlspecialchars($row['Email']) ?></td> -->
                <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                <!-- <td><?= htmlspecialchars($row['NIC']) ?></td> -->
                <td>
                    <form class="payment-status-form abc" action="backend/process-request.php" method="POST" style="display:inline;">
                        <input type="hidden" name="requestID" value="<?= $row['RequestID'] ?>">
                        <select name="paymentStatus" class="form-select" required <?= ($row['PaymentStatus'] === 'paid') ? 'disabled' : '' ?>>
                            <option value="">Select Payment Status</option>
                            <option value="paid" <?= ($row['PaymentStatus'] === 'paid') ? 'selected' : '' ?>>Paid</option>
                            <option value="unpaid" <?= ($row['PaymentStatus'] === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                        </select>
                        <button type="submit" class="btn btn-success mt-2" <?= ($row['Status'] === 'completed') ? 'disabled' : '' ?>>Update Payment</button>
                    </form>
                </td>
                <td>
                    <form class="return-form abc" action="backend/process-request.php" method="POST" style="display:inline;">
                        <input type="hidden" name="requestID" value="<?= $row['RequestID'] ?>">
                        <button type="submit" class="btn btn-warning mt-2" <?= ($row['Returned'] === 'yes') ? 'disabled' : '' ?>>
                            <?= ($row['Returned'] === 'yes') ? 'Returned' : 'Return' ?>
                        </button>
                    </form>
                </td>
                <td>
                    <form class="request-form abc" action="backend/process-request.php" method="POST" style="display:inline;">
                        <input type="hidden" name="requestID" value="<?= $row['RequestID'] ?>">
                        <select name="action" class="form-select" required>
                            <option value="">Select Action</option>
                            <option value="confirm" <?= ($row['Status'] === 'pending') ? '' : 'disabled' ?>>Confirm</option>
                            <option value="complete" <?= ($row['PaymentStatus'] === 'paid' && $row['Status'] === 'confirmed') ? '' : 'disabled' ?>>Complete</option>
                            <option value="reallocate" <?= ($row['Status'] !== 'reallocated' && $row['Status'] !== 'cancelled' && $row['Status'] !== 'completed') ? '' : 'disabled' ?>>Reallocate</option>
                            <!-- <option value="expire" <?= ($row['Status'] === 'pending' || $row['Status'] === 'confirmed') ? '' : 'disabled' ?>>Expire</option> -->
                            <option value="cancel" <?= ($row['Status'] !== 'cancelled') ? '' : 'disabled' ?>>Cancel</option>
                        </select>
                        <button type="submit" class="btn btn-primary mt-2">Apply</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
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
                        <label for="phoneNumber" class="form-label">Phone Number:</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nic" class="form-label">NIC:</label>
                        <input type="text" id="nic" name="nic" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="text" id="email" name="email" class="form-control">
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
                    <input type="hidden" id="outletID" name="outletID" value="<?= $outletID ?>">
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
            <div id="responseMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<?php else: ?>
    <div class="alert alert-info">No gas requests found for your outlet.</div>
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
            url: 'backend/process-request.php',
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

    // Handle Returned status update
    $('form.return-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var form = $(this);
        var requestID = form.find('input[name="requestID"]').val();

        var data = { requestID: requestID, action: 'mark_returned' };

        $.ajax({
            url: 'backend/process-request.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);

                    // Dynamically update the returned status
                    var row = form.closest('tr');
                    row.find('.returned').text('yes');  // Update the returned status in the table

                    // Disable the "Mark Returned" button
                    form.find('button').text('Returned').prop('disabled', true);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });

    // Handle other request actions (confirm, complete, etc.)
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
            url: 'backend/process-request.php',
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
  ///////////////////////////////////////////////////////////////////  

    // Show modal when add button is clicked
    $("#customerForm").submit(function (event) {
    event.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);

    fetch("backend/physical-consumer-process.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Ensure JSON is parsed
    .then(data => {
        let messageBox = $("#responseMessage");
        
        if (data.success) {
            messageBox.html(`<div class="alert alert-success">${data.message}</div>`);
            $("#customerForm")[0].reset(); // Reset form after success
            
            setTimeout(() => {
                $("#customerModal").modal("hide"); // Close modal after delay
                location.reload(); // Reload to update UI (if necessary)
            }, 2000);
        } else {
            messageBox.html(`<div class="alert alert-danger">${data.message}</div>`);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        $("#responseMessage").html(`<div class="alert alert-danger">An error occurred. Please try again.</div>`);
    });
});



});
</script>
<style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            padding-bottom: 20px;
        }

        #addCustomerBtn{
            font-size: 15px;
            font-weight: 600;
            background-color:rgba(2, 39, 247, 0.9);


        }
        #addCustomerBtn:hover{
            font-weight: 800;
            background-color:rgba(28, 4, 240, 0.77);

        }
        .container {
            max-width: 1900px;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 15.5px;
        }
            .navbar-logo {
            width: 120px;
            height: auto; /* Maintain aspect ratio */
        }

        .navbar-nav .nav-link {
            transition: all 0.3s ease;
        }

        .main {
            transition: all 0.3s ease;
            background-color: rgba(43, 255, 0, 0.34) !important;
            border-radius: 5px;

        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .navbar-nav .nav-link.active {
            color:rgb(255, 255, 255);
            font-weight: 600;
            border-radius: 5px;
        }

        .navbar-text {
            font-size: 1.1rem;
        }

        .btn-outline-danger {
            border-radius: 5px;
            padding: 8px 16px;
        }

        .navbar-collapse {
            justify-content: flex-end;
        }
        
        /* Ensure the navbar items are properly aligned on smaller screens */
        .navbar-toggler-icon {
            background-color: #fff;
        }
        @media (max-width: 1650px) {
            .container{
                font-size: smaller;
            }
            .navbar-logo{
                width: 90px;
            }
            .nav-link{
                font-size: smaller;
            }
            .btn{
                font-size: smaller;
            }
            .abc {
                font-size: smaller;
            }
            .abc button, option {
                font-size: smaller;
            }

            .abc .form-select{
                font-size: 12px;
                width: 90px;
            }
            .xyz .form-select{
                font-size: 14px;
                width: 130px;
            }
            .xyz input {
                font-size: 14px;
                
            }

        }
        
    </style>