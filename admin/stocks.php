<?php
$query = "
SELECT 
    o.OutletID,
    o.OutletName,
    o.Location,
    o.Email,
    o.ContactNumber,
    o.`22.5` AS Stock_22_5,
    o.`12.5` AS Stock_12_5,
    o.`5` AS Stock_5,
    o.`2.3` AS Stock_2_3,
    o.LastRestocked
    FROM outlets o
    ORDER BY o.OutletName ASC
";

$result = $conn->query($query);
?>

<h1 class="mb-4">Outlet Stocks</h1>
<?php if ($result->num_rows > 0): ?>
    <table class="table table-light table-striped">
        <thead>
            <tr>
                <th>Outlet ID</th>
                <th>Outlet Name</th>
                <th>Location</th>
                <th>Stock (22.5)</th>
                <th>Stock (12.5)</th>
                <th>Stock (5)</th>
                <th>Stock (2.3)</th>
                <th>Last Restocked</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['OutletID']) ?></td>
                    <td><?= htmlspecialchars($row['OutletName']) ?></td>
                    <td><?= htmlspecialchars($row['Location']) ?></td>
                    <td><?= htmlspecialchars($row['Stock_22_5']) ?></td>
                    <td><?= htmlspecialchars($row['Stock_12_5']) ?></td>
                    <td><?= htmlspecialchars($row['Stock_5']) ?></td>
                    <td><?= htmlspecialchars($row['Stock_2_3']) ?></td>
                    <td><?= htmlspecialchars($row['LastRestocked']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">No outlet stock information found.</div>
<?php endif; ?>

<?php $conn->close(); ?>
