<?php
require 'vars.php';
require_once('authorize.php');
$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$date_dirs = array_filter(glob($photo_dir."/$DATE/*"), 'is_dir');
$train_file = $CAMERA=="Gate"? $HDD_ROOT."/training/GatePhotos/$DATE/train.txt" : $HDD_ROOT."/training/StairsPhotos/$DATE/train.txt" ;

arsort($date_dirs);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php echo "$CAMERA Photos ($DATE)" ?> </title>

    <?php 
	echo "<h1><center>";
	echo "<a href='../../../'>$CAMERA Photos</a>&nbsp<a href='./photos.php?camera=$CAMERA&date=$DATE'>($DATE)</a>";
	if(file_exists($train_file)){
	    echo "&nbsp; <button onclick='update()'>UPDATE</button>";
	}
	else{
	    echo "&nbsp; <button onclick='create()'>CREATE</button>";
	}
	echo "</center></h1>";

    ?>
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

foreach($date_dirs as $k => $v){
$HOUR = basename($v);
$photo_hour_dir = "$photo_dir/$DATE/$HOUR";
$p_images = getPersonImages($photo_hour_dir);
if(file_exists("$photo_hour_dir/person.txt")){
    $person_file = fopen("$photo_hour_dir/person.txt","r");
    $cam = $CAMERA=="Gate"?"GatePhotos":"StairsPhotos";
    while(!feof($person_file)){
        $line = rtrim(fgets($person_file));
        if (strpos($line, " ") !== false) {
            $f_name = explode(" ", $line)[0];
            $boxes = rtrim(substr($line,strlen($f_name)+1));

            $img_link = "./$cam/$DATE/$HOUR/$f_name";
            $thumb_link = "./$cam/$DATE/$HOUR/thumbnails/$f_name";
            if(file_exists("$HDD_ROOT/$cam/$DATE/$HOUR/thumbnails/$f_name")){
                $img_link = $thumb_link;
            }
	    $img_id = str_replace (".", "_", $f_name);
	    echo "<p id='$img_id'>\n";
            echo "<canvas id='$img_link' boxes='$boxes' width='640' height='360' style='border:1px solid #d3d3d3;'></canvas>\n";
	    echo "<button onclick=\"remove('".$img_id."')\">Remove</button>\n";
	    echo "</p>\n";
        }
    }
    fclose($person_file);
}
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
    function remove(img) {
	document.getElementById(img).remove();
    }

    function create(){
	alert("Create called");
	var file = <?php echo "'$train_file'"?>;
	alert(file);
	var ps = document.getElementsByTagName("p");
	alert(ps.length);
	CreateFile(file);
    }

    function update(){
	var file = <?php echo "'$train_file'"?>;
	alert(file);
	var ps = document.getElementsByTagName("p");
	alert(ps.length);
    }

    function CreateFile(file){
	// Requiring fs module in which 
	// writeFile function is defined. 
	const fs = require('fs') 
  
	// Data which will write in a file. 
	let data = "Learning how to write in a file."
  
	// Write data in 'Output.txt' . 
	fs.writeFile(file, data, (err) => { 
      
	// In case of a error throw err. 
	if (err) throw err; 
	}) 
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
