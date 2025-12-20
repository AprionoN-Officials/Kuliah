<?php
require_once("config.php");

$username=$_POST['p_username'];
$pwd=$_POST['p_pwd'];
$user=$_POST['p_user'];
// $sqlInsert = "INSERT INTO login (username,passwd,user) VALUES('".$username."',password('".$pwd."'),'".$user."')";
// echo $sqlInsert; 
//    if ($mysqli->query($sqlInsert)) {
//       printf("Record inserted successfully.<br />");
//    }
//    if ($mysqli->errno) {
//       printf("Could not insert record into table: %s<br />", $mysqli→error);
//    }
if(empty($user)||empty($pwd)){
   echo "<script>alert('Harap isi terlebih dahulu form nya !!!');window.open('form_login.php','_self');</script>";
   //header('Location:form_login.php');
}else{
   $queryInsert="INSERT INTO login (username,passwd,name) VALUES (?, password(?), ?)";
   $stmt = $mysqli->prepare($queryInsert);
   $stmt->bind_param("sss", $a, $b, $c);
// set parameters and execute
   $a = $username;
   $b = $pwd;
   $c = $user;

// echo "Query template: " . $queryInsert . "\n";
// var_dump($user,$username,$pwd);
 if($stmt->execute()){
    echo "SUCCESS";
    echo"<script>alert('Sukses');window.open('form_login.php','_self');</script>";
 }
}
   $stmt->close();
   $mysqli->close();
?>