<?php
$HDD_ROOT = "/mnt/hdd";
$GATE_VIDEO_DIR = $HDD_ROOT."/GateVideos";
$STAIRS_VIDEO_DIR = $HDD_ROOT."/StairsVideos";

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$HOUR = $_GET["hour"];

$int_hour = substr($HOUR,0,2);
$prev_hour = sprintf("%'.02d",$int_hour-1);
$next_hour = sprintf("%'.02d",$int_hour+1);
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";

echo '<html>';
echo "<title>$CAMERA Videos ($DATE) ($HOUR)</title>";
echo "<head><h1><center><a href='../../../'>$CAMERA Videos</a>&nbsp<a href='./videos.php?camera=$CAMERA&date=$DATE'>($DATE)</a> ($HOUR) </a></center></h1></head>";
echo '<body>';
echo '<h2><h2>';

if($int_hour!="00"){
    echo "<div style='float: left'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$prev_hour"."hour'> Previous</a> (18hour)</div>";
}
if($int_hour!="23"){
    echo "<div style='float: right'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$next_hour"."hour'> Next</a> (20hour)</div>";
}

echo "<div style='margin: auto; width: 250px;'>";
echo "<a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$HOUR&tag=person'>Photos</a>&emsp;&emsp;<a href='./video_preview.php?camera=$other_cam&date=$DATE&hour=$HOUR'>$other_cam</a></div></h2>";

$video_dir = $CAMERA=="Gate"? $GATE_VIDEO_DIR : $STAIRS_VIDEO_DIR;
$video_dir .= "/$DATE/$HOUR";
$all_videos = glob("$video_dir/*.mp4");
foreach($all_videos as $index => $vid){
    $vid_name = basename($vid);
    echo "<h2><a href='./$CAMERA"."Videos/$DATE/$HOUR/$vid_name'>$vid_name</a> (54.55 MB)</h2>";
}

echo '</body></html>';
?>
