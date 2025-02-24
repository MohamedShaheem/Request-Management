<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $outletName = $_POST['outletName'];
    $location = $_POST['location'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];
    $lastRestocked = date("Y-m-d H:i:s");

    // Prepare the first query
    $sql = "INSERT INTO outlets (OutletName, Location, Email, ContactNumber, `22.5`, `12.5`, `5`, `2.3`, LastRestocked) 
            VALUES (?, ?, ?, ?, '100', '100', '100', '100', ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $outletName, $location, $email, $contactNumber, $lastRestocked);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Outlet created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    header("Location: admin-dashboard.php?page=create-outlet");
    $stmt->close();
    $conn->close();
}
?>
<div class="row d-flex justify-content-center mt-5">
    <div class="row text-center">  
        <h2>Create Outlet</h2>
    </div>
   
    <form method="POST" class="mt-3 mb-5 w-75 p-4 rounded shadow bg-white">
        <div class="mb-3">
            <label class="form-label">Outlet Name</label>
            <input type="text" name="outletName" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contactNumber" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create Outlet</button>
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
