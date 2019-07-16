<?php
$HDD_ROOT = "/mnt/hdd";
$GATE_PHOTO_DIR = $HDD_ROOT."/GatePhotos";
$STAIRS_PHOTO_DIR = $HDD_ROOT."/StairsPhotos";


?>

<html>
    <head>
        <title>Narahari Home CCTV Monitoring System</title>
        <head><h1><center>Narahari Home CCTV Monitoring System</center></h1>
            <center><b>Last Updated on : <?php echo date("Y-m-d h:i:sa")?>
		&emsp;Storage: <?php echo HumanSize(disk_free_space($HDD_ROOT))?> Available</b></center>
        </head>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
        th, td {
            padding: 5px;
            text-align: left;
        }
        </style>
    </head>
    <body>
        <br>
        <br>
        <table style=width:100%>
            <tr>
                <th><h2>Gate Photos</h2></th>
                <th><h2>Stairs Photos</h2></th>
                <th><h2>Gate Videos</h2></th>
                <th><h2>Stairs Videos</h2></th>
            </tr>
	<?php
	$dirs = array_filter(glob($GATE_PHOTO_DIR."/*"), 'is_dir');
	arsort($dirs);
	foreach($dirs as $k => $v){
	    $v = basename($v);
            echo '<tr>';
		echo "<td><h2><a href='./photos.php?camera=Gate&date=$v'> $v </a></h2></td>";
		echo "<td><h2><a href='./photos.php?camera=Stairs&date=$v'> $v </a></h2></td>";
		echo "<td><h2><a href='./videos.php?camera=Gate&date=$v'> $v </a></h2></td>";
		echo "<td><h2><a href='./videos.php?camera=Stairs&date=$v'> $v </a></h2></td>";
	    echo '</tr>';
	}
	?>
        </table>
        <h2><a href='./logs/internet_autologin/log.txt'> Internet Autologin</a></h2>
        <h2><a href='./logs'> Logs </a></h2>
    </body>
</html>

<?php
function HumanSize($Bytes)
{
  $Type=array("", "KB", "MB", "GB", "TB", "PB", "exa", "zetta", "yotta");
  $Index=0;
  while($Bytes>=1024)
  {
    $Bytes/=1024;
    $Index++;
  }
  return(sprintf('%1.2f' , $Bytes)." ".$Type[$Index]);
}
?>
