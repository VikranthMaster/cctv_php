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
echo "<head><h1><center><a href='../../'>$CAMERA Videos</a> ($DATE) </a></center></h1></head>";
echo '<body>';
foreach($date_dirs as $k => $v){
    $hour = basename($v);
    $all_videos = glob("$v/*.mp4");
    echo "<h2><A href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$hour'>$hour</A> (".count($all_videos)"." Videos)</h2>";
}
echo '</body>';
echo '</html>';
?>