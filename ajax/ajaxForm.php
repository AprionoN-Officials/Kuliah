<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<h1>Contoh jQuery-AJAX dengan method ajax()</h1>
<form method="post" id="form_mahasiswa">
<table border="0" align="center">
<tr>
<td><label>NIK </label></td>
<td><input type="number" name="nik"></td>
</tr>
<tr>
<td><label>Nama </label></td>
<td><input type="text" name="nama"></td>
</tr>
<tr>
<td><label>Jurusan </label></td>
<td><select name="jurusan">
<option value="TI">Teknik Informatika </option>
<option value="MI">Manajemen Informatika </option>
<option value="SI">Sistem Informatika </option>
</select></td>
</tr>
<tr>
<td colspan="2" align="center"><button id="btn_tampil" class="button1">Tampil</button></td>
</tr>
</table>
<hr>
<div id="tampil_data"></div>
<script>
$(document).ready(function(){
$("#tampil_data").load("ajaxFormTampil.php");
$("#btn_tampil").click(function(){
var dataForm=$("#form_mahasiswa").serialize();
var actionUrl='ajaxFormTambah.php';
$.ajax({
type: "POST",
url: actionUrl,
data: dataForm, // serializes the form's elements.
success: function(data)
{ 
//alert(data); // show response from the php script.
$("#tampil_data").load("ajaxFormTampil.php");
}
});
});
});
</script>