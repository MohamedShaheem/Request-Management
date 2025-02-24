<h1>Track Industrial Requests</h1>

<!-- Industrial Requests Tracking Table -->
<table class="table table-light table-striped">
    <thead>
        <tr>
            <th>Request ID</th>
            <th>Token</th>
            <th>Organization</th>
            <th>Outlet</th>
            <th>GasType</th>
            <th>Requested Amount</th>
            <th>Payment Status</th>
            <th>Payment Amount(LKR)</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Pickup Date</th>
        </tr>
    </thead>
    <tbody id="industrialRequestsTableBody">
        <!-- Rows will be dynamically populated -->
    </tbody>
</table>

<script>
$(document).ready(function() {
    function fetchIndustrialRequests() {
        $.ajax({
            url: 'backend/fetch-user-requests.php',
            type: 'GET',
            success: function(response) {
                console.log('Response:', response); // Log the response to debug
                const data = response; // Assuming response is already in JSON format
                const tableBody = $('#industrialRequestsTableBody');
                tableBody.empty();

                if (data.requests && data.requests.length > 0) {
                    data.requests.forEach(request => {
                        const row = `
                            <tr>
                                <td>${request.IndustrialRequestID}</td>
                                <td>${request.Token}</td>
                                <td>${request.OrganizationName}</td>
                                <td>${request.OutletName}</td>
                                <td>${request.GasType}</td>
                                <td>${request.RequestedAmount}</td>
                                <td>${request.PaymentStatus}</td>
                                <td>${request.PaymentAmount}</td>
                                <td>${request.Status}</td>
                                <td>${request.RequestDate}</td>
                                <td>${request.ExpectedPickupDate}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append('<tr><td colspan="6">No industrial requests found.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching requests:', status, error); // Log error
                alert('Failed to fetch industrial requests.');
            }
        });
    }

    fetchIndustrialRequests();
});
</script>
<style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            max-width: 1500px;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
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
        @media (max-width: 1650px) {
            table{
                font-size: smaller;
            }
            .container {
            max-width: 1400px;
        
        }
        }
    </style>
