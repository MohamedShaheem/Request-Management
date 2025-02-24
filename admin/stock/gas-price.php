<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'dispatch_office') {
    header('Location: ../login.php');
    exit;
}
require '../../db.php';

// Fetch user information
$userID = $_SESSION['userID']; 
$stmt = $conn->prepare("SELECT FullName FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();
$stmt->close();
?>
<?php
// Fetch data from outletstockprice table
$sql = "SELECT id, Type, Price FROM outletstockprice";
$result = $conn->query($sql);

// Handle the price update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_price'])) {
    $id = $_POST['id'];
    $new_price = $_POST['new_price'];

    // Update the price in the database
    $update_sql = "UPDATE outletstockprice SET Price = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $new_price, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../stock-management.php");
}
?>

