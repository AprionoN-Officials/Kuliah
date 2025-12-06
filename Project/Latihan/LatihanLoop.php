<!DOCTYPE html>
<html>
<head>
    <title>Form Transaksi</title>
</head>
<body>
<h2>Transaksi</h2>
<?php
for($i = 1; $i <= 5; $i++){
?>
 
    <form action="hasilloop.php" method="post">
        <table border="0">
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td><?php echo $i; ?></td>
            </tr>
            <tr>
                <td>Nama Barang</td>
                <td>:</td>
                <td><input type="text" name="namabarang[]"></td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>:</td>
                <td><input type="text" name="jumlah[]"></td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>:</td>
                <td><input type="text" name="harga[]"></td>
            </tr>
            <?php
}
?>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <input type="submit" value="Proses"/>
                    <input type="reset" value="Reset">
                    <input type="button" value="Refresh" onclick="window.location.reload()">                
            </td>
            </tr>
        </table>
    </form>
</body>
</html>