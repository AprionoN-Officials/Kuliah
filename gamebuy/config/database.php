<?php
$host = "localhost";
$user = "root";      // Default XAMPP/Laragon
$pass = "";          // Default kosong, sesuaikan jika ada password
$db   = "datagame";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi, matikan script jika gagal agar error tidak bocor ke user
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>