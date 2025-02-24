<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title text-center">Request Delivery Schedule</h4>
        </div>
        <div class="card-body">
            <form id="requestForm">
                <div class="mb-3">
                    <label for="gasType">Select Gas Type:</label>
                    <select name="gasType" id="gasType" class="form-select" required>
                        <option value="22.5 Kg">22.5 Kg</option>
                        <option value="12.5 Kg">12.5 Kg</option>
                        <option value="5 Kg">5 Kg</option>
                        <option value="2.3 Kg">2.3 Kg</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="requestAmount" class="form-label">Request Amount (Kg)</label>
                    <input type="number" step="0.01" class="form-control" id="requestAmount" name="requestAmount" required>
                </div>
                <div class="mb-3">
                    <label for="requestDate" class="form-label">Request Date</label>
                    <input type="date" class="form-control" id="requestDate" name="requestDate" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
            <div id="feedback" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#requestForm').on('submit', function (e) {
            e.preventDefault();

            const formData = {
                gasType: $('#gasType').val(),
                requestAmount: $('#requestAmount').val(),
                requestDate: $('#requestDate').val(),
            };

            $.ajax({
                url: 'backend/save_schedule.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $('#feedback').html(
                        `<div class="alert alert-${response.success ? 'success' : 'danger'}">${response.message}</div>`
                    );
                },
                error: function (xhr, status, error) {
                    $('#feedback').html(`<div class="alert alert-danger">An error occurred: ${error}</div>`);
                }
            });
        });
    });
</script>
<style>
    .container {
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
</style>