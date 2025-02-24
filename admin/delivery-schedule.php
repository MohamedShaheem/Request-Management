<?php
$query = "
    SELECT 
        deliveryschedules.*,
        outlets.OutletName 
    FROM 
        deliveryschedules 
    JOIN 
        outlets 
    ON 
        deliveryschedules.OutletID = outlets.OutletID 
    ORDER BY 
        deliveryschedules.DeliveryDate ASC
";
$result = $conn->query($query);
?>

        <h1>Manage Delivery Schedules</h1>
        
        <button class="btn btn-primary mb-3 w-25" id="addScheduleButton">Add Schedule</button>

            <div class="filter mb-3">
                <label for="statusFilter">Filter by Status: </label>
                <select id="statusFilter" class="form-select w-25">
                    <option value="">All</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>

        
        <table class="table table-light table-striped">
            <thead>
                <tr>
                    <th>Schedule ID</th>
                    <th>Request ID</th>
                    <th>Outlet Name</th>
                    <th>Delivery Date</th>
                    <th>Gas Type</th>
                    <th>Scheduled Stock</th>
                    <th>Delivered Stock</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-status="<?= strtolower($row['Status']) ?>">
                    <td><?= htmlspecialchars($row['ScheduleID']) ?></td>
                    <td><?= htmlspecialchars($row['RequestID']) ?></td>
                    <td><?= htmlspecialchars($row['OutletName']) ?></td>
                    <td><?= htmlspecialchars($row['DeliveryDate']) ?></td>
                    <td><?= htmlspecialchars($row['GasType']) ?></td>
                    <td><?= htmlspecialchars($row['ScheduledStock']) ?></td>
                    <td><?= htmlspecialchars($row['DeliveredStock']) ?></td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
                    <td>
                        <button class="btn btn-sm <?= strtolower($row['Status']) === 'delivered' ? 'btn-Secondary disabled' : 'btn-warning' ?>" onclick="editSchedule(<?= $row['ScheduleID'] ?>)">Edit</button>
                        <button class="btn btn-sm <?= strtolower($row['Status']) === 'delivered' ? 'btn-Secondary disabled' : 'btn-danger' ?>" onclick="deleteSchedule(<?= $row['ScheduleID'] ?>)">Delete</button>
                        <button class="btn btn-sm <?= strtolower($row['Status']) === 'delivered' ? 'btn-success disabled' : 'btn-primary' ?>" onclick="changeStatus(<?= $row['ScheduleID'] ?>, 'delivered')"><?= strtolower($row['Status']) === 'delivered' ? 'Delivered' : 'Mark Delivered' ?></button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>

<?php
    // Fetch outlet data for dropdown
    $outletQuery = "SELECT OutletID, OutletName FROM outlets ORDER BY OutletName ASC";
    $outletResult = $conn->query($outletQuery);
?>

<!-- Add/Edit Schedule Form -->
<div id="scheduleFormModal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="scheduleForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="scheduleID" name="scheduleID">
                    <div class="mb-3">
                        <label for="outletID" class="form-label">Outlet</label>
                        <select id="outletID" name="outletID" class="form-control" required>
                            <option value="">-- Select Outlet --</option>
                            <?php while ($outlet = $outletResult->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($outlet['OutletID']) ?>">
                                    <?= htmlspecialchars($outlet['OutletName']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deliveryDate" class="form-label">Delivery Date</label>
                        <input type="date" id="deliveryDate" name="deliveryDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="scheduledStock" class="form-label">Scheduled Stock</label>
                        <input type="number" id="scheduledStock" name="scheduledStock" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSchedule(scheduleID) {
            fetch(`backend/get_schedule.php?scheduleID=${scheduleID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const form = document.getElementById('scheduleForm');
                        form.scheduleID.value = data.schedule.ScheduleID;
                        form.outletID.value = data.schedule.OutletID;
                        form.deliveryDate.value = data.schedule.DeliveryDate;
                        form.scheduledStock.value = data.schedule.ScheduledStock;

                        new bootstrap.Modal(document.getElementById('scheduleFormModal')).show();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
        }

        function deleteSchedule(scheduleID) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                fetch(`backend/delete_schedule.php?scheduleID=${scheduleID}`, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) location.reload();
                    })
                    .catch(err => console.error(err));
            }
        }

        function changeStatus(scheduleID, status) {
            fetch('backend/change_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ scheduleID, status }),
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                })
                .catch(err => console.error(err));
        }

        document.getElementById('scheduleForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch('backend/save_schedule.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                })
                .catch(err => console.error(err));
        });

        document.getElementById('addScheduleButton').addEventListener('click', () => {
            const form = document.getElementById('scheduleForm');
            form.reset();
            form.scheduleID.value = '';
            new bootstrap.Modal(document.getElementById('scheduleFormModal')).show();
        });

        $(document).ready(function() {
            $('#statusFilter').change(function() {
                var selectedStatus = $(this).val().toLowerCase();

                if (selectedStatus === "") {
                    $('tbody tr').show(); // Show all rows in the body
                } else {
                    $('tbody tr').each(function() {
                        var rowStatus = $(this).data('status');
                        if (rowStatus === selectedStatus) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });

            $('#statusFilter').val('scheduled').trigger('change');
        });


    </script>

<style>
     @media (max-width: 1650px) {
            .container {
                max-width: 1400px;
            }

            .navbar-logo{
                width: 90px;
            }
            .nav-link{
                font-size: smaller;
            }

        }
</style>