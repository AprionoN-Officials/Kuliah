<?php
$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "dataquiz";
$koneksi    = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Tidak bisa terkoneksi ke database");
}
?>