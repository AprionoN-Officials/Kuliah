<html>
<head>
    <title>Hasil Transaksi</title>
</head>
<body>

<h3>HITUNG TRANSAKSI</h3>
<br>

<?php
$nama_barang = $_POST['namabarang'];
$jumlah = $_POST['jumlah'];
$harga = $_POST['harga'];

$grand_total = 0;

for ($i = 0; $i < count($nama_barang); $i++) {
    
    if (!empty($nama_barang[$i])) {
        
        $subtotal = $jumlah[$i] * $harga[$i];
        
        $grand_total = $grand_total + $subtotal;
        
        echo "Nomor : " . ($i + 1) . "<br>";
        echo "Nama Barang : " . $nama_barang[$i] . "<br>";
        echo "Jumlah : " . $jumlah[$i] . "<br>";
        echo "Harga : " . $harga[$i] . "<br>";
        echo "Total : " . $subtotal . "<br>";
        
        echo "<hr>";
    }
}

echo "Jumlah Total : " . $grand_total;
?>
</body>
</html>