<form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
<table>
<tr>
<td>jumlah Loop : </td>
<td><select name="p_loop">
<option>---</option>
<?php
for($a=1;$a<=100;$a++){
echo "<option value=".$a.">$a</option>";
}
?>
</select>
</td>
<tr>
<td><input type="submit" value="Process" name="p_submit">
</tr>
</table>
</form>