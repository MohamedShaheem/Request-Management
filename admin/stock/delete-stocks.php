<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
session_start();
include("../../db.php");
$id = $_POST['id'];
$sql = "DELETE FROM mainstock WHERE id = '$id'";
$result =mysqli_query($conn,$sql);

if($result){
    $_SESSION['message'] = "Stock Deleted successfully!";
    $_SESSION['msg_type'] = "success";
}else{
    $_SESSION['message'] = "Stock delete failed!";
    $_SESSION['msg_type'] = "danger";
}
header("location:add-stock.php");
mysqli_close($conn);
}

?>