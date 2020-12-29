<?php
require 'vars.php';
require_once ('authorize.php');
include ('conn.php');
include ('queries.php');

$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];

$cols = array(
    "Hour", "PersonCount", "TotalCount", "cachedir", "time"
);
$photos = runQueryWithTwoArg($get_photos_query, $CAMERA, $DATE, $cols);
$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;

?>

<html>
<title><?php echo "$CAMERA Photos ($DATE)"?></title>
<head><h1>
<center>
<a href='./index.php'><?php echo $CAMERA ?>Photos</a> 
<?php echo "($DATE)"?>
<?php
if(file_exists("$HDD_ROOT/training")) {
    echo "<a href='./train.php?camera=$CAMERA&date=$DATE'>[Train]</a>";
}?>

</center></h1>
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
if(file_exists("$photo_dir/$yday")){
    echo "<div style='float: left'><a href='./photos.php?camera=$CAMERA&date=$yday'> Previous</a> ($yday)</div>\n";
}
if(file_exists("$photo_dir/$tmrw")) {
    echo "<div style='float: right'><a href='./photos.php?camera=$CAMERA&date=$tmrw'> Next</a> ($tmrw)</div>\n";
}
echo "<div style='margin: auto; width: 250px;'><a href='./photos.php?camera=$other_cam&date=$DATE'>$other_cam</a></div></h2>\n";

foreach($photos as $k => $v){
    $thumb =  $v[3]."/".$DATE."/".str_replace(":","_",$v[4]).".jpg";
    $thumb = ".".getRelativePath($HDD_ROOT,$thumb);
    $hour = sprintf("%02d",$v[0]);

    echo "<div class='gallery''>\n";
    echo "<a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$hour'> <img src=$thumb alt='Cinque Terre' width='600' height='400'> </a>\n";
    echo "<div class='desc'>$v[0] ( $v[1] / $v[2] ) </div>\n";
    echo "</div>\n";
}
?>

</body>
</html>
