<?php
include 'config/database.php';
$query = mysqli_query($conn, "SELECT id, judul, gambar FROM games");
while($row = mysqli_fetch_assoc($query)) {
    echo "ID: " . $row['id'] . " | Judul: " . $row['judul'] . " | Gambar: " . $row['gambar'] . "\n";
}
?>