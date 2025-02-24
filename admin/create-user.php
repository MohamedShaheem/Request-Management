<?php

$outletQuery = "SELECT OutletID, OutletName FROM outlets";
$outletResult = $conn->query($outletQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $outletID = $_POST['outletID'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $nic = $_POST['nic'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = "outlet_manager";
    $createdAt = date("Y-m-d H:i:s");

    $sql = "INSERT INTO users (FullName, outletID, Email, PhoneNumber, NIC, PasswordHash, Role, CreatedAt) 
            VALUES ('$fullname', '$outletID', '$email', '$phone', '$nic', '$password', '$role', '$createdAt')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Outlet Manager created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
$conn->close();
?>


    <div class="row d-flex justify-content-center mt-5">
        <div class="row text-center"><h2>Create Outlet Manager</h2></div>
    
    <form method="POST" class="mt-3 mb-5 w-50">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Assign Outlet</label>
            <select name="outletID" class="form-control" required>
                <option value="">Select Outlet</option>
                <?php while ($row = $outletResult->fetch_assoc()) { ?>
                    <option value="<?php echo $row['OutletID']; ?>">
                        <?php echo $row['OutletName']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">NIC</label>
            <input type="text" name="nic" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Outlet Manager</button>
    </form>
    </div>


    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7fa;
        color: #333;
        padding-bottom: 20px;
    }

    .container {
        max-width: 700px;
    }

    .form-control {
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        border-radius: 5px;
        padding: 12px;
    }

    .alert {
        margin-top: 15px;
        text-align: center;
        font-size: 16px;
    }

    .row {
        max-width: 900px;
    }
</style>
