<?php
$host = 'localhost';
$dbname = 'gas';
$username = 'root';
$password = '';

try{
    $conn = mysqli_connect($host,$username,$password,$dbname);
}
catch(mysqli_sql_exception){
    echo "Database Connection Error". mysqli_error($conn);
}
?>
