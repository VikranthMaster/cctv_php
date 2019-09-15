<?php
require 'vars.php';
require_once('authorize.php');
$CAMERA = $_GET["camera"];
$DATE = $_GET["date"];
$photo_dir = $CAMERA=="Gate"? $GATE_PHOTO_DIR : $STAIRS_PHOTO_DIR;
$date_dirs = array_filter(glob($photo_dir."/$DATE/*"), 'is_dir');
$train_file = $CAMERA."_".$DATE.".lbl";

arsort($date_dirs);

if(isset($_POST['submit'])){
    if(file_exists($train_file)){
	//Delete file
	$de = unlink($train_file);
    }
    $fh = fopen($train_file, 'a');
    if(!empty($_POST['check_list'])){
	foreach($_POST['check_list'] as $selected){
	    fwrite($fh, $selected."\n");
	}
    }
    fclose($fh);
    if(file_exists($train_file)){
	echo "TRAINING FILE CREATED...";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php echo "$CAMERA Photos ($DATE)" ?> </title>
    <style>
.myButton {
	background-color:#44c767;
	-moz-border-radius:28px;
	-webkit-border-radius:28px;
	border-radius:28px;
	border:1px solid #18ab29;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:22px;
	padding:13px 20px;
	text-decoration:none;
	text-shadow:0px 1px 0px #2f6627;
}
.myButton:hover {
	background-color:#5cbf2a;
}
.myButton:active {
	position:relative;
	top:1px;
}

    </style>
    <?php 
	echo "<h1><center>";
	echo "<a href='../../../'>$CAMERA Photos</a>&nbsp<a href='./photos.php?camera=$CAMERA&date=$DATE'>($DATE)</a>";
	echo "</center></h1>";

    ?>
</head>
<body>
<?php
$other_cam = $CAMERA=="Gate"? "Stairs" : "Gate";
$today = strtotime($DATE);
$yday = date('Y-m-d', strtotime('-1 day', $today ));
$tmrw = date('Y-m-d', strtotime('1 day', $today));
echo "<form action='#' method='post'>\n";
echo '<h2>';
if(file_exists("$photo_dir/$yday")){
    echo "<div style='float: left'><a href='./photos.php?camera=$CAMERA&date=$yday'> Previous</a> ($yday)</div>\n";
}
if(file_exists("$photo_dir/$tmrw")) {
    echo "<div style='float: right'><a href='./photos.php?camera=$CAMERA&date=$tmrw'> Next</a> ($tmrw)</div>\n";
}
echo "<div style='margin: auto; width: 250px;'><a href='./photos.php?camera=$other_cam&date=$DATE'>$other_cam</a> &nbsp;";
if(file_exists($train_file)){
    echo "<input type='submit' class='myButton' name='submit' value='Update'/>";
}
else{
    echo "<input type='submit' class='myButton' name='submit' value='Create'/>";
}
echo "</div></h2>\n";
foreach($date_dirs as $k => $v){
$HOUR = basename($v);
$photo_hour_dir = "$photo_dir/$DATE/$HOUR";
$p_images = getPersonImages($photo_hour_dir);
$files = "";
if(file_exists($train_file)){
    $myfile = fopen($train_file,'r');
    $files = fread($myfile, filesize($train_file));
    fclose($myfile);
}
    
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
	    if(file_exists($train_file) and strpos($files,$img_id)===false){
		continue;
	    }
	    echo "<p id='$img_id'>\n";
            echo "<canvas id='$img_link' boxes='$boxes' width='640' height='360' style='border:1px solid #d3d3d3;'></canvas>\n";
	    echo "<input onclick=\"remove('".$img_id."')\" type='checkbox' name='check_list[]' value='$img_id' checked><label>DELETE</label><br/>";
	    echo "</p>\n";
        }
    }
    fclose($person_file);
}
}
echo "</form>";
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
	const fs = require('fs');
  
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
