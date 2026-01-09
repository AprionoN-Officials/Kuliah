<?php
include "config/database.php";
session_start();

if (isset($_POST['submit'])) {
    $judul      = mysqli_real_escape_string($conn, $_POST['judul']);
    $genre      = mysqli_real_escape_string($conn, $_POST['genre']);
    $harga_sewa = $_POST['harga_sewa'];
    $harga_beli = $_POST['harga_beli'];
    $stok       = $_POST['stok'];
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Logika Upload Gambar
    $nama_file = "";
    if ($_FILES['gambar']['name'] != "") {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $_FILES['gambar']['name']);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 2048000) { // Max 2MB
                // Gunakan nama file berdasarkan judul agar konsisten dengan sistem deteksi otomatis
                $nama_file = strtolower(str_replace(' ', '_', $judul)) . "." . $ekstensi;
                move_uploaded_file($file_tmp, 'aset/images/' . $nama_file);
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Max 2MB'); window.location='tambah_game.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Ekstensi file tidak diperbolehkan!'); window.location='tambah_game.php';</script>";
            exit;
        }
    }

    // Insert ke database sesuai urutan kolom: id,judul,deskripsi,genre,harga_sewa,harga_beli,stok,gambar,created_at
    // id dan created_at biasanya auto-increment/timestamp
    $query = "INSERT INTO games (judul, deskripsi, genre, harga_sewa, harga_beli, stok, gambar) 
              VALUES ('$judul', '$deskripsi', '$genre', '$harga_sewa', '$harga_beli', '$stok', '$nama_file')";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Game berhasil ditambahkan!'); window.location='daftargame.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan game: " . mysqli_error($conn) . "'); window.location='tambah_game.php';</script>";
    }
} else {
    header("Location: daftargame.php");
}
?>
