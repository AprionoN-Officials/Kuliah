<?php
include('../mysql/config.php');
$nik=$_POST['nik'];
$nama=$_POST['nama'];
$jurusan=$_POST['jurusan'];
$queryInsert="INSERT INTO mahasiswa (nik,nama,jurusan) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($queryInsert);
$stmt->bind_param("sss", $a, $b, $c);
$a = $nik;
$b = $nama;
$c = $jurusan;
$stmt->execute();
$stmt->close();
$mysqli->close();
?>