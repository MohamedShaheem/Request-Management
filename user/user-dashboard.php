<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'consumer') {
    header('Location: ../login.php');
    exit;
}

require '../db.php';

// Fetch user information
$userID = $_SESSION['userID'];
$stmt = $conn->prepare("SELECT FullName FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();
$stmt->close();
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
            background-color: #f4f7fa;
            color: #333;
                padding-bottom: 20px;
        }

        .container {
            max-width: 800px;
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

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-select, .form-control {
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .price-list {
            margin-top: 20px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
        }

        .price-list table {
            width: 100%;
            margin-bottom: 0;
        }

        .price-list th, .price-list td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .price-list th {
            background-color: #3498db;
            color: white;
        }

        .price-list td {
            background-color: #f9f9f9;
        }

        .price-list .gas-type {
            font-weight: 600;
        }

        .alert {
            font-size: 16px;
        }

        .mt-3 {
            margin-top: 20px;
        }

            .navbar-logo {
            width: 120px;
            height: auto; /* Maintain aspect ratio */
        }

        .navbar-nav .nav-link {
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .navbar-nav .nav-link.active {
                color:rgb(255, 255, 255);
                font-weight: 600;
                border-radius: 5px;
            }

        .navbar-text {
            font-size: 1.1rem;
        }

        .btn-outline-danger {
            border-radius: 5px;
            padding: 8px 16px;
        }

        .navbar-collapse {
            justify-content: flex-end;
        }

        /* Ensure the navbar items are properly aligned on smaller screens */
        .navbar-toggler-icon {
            background-color: #fff;
        }
    </style>
</head>
<body style="background: hsla(240, 2%, 12%, 0.178);">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="template.php">
            <img src="../assets/logo.png" alt="Brand Logo" class="navbar-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php $page = $_GET['page'] ?? 'request-gas'; ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'request-gas' ? 'active' : '' ?>" href="user-dashboard.php?page=request-gas">Gas-Request</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'request-tracking' ? 'active' : '' ?>" href="user-dashboard.php?page=request-tracking">Your-Requests</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                Welcome, <a href="profile.php"><span class="fw-bolder text-light"><?= htmlspecialchars($fullName); ?></span></a>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>


<div class="container mt-4 ">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'request-gas';
    $allowed_pages = ['request-tracking', 'request-gas', 'process-gas-request' , 'cancel-request'];
    
    if (in_array($page, $allowed_pages)) {
        include "$page.php";        // this is coming from navbar href -> href="user-dashboard.php?page=request-gas"
    } else {
        include 'request-gas.php'; // Default page
    }
    ?>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
