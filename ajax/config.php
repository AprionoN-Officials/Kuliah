<?php

$server = "localhost";
$user = "root";
$password = "";
$nama_database = "ubs";

// $db = mysqli_connect($server, $user, $password, $nama_database);

// if( !$db ){
//     die("Gagal terhubung dengan database: " . mysqli_connect_error());
// }
//$mysqli = new mysqli("localhost","root","","ubs");
$mysqli = new mysqli($server,$user,$password,$nama_database);
// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}
// }else{
//   echo "<h1> Koneksi Sukses</h1>";
// }
?>
