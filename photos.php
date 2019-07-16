<?php
$HDD_ROOT = "/mnt/hdd";
$GATE_PHOTO_DIR = $HDD_ROOT."/GatePhotos";
$STAIRS_PHOTO_DIR = $HDD_ROOT."/StairsPhotos";

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];

echo '<html>';
echo "<title>$CAMERA Photos ($DATE)</title>";
echo "<head><h1><center><a href='./index.php'>$CAMERA Photos</a> ($DATE) </a></center></h1></head>";
echo '<body>';
$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$date_dirs = array_filter(glob($photo_dir."/$DATE/*"), 'is_dir');
arsort($date_dirs);
foreach($date_dirs as $k => $v){
    $hour = basename($v);
    $p_images = [];
    if(file_exists("$v/persons")){
        $p_images = glob("$v/persons/*.jpg");
    }
    $all_images = glob("$v/*.jpg");
    $o_images = array_flip(array_diff_key(array_flip($all_images),array_flip($p_images)));
    if(count($p_images)!=0){
	echo "<h2>$hour&emsp;<A href='./preview.php?camera=$CAMERA&date=$DATE&hour=$hour&tag=person'>Person images(".count($p_images).")</A>&emsp;<A href='./preview.php?camera=$CAMERA&date=$DATE&hour=$hour&tag=other'> Other Images (".count($o_images).")</A></h2>";
    }
    else{
	echo "<h2><A href='./preview.php?camera=$CAMERA&date=$DATE&hour=$hour&tag=all'>$hour</A> (".count($o_images)." Images with no persons)</h2>";
    }
}
echo '</body>';
echo '</html>';
?>
