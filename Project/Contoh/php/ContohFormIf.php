<html>
<title>Belajar Fungsi If</title>
<body>
<h3>Isikan Form berikut ini</h3>
<form action="contohFormIf2.php" method="post">
<table border=0>
<td>Nama <td> : <input type="text" name="nama" placeholder="isikan
nama"><tr>
<td>Nilai<td> : <input type="text" name="nilai" placeholder="isikan
nilai"><tr>
<td><td> <input type="submit" name="submit" values="kirim">
<?php
if(isset($_POST['submit'])) {
if(empty($_POST['nama']) || empty($_POST['nilai'])){
echo "<hr>";
print('Isi dulu coy');
}else{
$nama=$_POST['nama'];
$nilai=$_POST['nilai'];
echo "<hr>";
echo "Hai <b>$nama..!!</b><br>";
echo "Nilai $nilai<br>";
$nilai=$nilai;
if($nilai=='A') {
echo "Anda Mendapat Nilai Sempurna";
}
elseif ($nilai=='B') {
echo "Anda mendapat Nilai Baik";
}
elseif ($nilai=='C') {
echo "Anda mendapat Nilai Cukup";
}
elseif ($nilai=='D') {
echo "Anda mendapat Nilai Kurang";
}
else {
echo "Anda mendapat Nilai ErroR, Harus Mengulang tahun depan";
}
}
}
?>
</table>
</body>
</html>