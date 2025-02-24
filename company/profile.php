<?php
session_start();
if (!isset($_SESSION['OrganizationID'])) {
    header('Location: login.php');
    exit;
}

require '../db.php';

// Fetch user information
$userID = $_SESSION['OrganizationID'];
$stmt = $conn->prepare("SELECT ContactPerson, ContactNumber, Email  FROM organizations WHERE OrganizationID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($contactperson, $contactnumber, $email);
$stmt->fetch();
$stmt->close();

// Ensure header() is called before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the inputs
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['password_confirmation'];

    // Check if fields are empty
    if (empty($username) || empty($email) || empty($phoneNumber)) {
        $_SESSION['error'] = "All fields are required!";
    } elseif (!empty($password) && $password !== $passwordConfirmation) {
        // Ensure passwords match
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        // Update the user in the database
        $stmt = $conn->prepare("UPDATE Organizations SET ContactPerson = ?, ContactNumber = ?, Email = ? WHERE OrganizationID = ?");
        $stmt->execute([$username, $phoneNumber, $email, $userID]);

        // Update password if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Organizations SET passwordHash = ? WHERE OrganizationID = ?");
            $stmt->execute([$hashedPassword, $userID]);
        }

        $_SESSION['success'] = "Profile updated successfully!";
        
        // Redirect to the dashboard
        header("Location: company-dashboard.php");
        exit;  // Ensure no further code is executed after the redirect
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="shortcut icon" href="../assets/fav.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  <!-- JQuery -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg,rgb(24, 72, 104), #6dd5ed);
            color: #333;
            padding-bottom: 30px;
        }

        .container {
            max-width: 700px;
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            font-size: 32px;
            color:rgb(45, 45, 49);
            margin-bottom: 40px;
            font-weight: bold;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #34495e;
        }

        .form-control, .form-select {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border 0.3s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.7);
        }

        .btn-primary {
            background-color:rgb(29, 190, 56);
            border-color:rgb(37, 192, 58);
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color:rgb(55, 212, 34);
            border-color:rgb(41, 185, 96);
        }

        .alert {
            font-size: 16px;
            color: #e74c3c;
            margin-top: 20px;
        }

        .form-footer {
            text-align: center;
            margin-top: 30px;
        }

        .form-footer a {
            color:rgb(255, 255, 255);
            text-decoration: none;
            font-weight: 600;
            background-color: rgba(80, 80, 80, 0.76);
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-footer a:hover {
            background-color: rgba(48, 47, 47, 0.76);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Profile</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php elseif (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Contact Person Name</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($contactperson) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Contact Person Mobile Number </label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($contactnumber) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Company Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
           
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>

        <div class="form-footer mt-3">
            <a href="company-dashboard.php">Cancel</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
