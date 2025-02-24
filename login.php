<?php
require 'db.php';
session_start();  // Start the session


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT UserID, PasswordHash, Role FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userID, $hash, $role);

    if ($stmt->fetch() && password_verify($password, $hash)) {
        $_SESSION['userID'] = $userID;
        $_SESSION['role'] = $role;

        if ($role === 'consumer') {
            header('Location: user/user-dashboard.php');
        } elseif ($role === 'outlet_manager') {
            header('Location: outlet/outlet-dashboard.php');
        } elseif ($role === 'dispatch_office') {
            header('Location: admin/admin-dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-container h2, .form-container h1 {
            text-align: center;
        }
        .btn-primary, .btn-success, .btn-secondary {
            width: 100%;
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
            background-color:rgb(61, 96, 248);
            border: none;
        }
        .btn-primary:hover {
            background-color:rgb(70, 81, 240);
        }
        .btn-success {
            background-color:rgb(3, 218, 111);
            border: none;
        }
        .btn-success:hover {
            background-color:rgb(8, 212, 185);
        }
        /* .alert {
            background-color: #cf6679;
            color: #fff;
            border: none;
        } */
    </style>
</head>


<body>
<div class="form-container">
    <img src="assets/logo.png" class="navbar-logo" alt="">
    <h2>Login</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);  // Clear the message after displaying
    }
    ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"> <?= $error ?> </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary mb-3">Login</button>
        <a href="register.php" class="btn btn-success mb-3">Register</a>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

