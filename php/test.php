<?php
require 'vars.php';
$cam = "Gate";
$date = "2020-04-04";
$hour = "10hour";
$dir = getPersonImages($cam, $date, $hour);
printArray($dir);
#echo "$dir";
?>
