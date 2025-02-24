<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'outlet_manager') {
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
    <title>Outlet Dashboard</title>
    <link rel="shortcut icon" href="../assets/fav.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- JQuery -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


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

        .main {
            transition: all 0.3s ease;
            background-color: rgba(43, 255, 0, 0.34) !important;
            border-radius: 5px;

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
            .navbar-logo{
                width: 90px;
            }
            .nav-link{
                font-size: smaller;
            }

        }
    </style>
</head>
<body style="background: hsla(240, 2%, 12%, 0.178);">
<!-- Navbar -->
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
                    <a class="nav-link <?= $page === 'requests' ? 'active' : '' ?>" href="outlet-dashboard.php?page=requests">Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'industrial-requests' ? 'active' : '' ?>" href="outlet-dashboard.php?page=industrial-requests">Industrial Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'outlet-stocks' ? 'active' : '' ?>" href="outlet-dashboard.php?page=outlet-stocks">Outlet Stocks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'reallocate' ? 'active' : '' ?>" href="outlet-dashboard.php?page=reallocate">Reallocate Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'available-schedule' ? 'active' : '' ?>" href="outlet-dashboard.php?page=available-schedule">Available Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'users' ? 'active' : '' ?>" href="outlet-dashboard.php?page=users">All Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'r-token' ? 'active' : '' ?>" href="outlet-dashboard.php?page=r-token">Reallocated Tokens</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'report' ? 'active' : '' ?>" href="outlet-dashboard.php?page=report">Sales Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main <?= $page === 'schedule' ? 'active' : '' ?>" href="outlet-dashboard.php?page=schedule">Request Schedule</a>
                </li>
            </ul>
            <span class="navbar-text me-3 text-light">
                Welcome, <span class="fw-bold"><?= htmlspecialchars($fullName); ?></span>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

    <!-- Page Content -->
    <div class="container d-flex flex-column mb-2 mt-3">
        <?php
        $allowed_pages = ['requests', 'outlet-stocks', 'reallocate', 'industrial-requests', 'schedule', 'available-schedule', 'users','r-token','report','profile'];
        if (in_array($page, $allowed_pages)) {
            include "$page.php";
        } else {
            include 'requests.php'; // Default page
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
