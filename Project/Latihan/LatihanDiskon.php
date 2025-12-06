<!DOCTYPE html>
<html>
<head>
    <title>Form Transaksi</title>
</head>
<body>
<h2>TOKO LAPTOP</h2>
    <form action="hasilloop.php" method="post">
        <table border="0">
            <tr>
                <td>Nama </td>
                <td>:</td>
                <td><input type="text" name="nama"></td>
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