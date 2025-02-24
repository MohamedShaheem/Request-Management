<?php
$userID = $_SESSION['userID'];

// Fetch the outlet assigned to the user
$stmt = $conn->prepare("SELECT OutletID FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($outletID);
$stmt->fetch();
$stmt->close();

// If no outlet is assigned, show an error message
if (!$outletID) {
    echo '<div class="alert alert-danger">No outlet is assigned to your account.</div>';
    exit;
}

// Fetch the outlet's details
$query = "
    SELECT 
        o.OutletID,
        o.OutletName,
        o.Location,
        o.Email,
        o.ContactNumber,
        `22.5` AS kg_22_5,
        `12.5` AS kg_12_5,
        `5` AS kg_5,
        `2.3` AS kg_2_3,
        o.LastRestocked
    FROM 
        outlets o
    WHERE 
        o.OutletID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $outletID);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">Outlet Stock Details</h1>
<?php if ($result->num_rows > 0): ?>
    <?php $row = $result->fetch_assoc(); ?>
    <table class="table table-light table-striped w-75">
        <thead>
            <tr> 
                <th>22.5 Kg</th>
                <th>12.5 Kg</th>
                <th>5 Kg</th>
                <th>2.3 Kg</th>
                <th>Last Restocked</th>
            </tr>
        </thead>
        <tbody>
            <tr>           
                <td><?= htmlspecialchars($row['kg_22_5']) ?></td>
                <td><?= htmlspecialchars($row['kg_12_5']) ?></td>
                <td><?= htmlspecialchars($row['kg_5']) ?></td>
                <td><?= htmlspecialchars($row['kg_2_3']) ?></td>
                <td><?= htmlspecialchars($row['LastRestocked']) ?></td>
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No stock information found for your outlet.</div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
