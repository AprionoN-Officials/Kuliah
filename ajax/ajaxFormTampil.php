<link rel="stylesheet" href="style.css">
<?php
require_once("config.php");
$sql = "SELECT nik,nama,jurusan FROM mahasiswa";
$result = $mysqli -> query($sql);
?>
<table id="customers" class="table-container">
<tr><th>Username</th>
<th>Password</th>
<th>User</th>
</tr>
<?php
while($row = $result -> fetch_assoc()){
echo "<tr>";
echo "<td>".$row["nik"]."</td>";
echo "<td>".$row["nama"]."</td>";
echo "<td>".$row["jurusan"]."</td>";
echo "</tr>";
}
?>
</table>