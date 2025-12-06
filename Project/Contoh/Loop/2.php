<?php
$count = 0;
$seriesA = "";
while ($count < 100)
{
$seriesA = $seriesA + 'a'; // can also be written as seriesA += 'a’;
$count++; // if you forget this one you are in trouble
}
echo $seriesA;
?>