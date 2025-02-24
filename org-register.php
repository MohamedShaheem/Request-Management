<?php 

include 'db.php'; 
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $certification = 'Certified';
    $contactPerson = !empty($_POST['contactPerson']) ? $_POST['contactPerson'] : NULL;
    $contactNumber = !empty($_POST['contactNumber']) ? $_POST['contactNumber'] : NULL;
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO organizations (Name, Certification, ContactPerson, ContactNumber, Email, password) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name,  $certification, $contactPerson, $contactNumber, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organization</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for a clean look */
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
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h2 class="text-center">Register Organization</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Organization Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter your organization name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Contact Person</label>
                        <input type="text" name="contactPerson" class="form-control" id="contactPerson" placeholder="Enter contact person name">
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="text" name="contactNumber" class="form-control" id="contactNumber" placeholder="Enter contact number">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email address" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Create a password" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                </form>
                <p class="mt-3 text-center">
                    Already have an account? <a href="org-login.php">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

