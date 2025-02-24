<?php

$query = "
    SELECT u.UserID, u.FullName, u.Email, u.PhoneNumber, u.NIC, u.Role, o.OutletName, o.Location, o.ContactNumber
    FROM users u
    JOIN outlets o ON u.outletID = o.OutletID
    WHERE u.Role IN ('outlet_manager', 'dispatch_office')
";
$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

        <h1 class="text-center mb-4">Outlet Contact Details</h1>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Outlet Name</th>
                    <th>Location</th>
                    <th>Outlet Manger Name</th>
                    <th>Email</th>
                    <th>Manager Contact Number</th>
                    <th>NIC</th>
                  
                    <th>Outlet Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['UserID']; ?></td>
                        <td><?php echo $row['OutletName']; ?></td>
                        <td><?php echo $row['Location']; ?></td>
                        <td><?php echo $row['FullName']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td><?php echo $row['PhoneNumber']; ?></td>
                        <td><?php echo $row['NIC']; ?></td>
                      
                        <td><?php echo $row['ContactNumber']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

<?php
// Close the database connection
mysqli_close($conn);
?>
