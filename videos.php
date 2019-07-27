<?php
require_once('authorize.php');
require 'vars.php';

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$video_dir = $CAMERA=="Gate"? $GATE_VIDEO_DIR : $STAIRS_VIDEO_DIR;
$date_dirs = array_filter(glob($video_dir."/$DATE/*"), 'is_dir');
arsort($date_dirs);
?>

<html>
<title><?php echo "$CAMERA Photos ($DATE)"?></title>
<head><h1><center><a href='./index.php'><?php echo $CAMERA ?>Photos</a> <?php echo "($DATE)"?> </a></center></h1>
    <style>
        div.gallery {
            margin: 5px;
            border: 2px solid #ccc;
            float: left;
            width: 300px;
        }

        div.gallery:hover {
            border: 2px solid #777;
        }

        div.gallery img {
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
</head>
<body>

<?php
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
$today = strtotime($DATE);
$yday = date('Y-m-d', strtotime('-1 day', $today ));
$tmrw = date('Y-m-d', strtotime('1 day', $today));
echo '<h2>';
if(file_exists("$video_dir/$yday")){
    echo "<div style='float: left'><a href='./videos.php?camera=$CAMERA&date=$yday'> Previous</a> ($yday)</div>\n";
}
if(file_exists("$video_dir/$tmrw")) {
    echo "<div style='float: right'><a href='./videos.php?camera=$CAMERA&date=$tmrw'> Next</a> ($tmrw)</div>\n";
}
echo "<div style='margin: auto; width: 250px;'><a href='./videos.php?camera=$other_cam&date=$DATE'>$other_cam</a></div></h2>\n";

$cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
foreach($date_dirs as $k => $v){
    $hour = basename($v);
    $all_videos = glob("$v/*.mp4");
    $video_count = count($all_videos);

    if($video_count==0) continue;


    $p_images = getPersonImages("$HDD_ROOT/$cam/$DATE/$hour");
    $o_images = getOtherImages("$HDD_ROOT/$cam/$DATE/$hour");

    $p_count = count($p_images);

    reset($p_images);
    $img = current($p_images);
    if($p_count==0){
        reset($o_images);
        $img = current($o_images);
    }

    $img_link = "./$cam/$DATE/$hour/$img";
    $thumb_link = "./$cam/$DATE/$hour/thumbnails/$img";
    if(!file_exists("$HDD_ROOT/$cam/$DATE/$hour/thumbnails/$img")){
        $thumb_link = $img_link;
    }

    echo "<div class='gallery''>\n";
    echo "<a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$hour'> <img src=$thumb_link alt='Cinque Terre' width='600' height='400'> </a>\n";
    echo "<div class='desc'>$hour ($video_count) </div>\n";
    echo "</div>\n";
}
?>
</body>
</html>