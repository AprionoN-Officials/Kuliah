<!DOCTYPE html>
<html>
    <?php
$satuan = ["buah","pcs","butir","kodi","set"];
?>
<head>
    <title>Form Transaksi</title>
</head>
<body>
<h2>TOKO LAPTOP</h2>
    <form action="HasilDiskon.php" method="post">
        <table border="0">
            <tr>
                <td>Nama </td>
                <td>:</td>
                <td><input type="text" name="nama" size="20"></td>
            </tr>
            <tr>
                <td>Telp</td>
                <td>:</td>
                <td><input type="text" name="telp" size="20"></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><textarea name="alamat" cols="22"></textarea></td>
            </tr>
            <tr>
                <td>Barang</td>
                <td>:</td>
                <td><input type="text" name="barang" size="20"></td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>:</td>
                <td><input type="text" name="harga" size="20"></td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>:</td>
                <td><input type="text" name="jumlah" size="10"></td>
            </tr>
            <tr>
                <td>Satuan</td>
                <td>:</td>
                <td><select name="satuan"><?php foreach ($satuan as $s) { echo "<option value='$s'>$s</option>"; } ?></select>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <input type="submit" value="Proses"/>
                    <input type="reset" value="Cancel">
            </td>
            </tr>
        </table>
    </form>
</body>
</html>