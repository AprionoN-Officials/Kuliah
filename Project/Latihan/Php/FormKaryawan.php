<!DOCTYPE html>
<html>
<head>
    <title>Hitung Gaji Karyawan</title>
</head>
<body>

<div id="Form">
    <h2>Form Gaji Mingguan</h2>
    <form method="post" action="">
        <table>
            <tr>
                <td><label>Nama Karyawan</label></td>
                <td>: <input type="text" name="nama" required></td>
            </tr>
            <tr>
                <td><label>Jam Kerja (Mingguan)</label></td>
                <td>: <input type="number" name="jam_kerja" min="1" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <input type="submit" name="submit" value="Hitung Gaji">
                </td>
            </tr>
        </table>
    </form>
</div>

<hr>

<?php
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $jam = $_POST['jam_kerja'];

    $upah_per_jam = 2000;
    $upah_lembur = 3000;
    $batas_jam = 48;
    $total_gaji = 0;

    if ($jam > $batas_jam) {
        $gaji_normal = $batas_jam * $upah_per_jam;
        $jam_lembur = $jam - $batas_jam;
        $gaji_lembur = $jam_lembur * $upah_lembur;
        
        $total_gaji = $gaji_normal + $gaji_lembur;
    } else {
        $total_gaji = $jam * $upah_per_jam;
    }

    echo "<div id='Output'>";
    echo "<h3>Hasil Perhitungan untuk: " . htmlspecialchars($nama) . "</h3>";
    echo "Total Jam Kerja: " . $jam . " Jam<br>";
    echo "<h3>Upah Yang Diterima: Rp. " . number_format($total_gaji, 0, ',', '.') . ",-</h3>";
    echo "</div>";
}
?>


<!-- Versi 1.

</body>
</html>

<html>

<div id="Form">

<table>
<tr>
    <td>
        <label>Nama</label><br>
             <input type="text" id="Nama" placeholder="">
    </td>
</tr>
  <tr>
    <td>
        <label>Jam Kerja</label><br>
         <input type="number" id="Jam" min="1" placeholder="">
    </td>
</tr>
    <tr>
        <td>
             <input type="submit" value="Submit" onclick="hitung()">
        </td>
</table>
</div>

<div id=Output style=display:none;>
    <h3>Upah Yang Diterima : </h3>
    <h3> Rp. <span id="gaji"></span></h3>

</div>
<script> 
function hitung() {
var jam = document.getElementById("Jam").value;
var upah = 2000,lembur,gaji;
lembur = 3000;
if(jam > 48) {
    gaji = jam * lembur * 7;
} else if(jam <= 48) {
    gaji = jam * upah * 7; 
} else {
    gaji = jam * upah * 7;
}
document.getElementById("gaji").innerText = gaji;
document.getElementById("Output").style.display = "block";
}

</script>
</html> -->