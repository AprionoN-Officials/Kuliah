<?php
include 'config.php';
$id = $_GET['id'];

$stmt = $mysqli->prepare("DELETE FROM  login WHERE ID=?");
$stmt->bind_param("i", $id);

// set parameters and execute
if($stmt->execute()){
   echo "SUCCESS";
   echo"<script>alert('Sukses');window.open('form_login.php','_self');</script>";
}

$mysqli->close();
//header("location:login.php");
?>