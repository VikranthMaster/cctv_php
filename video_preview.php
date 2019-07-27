<?php
require_once('authorize.php');
require 'vars.php';

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$HOUR = $_GET["hour"];

$int_hour = substr($HOUR,0,2);
$prev_hour = sprintf("%'.02d",$int_hour-1);
$next_hour = sprintf("%'.02d",$int_hour+1);
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
?>
    <html>
    <title><?php echo "$CAMERA Videos ($DATE) ($HOUR)"?></title>
    <head>
        <style>
            div.vgallery {
                margin: 5px;
                border: 2px solid #ccc;
                float: left;
                width: 400px;
            }

            div.vgallery:hover {
                border: 2px solid #777;
            }

            div.vgallery img {
                width: 100%;
                height: auto;
            }

            div.desc {
                font-size: larger;
                font-weight: bolder;
                padding: 10px;
                text-align: center;
                background-color: #5DADE2;
            }
        </style>

<?php
echo "<h1><center><a href='./index.php'>$CAMERA Videos</a>&nbsp<a href='./videos.php?camera=$CAMERA&date=$DATE'>($DATE)</a> ($HOUR) </a></center></h1>";
echo "</head>";
echo '<body>';
echo '<h2><h2>';

if($int_hour!="00"){
    echo "<div style='float: left'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$prev_hour"."hour'> Previous</a> ($prev_hour hour)</div>";
}
if($int_hour!="23"){
    echo "<div style='float: right'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$next_hour"."hour'> Next</a> ($next_hour hour)</div>";
}

echo "<div style='margin: auto; width: 250px;'>";
echo "<a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$HOUR&tag=person'>Photos</a>&emsp;&emsp;<a href='./video_preview.php?camera=$other_cam&date=$DATE&hour=$HOUR'>$other_cam</a></div></h2>";

$video_dir = $CAMERA=="Gate"? $GATE_VIDEO_DIR : $STAIRS_VIDEO_DIR;
$video_dir .= "/$DATE/$HOUR";
$all_videos = glob("$video_dir/*.mp4");
foreach($all_videos as $index => $vid){
    $vid_name = basename($vid);

    echo "<div class='vgallery'>";
    echo "<video width='400' controls>";
    echo "<source src='./$CAMERA"."Videos/$DATE/$HOUR/$vid_name'>$vid_name' type='video/mp4'>";
    echo '</video>';
    echo "<div class='desc'>$vid_name(".HumanSize(filesize($vid)).") </div>";
    echo '</div>';
}

echo '</body></html>';
?>