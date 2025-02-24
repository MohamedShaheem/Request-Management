<h1 class="mb-4">Dispatch Officer Dashboard</h1>

<div class="mb-4">
    <label for="locationFilter" class="form-label">Filter by Location:</label>
    <select id="locationFilter" class="form-select">
        <option value="">-- Select Location --</option>
        <?php
        // Fetch distinct locations from the database
        require '../db.php';
        $locationsQuery = "SELECT DISTINCT Location FROM outlets ORDER BY Location";
        $locationsResult = $conn->query($locationsQuery);

        while ($locationRow = $locationsResult->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($locationRow['Location']) . "'>" . htmlspecialchars($locationRow['Location']) . "</option>";
        }
        ?>
    </select>
</div>

<div id="requestsContainer">
    <div class="alert alert-info">Please select a location to view requests.</div>
</div>
</div>

<script>
$(document).ready(function () {
    $('#locationFilter').change(function () {
        const selectedLocation = $(this).val();

        if (selectedLocation) {
            $.ajax({
                url: 'backend/fetch-requests.php',
                type: 'POST',
                data: {location: selectedLocation},
                dataType: 'json',
                success: function (data) {
                    let html = '';

                    if (data.length > 0) {
                        html += `<h2 class='mt-4'>Location: ${selectedLocation}</h2>`;
                        html += `
                            <table class='table table-light table-striped'>
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Request Type</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th>Expected/Requested Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        data.forEach(row => {
                            if (row.GasRequestID) {
                                html += `
                                    <tr>
                                        <td>${row.GasRequestID}</td>
                                        <td>Gas Request</td>
                                        <td>
                                            Token: ${row.Token}<br>
                                            Payment: ${row.PaymentStatus}<br>
                                            Returned: ${row.Returned}
                                        </td>
                                        <td>${row.GasRequestStatus}</td>
                                        <td>${row.ExpectedPickupDate}</td>
                                    </tr>
                                `;
                            }
                        });

                        html += '</tbody></table>';
                    } else {
                        html = '<div class="alert alert-warning">No requests found for this location.</div>';
                    }

                    $('#requestsContainer').html(html);
                },
                error: function () {
                    $('#requestsContainer').html('<div class="alert alert-danger">An error occurred while fetching data.</div>');
                }
            });
        } else {
            $('#requestsContainer').html('<div class="alert alert-info">Please select a location to view requests.</div>');
        }
    });
});
</script>
