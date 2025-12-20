<?php
//$mysqli = new mysqli("localhost","root","","ubs");
require_once("config.php");

$user=$_POST['p_username'];
$pwd=$_POST['p_pwd'];
if($user=="" || $pwd==""){
   header('Location:login.php');
}
$sqlLogin = "SELECT id, username,passwd FROM login where username=? and passwd=password(?)";
$stmt=$mysqli->prepare($sqlLogin);
/*$stmt = $mysqli->prepare($sqlLogin); 
$stmt->bind_param("s","s",$user,$pwd);
$stmt->execute();
$result = $stmt->get_result();
echo $result;
$numrows=$stmt->num_rows();
echo $numrows;
*/
//$stmt=$mysqli->prepare("SELECT id,username,passwd FROM LOGIN WHERE username=? AND passwd=PASSWORD(?)");
$stmt->bind_param("ss",$user,$pwd);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    header('Location: dashboard.php');
   //  while ($row = $result->fetch_assoc()) {
   //      echo "id: " . $row["id"] . " - Name: " . $row["username"] . " " . $row["passwd"] . "<br>";
   //  }
} else {
    echo "<script>alert('salah username/password!!!');window.open('login.php','_self');</script>";
    //header('Location: login.php');
}
// while ($row = $result->fetch_assoc()) {
//     echo $row['name'];
// }





  $stmt->close();
   $mysqli -> close(); 

?>