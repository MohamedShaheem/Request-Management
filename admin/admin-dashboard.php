<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'dispatch_office') {
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
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/fav.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  <!-- JQuery -->
    <style>
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            padding-bottom: 20px;
        }

        .container {
            max-width: 1700px;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        @media (max-width: 1650px) {
            .container {
                max-width: 1200px;
            }

            .navbar-logo{
                width: 90px;
            }
            .nav-link{
                font-size: smaller;
            }
            .navbar-text{
                font-size: 15px;
            }
            .navbar-text .wel{
                display: none;
            }

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
            <?php $page = $_GET['page'] ?? 'requests'; ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'requests' ? 'active' : '' ?>" href="admin-dashboard.php?page=requests">Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'industrial-requests' ? 'active' : '' ?>" href="admin-dashboard.php?page=industrial-requests">Industrial Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'outlet-requests' ? 'active' : '' ?>" href="admin-dashboard.php?page=outlet-requests">Outlet Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'delivery-schedule' ? 'active' : '' ?>" href="admin-dashboard.php?page=delivery-schedule">Delivery Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'stocks' ? 'active' : '' ?>" href="admin-dashboard.php?page=stocks">Outlet Stocks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'outlet-details' ? 'active' : '' ?>" href="admin-dashboard.php?page=outlet-details">Outlet Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'create-user' ? 'active' : '' ?>" href="admin-dashboard.php?page=create-user">Create User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'create-outlet' ? 'active' : '' ?>" href="admin-dashboard.php?page=create-outlet">Create Outlet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'users' ? 'active' : '' ?>" href="admin-dashboard.php?page=users">Consumers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'stock-management' ? 'active' : '' ?>" href="stock-management.php">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'report' ? 'active' : '' ?>" href="admin-dashboard.php?page=report">Sales Report</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                <span class="wel">Welcome,</span> <span class="fw-bolder text-light"><?= htmlspecialchars($fullName); ?></span>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>


<div class="container mt-4 p-2 shadow-sm bg-white rounded">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'requests';
    $allowed_pages = ['requests','outlet-requests','stocks', 'delivery-schedule', 'industrial-requests','create-user','create-outlet','outlet-details','report','users','org-consumer'];
    
    if (in_array($page, $allowed_pages)) {
        include "$page.php";        // this is coming from navbar href -> href="user-dashboard.php?page=request-gas"
    } else {
        include 'requests.php'; // Default page
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
