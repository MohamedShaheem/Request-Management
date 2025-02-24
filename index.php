<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select User Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(19, 27, 46),rgb(45, 54, 104));
            color: rgb(0, 0, 0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }

        .navbar-logo {
                width: 350px;
                height: auto; /* Maintain aspect ratio */
            }
        .selection-container {
            background-color:rgb(204, 204, 204);
            border-radius: 12px;
            padding: 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .selection-container:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .selection-container h1 {
            color:rgb(0, 0, 0);
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .selection-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .selection-container p {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }

        .btn {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            text-transform: uppercase;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary {
            background-color: #333;
            border: none;
        }

        .btn-primary:hover {
            background-color: #555;
            transform: translateY(-3px);
        }

        .btn-success {
            background-color: #2e7d32;
            border: none;
        }

        .btn-success:hover {
            background-color: #1b5e20;
            transform: translateY(-3px);
        }

        @media (max-width: 576px) {
            .selection-container {
                padding: 25px;
                width: 90%;
            }

            .btn {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="selection-container">
<img src="assets/logo.png" class="navbar-logo" alt="">
    <h2>Select User Type</h2>
    <p>Please choose your account type to proceed.</p>
    <a href="login.php" class="btn btn-primary mb-2">Normal User</a>
    <a href="org-login.php" class="btn btn-success">Business User</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
