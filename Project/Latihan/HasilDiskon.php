<!DOCTYPE html>
<html>
<head>
    <title>Hasil Transaksi</title>
</head>
<body>
<?php
    $nama   = $_POST['nama'];
    $telp   = $_POST['telp'];
    $alamat = $_POST['alamat'];
    $barang = $_POST['barang'];
    $satuan = $_POST['satuan'];
    
    $harga  = intval($_POST['harga']); 
    $jumlah = intval($_POST['jumlah']); 

    $total = $harga * $jumlah;

    function hitung_diskon($total_harga)
    {
        if ($total_harga > 500000) {
            return $total_harga * 0.05; 
        } elseif ($total_harga > 250000) {
            return $total_harga * 0.025;
        } else {
            return 0;
        }
    }
    $disc = hitung_diskon($total);
    $totBayar = $total - $disc;
?>

    <p>
        <span class="label">Nama</span> : <?php echo $nama; ?><br><br>
        
        <span class="label">Telepon</span> : <?php echo $telp; ?><br><br>

        <span class="label">Alamat</span> : <?php echo nl2br($alamat); ?><br><br>

        <span class="label">Barang</span> : <?php echo $barang; ?><br><br>

        <span class="label">Harga</span> : <?php echo number_format($harga, 0, ',', '.'); ?><br><br>

        <span class="label">Jumlah</span> : <?php echo $jumlah; ?><br><br>

        <span class="label">Satuan</span> : <?php echo ucfirst($satuan);?><br><br>

        <span class="label">Total</span> : <?php echo number_format($total, 2, ',', '.'); ?><br><br>

        <span class="label">Diskon</span> : <?php echo number_format($disc, 2, ',', '.'); ?><br><br>

        <span class="label">Total Bayar</span> : <?php echo number_format($totBayar, 2, ',', '.'); ?>
    </p>
    <br>
    <a href="LatihanDiskon.php">back</a> 
</body>
</html>