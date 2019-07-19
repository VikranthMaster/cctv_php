<?php
require 'vars.php';

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$HOUR = $_GET["hour"];
$TAG= $_GET["tag"];
?>

<html>
<style>
    img{border: 1px solid #ddd;border-radius: 4px; padding: 5px; width: 150px;}
    img:hover { box-shadow: 0 0 2px 1px rgba(0,140, 186, 0.5);}
</style>

<?php
echo "<title>$CAMERA Photos ($DATE) ($HOUR) (All)</title>";
echo "<head><h1><center><a href='../../../'>$CAMERA Photos</a>&nbsp<a href='./photos.php?camera=$CAMERA&date=$DATE'>($DATE)</a> ($HOUR) (All)</a></center></h1></head>";
echo '<body>';
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
$int_hour = substr($HOUR,0,2);
$prev_hour = sprintf("%'.02d",$int_hour-1);
$next_hour = sprintf("%'.02d",$int_hour+1);
echo "<h2><h2>";
if($int_hour!="00"){
    echo "<div style='float: left'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$prev_hour"."hour&tag=$TAG'> Previous</a> ($prev_hour hour)</div>";
}
if($int_hour!="23"){
    echo "<div style='float: right'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$next_hour"."hour&tag=$TAG'> Next</a> ($next_hour hour)</div>";
}
echo "<div style='margin: auto; width: 250px;'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$HOUR'>Videos</a>&emsp;&emsp;<a href='./preview.php?camera=$other_cam&date=$DATE&hour=$HOUR&tag=$TAG'>$other_cam</a></div></h2>";

$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$photo_dir .= "/$DATE/$HOUR";
$images = [];
if($TAG=="person"){
    $images = getPersonImages($photo_dir);
}else{
    $images = getOtherImages($photo_dir);
}
foreach($images as $index => $img){
    $cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
    $img_link = "./$cam/$DATE/$HOUR/$img";
    $thumb_link = "./$cam/$DATE/$HOUR/thumbnails/$img";
    if(!file_exists("$HDD_ROOT/$cam/$DATE/$HOUR/thumbnails/$img")){
        $thumb_link = $img_link;
    }
    echo "<a target='_blank' href='$img_link'><img src='$thumb_link' alt='Forest'></a>";
}
echo '</body>';
echo '</html>';
?>
