<?php
include 'db.php';
session_start(); // Start the session at the beginning.

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM organizations WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Store user data in session
            $_SESSION['OrganizationID'] = $row['OrganizationID'];
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Email'] = $row['Email'];

            // Redirect to the dashboard
            header('Location: company/company-dashboard.php');
            exit(); // Stop further script execution after redirection
        } else {
            echo "<script>alert('Invalid credentials.');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.');</script>";
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
    
        .form-container {
            background-color: #1e1e1e;
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .form-container .navbar-logo {
            width: 350px;
            height: auto; /* Maintain aspect ratio */
        }
        .form-container h1, .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color:rgb(248, 247, 250);
        }
        .form-control {
            background-color: #2c2c2c;
            border: 1px solid #444;
            color: #e0e0e0;
        }
        .form-control:focus {
            background-color: #333;
            border-color: #bb86fc;
            color: #e0e0e0;
            box-shadow: none;
        }
        .btn-primary {
            background-color: rgb(61, 96, 248);
            border: none;
            width: 100%;
        }
        .btn-secondary {
            background-color: rgb(92, 95, 100);
            border: none;
            width: 100%;
            
        }
    
        .btn-primary:hover {
            background-color: rgb(70, 81, 240);
        }
        .btn-success {
            background-color: rgb(3, 218, 111);
            border: none;
            width: 100%;
        }
        .btn-success:hover {
            background-color: rgb(8, 212, 185);
        }
        a {
            color: #bb86fc;
            text-decoration: none;
        }
    
    </style>
</head>
<body>
            <div class="form-container">
            <img src="assets/logo.png" class="navbar-logo" alt="">
                <h2>Business User</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary mb-2">Login</button>
                    <a href= "index.php" name="login" class="btn btn-secondary">Back</a>
                </form>
                <p class="mt-3 text-center">
                    Don't have an account? <a href="org-register.php">Register</a>
                </p>
            </div>
</body>
</html>



