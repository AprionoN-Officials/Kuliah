<html>
<h1> Contoh Aritmatika</h1>
</html>
<?php
echo "<h3>Hasil</h3>";
echo "Bil1 : ".$_POST['bil1'];
echo "<br>Bil2 : ".$_POST['bil2'];
echo "<br>";
$a=$_POST['bil1'];
$b=$_POST['bil2'];
echo "<br>";
echo "<h3>Aritmatik</h3>";
echo $a."+".$b."=".($a+$b)."<br>";
echo $a."-".$b."=".($a-$b)."<br>";
echo $a."*".$b."=".($a*$b)."<br>";
echo $a."/".$b."=".($a/$b)."<br>";
echo $a."%".$b."=".($a%$b)."<br>";
echo "<br>";
echo "<h3>Comparasion</h3>";
echo $a.">".$b." = ";
var_dump($a>$b);
echo "<br>".$a."<".$b." = ";
var_dump($a<$b);
echo "<br>".$a.">=".$b." = ";
var_dump($a>=$b);
echo "<br>".$a."<=".$b." = ";
var_dump($a<=$b);
echo "<br>".$a."==".$b." = ";
var_dump($a==$b);
?>