<?php
$HDD_ROOT = "/mnt/hdd";
$GATE_VIDEO_DIR = $HDD_ROOT."/GateVideos";
$STAIRS_VIDEO_DIR = $HDD_ROOT."/StairsVideos";

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$video_dir = $CAMERA=="Gate"? $GATE_VIDEO_DIR : $STAIRS_VIDEO_DIR;
$date_dirs = array_filter(glob($video_dir."/$DATE/*"), 'is_dir');
arsort($date_dirs);

echo '<html>';
echo "<title>$CAMERA Videos ($DATE)</title>";
echo "<head><h1><center><a href='../../'>GateVideos</a> ($DATE) </a></center></h1></head>";
echo '<body>';
foreach($date_dirs as $k => $v){
    $hour = basename($v);
    echo "<h2><A href='./21hour'>$hour</A> (4 Videos)</h2>";
}
echo '</body>';
echo '</html>';
?>