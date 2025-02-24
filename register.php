<?php
require 'db.php';
session_start();  // Start the session

// Fetch available outlets from the database
$outletQuery = "SELECT OutletID, OutletName, Location FROM outlets";
$outletResult = $conn->query($outletQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $nic = $_POST['nic'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'consumer';
    $outletID = $_POST['outlet']; // Get selected outlet ID from the form

    $stmt = $conn->prepare("INSERT INTO users (FullName, Email, PhoneNumber, NIC, PasswordHash, Role, outletID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullname, $email, $phone, $nic, $password, $role, $outletID);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registration successful! You can now login.";  // Set success message
        header('Location: login.php');
        exit();  // Make sure to exit after header redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background for a clean look */
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Register</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
        </div>
        <div class="mb-3">
            <label for="nic" class="form-label">NIC</label>
            <input type="text" class="form-control" id="nic" name="nic" placeholder="Enter your NIC" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
        </div>
        <div class="mb-3">
            <label for="outlet" class="form-label">Select Outlet</label>
            <select class="form-select" id="outlet" name="outlet" required>
                <option value="">Select an outlet</option>
                <?php while ($row = $outletResult->fetch_assoc()): ?>
                    <option value="<?= $row['OutletID']; ?>"><?= $row['OutletName']; ?> (<?= $row['Location']; ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <p class="mt-3 text-center">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </form>
</div>
</body>
</html>
