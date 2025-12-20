
<link rel="stylesheet" href="style.css">
<style>
/* Submit */
input[type="submit"] {
    background: #0a9f67;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

input[type="submit"]:hover {
    background: #087a50;
    box-shadow: 0 4px 10px rgba(10,159,103,0.4);
}

/* Reset */
input[type="reset"] {
    background: #6c757d;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
    margin-left: 10px;
}

input[type="reset"]:hover {
    background: #5a6268;
}

/* Responsive */
@media (max-width: 576px) {
    input[type="submit"],
    input[type="reset"] {
        width: 100%;
        margin: 5px 0;
    }
}

</style>
<div class="box">
<!--table border="0" align="center" width="100%">
<tr>
<td-->
  <form action="formlogin_act.php" method="post" border="0">
      <table width="70%" align="center">
      <tr>
        <td colspan="3" align="center"><h2>FORM LOGIN</h2></td>
      </tr>
      <tr>
      <tr>
        <td>Username</td>
        <td>:</td>
        <td><input type="text" name="p_username"></td>
      </tr>
      <tr>
        <td>Password</td>
        <td>:</td>
        <td><input type="password" name="p_pwd"></td>
      </tr>
      <tr>
        <td>Nama User</td>
        <td>:</td>
        <td><input type="text" name="p_user"></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
        <input type="submit" value="Register" > 
        
     </td>
      </tr>
    </table>
  </form>
  <button onclick="window.location.href='login.php'" class="btn-secondary">Back to Login</button>
<!--/td>
</tr>
</table-->

<br>
<br>
<?php
require_once("config.php");
$sql = "SELECT id,username,passwd,name from login";
$result = $mysqli -> query($sql);
?>
<table id="customers" class="table-container">
    <tr>
        <th>Username</th>
        <th>Password</th>
        <th>User</th>
        <th>Action</th>
    </tr>

<?php
// Associative array
while($row = $result -> fetch_assoc()){
  echo "<tr>";
   echo "<td>".$row["username"]."</td>";
   echo "<td>".$row["passwd"]."</td>";
   echo "<td>".$row["name"]."</td>";
   //echo "<td><img onclick='test(".$row['id'].")' src='img/update.png'><img src='img/delete.png'> </td>";
   echo "<td class='table-action'><a href='formlogin_edit.php?id=".$row['id']."'><img src='img/update.png'></a>";
   echo '<a href="formlogin_delete.php?id='. urlencode($row['id']).'" onclick="return confirm(\'Yakin ? \')"><img src="img/delete.png"></a>';
   echo "</td></tr>";
}
$result -> free_result();
$mysqli -> close();
?>

</table>
</div>


