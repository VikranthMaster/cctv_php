<?php
require 'vars.php';
require_once('authorize.php');
$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$HOUR = $_GET["hour"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php echo "$CAMERA Photos ($DATE) ($HOUR)" ?> </title>
    <?php echo "<h1><center><a href='../../../'>$CAMERA Photos</a>&nbsp<a href='./photos.php?camera=$CAMERA&date=$DATE'>($DATE)</a> ($HOUR)</a></center></h1>" ?>
</head>
<body>

<?php
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
$int_hour = substr($HOUR,0,2);
$prev_hour = sprintf("%'.02d",$int_hour-1);
$next_hour = sprintf("%'.02d",$int_hour+1);
echo "<h2><h2>";
if($int_hour!="00"){
    echo "<div style='float: left'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$prev_hour"."hour'> Previous</a> ($prev_hour hour)</div>\n";
}
if($int_hour!="23"){
    echo "<div style='float: right'><a href='./preview.php?camera=$CAMERA&date=$DATE&hour=$next_hour"."hour'> Next</a> ($next_hour hour)</div>\n";
}
echo "<div style='margin: auto; width: 250px;'><a href='./video_preview.php?camera=$CAMERA&date=$DATE&hour=$HOUR'>Videos</a>&emsp;&emsp;<a href='./preview.php?camera=$other_cam&date=$DATE&hour=$HOUR'>$other_cam</a></div></h2>\n";

$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$photo_dir .= "/$DATE/$HOUR";
$p_images = getPersonImages($photo_dir);

if(file_exists("$photo_dir/person.txt")){
    $person_file = fopen("$photo_dir/person.txt","r");
    while(!feof($person_file)){
        $line = rtrim(fgets($person_file));
//        echo $line;
        if (strpos($line, " ") !== false) {
            $f_name = explode(" ", $line)[0];
            $boxes = rtrim(substr($line,strlen($f_name)+1));
            echo "<canvas id='$photo_dir/$f_name' boxes='$boxes' width='640' height='360' style='border:1px solid #d3d3d3;'></canvas>\n";
        }
    }
    fclose($person_file);
}
?>

<script>
    var img_dict = {};
    var cs = document.getElementsByTagName("canvas");
    for (i = 0; i < cs.length; i++) {
        c = cs[i];
        var im = new Image();
        im.src = c.getAttribute("id");
        img_dict[c.getAttribute("id")] = im;
    }
    window.onload = function() {
        var cs = document.getElementsByTagName("canvas");

        for (i = 0; i < cs.length; i++) {
            c = cs[i];
            var ctx = c.getContext("2d");
            ctx.drawImage(img_dict[c.getAttribute("id")], 0, 0,640,360);

            boxes = c.getAttribute("boxes").split(" ");

            for(j=0;j<boxes.length;){

                // Blue rectangle
                ctx.beginPath();
                ctx.lineWidth = "5";
                ctx.strokeStyle = "blue";
                ctx.rect(boxes[j+1], boxes[j], boxes[j+3]-boxes[j+1],boxes[j+2]-boxes[0]); //Converted from 78 317 233 380 96.7
                ctx.stroke();

                ctx.lineWidth = "1.2";
                ctx.strokeStyle = "red";
                ctx.font = "18px sans-serif";
                ctx.strokeText(boxes[j+4], boxes[j+1], boxes[j]);
                j=j+5;
            }
        }
    }
</script>
</body>
</html>
