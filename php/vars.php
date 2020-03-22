<?php
$HDD_ROOT = "/mnt/hdd/tmp" ; #"/Applications/XAMPP/xamppfiles/htdocs/cctv";
$GATE_PHOTO_DIR = $HDD_ROOT."/GateCamera";
$STAIRS_PHOTO_DIR = $HDD_ROOT."/StairsCamera";
$GATE_VIDEO_DIR = $HDD_ROOT."/GateVideos";
$STAIRS_VIDEO_DIR = $HDD_ROOT."/StairsVideos";

function printArray($values)
{
	foreach($values as $k => $v){
		echo "$v<br>";
	}
}

function getRelativePath($base, $path) {
    // Detect directory separator
    #echo("Base: $base<br>");
    #echo("Path: $path<br>");
    
	$separator = substr($base, 0, 1);
    $base = array_slice(explode($separator, rtrim($base,$separator)),1);
    $path = array_slice(explode($separator, rtrim($path,$separator)),1);

    $ret =  $separator.implode($separator, array_slice($path, count($base)));
	#echo("Relpath: $ret");
	return $ret;
}

function HumanSize($Bytes)
{
    $Type=array("", "KB", "MB", "GB", "TB", "PB", "exa", "zetta", "yotta");
    $Index=0;
    while($Bytes>=1024)
    {
        $Bytes/=1024;
        $Index++;
    }
    return(sprintf('%1.2f' , $Bytes)." ".$Type[$Index]);
}

function getPersonImages($root_dir){
    $p_images = [];
    if(file_exists("$root_dir/person.txt")){
        $person_file = fopen("$root_dir/person.txt","r");
        while(!feof($person_file)){
            $line = rtrim(fgets($person_file));
            if (strpos($line, " ") !== false) {
                $line = explode(" ", $line)[0];
            }
            if($line!=''){
                array_push($p_images,$line);
            }
        }
        fclose($person_file);
    }
    if(file_exists("$root_dir/persons")){
        $p_images = glob("$root_dir/persons/*.jpg");
        $p_images = array_map("basename",$p_images);
    }
    sort($p_images);
    return $p_images;
}

function getOtherImages($CAMERA, $DATE, $HOUR){
	global $GATE_PHOTO_DIR;
    global $STAIRS_PHOTO_DIR;
	$cam = $CAMERA=="Gate"?"GateCamera":"StairsCamera";
    $cam_dir = $CAMERA=="Gate"?$GATE_PHOTO_DIR:$STAIRS_PHOTO_DIR;
	$root_dir = "$cam_dir/$DATE";
	if ($CAMERA=="Gate") {
		$root_dir = $root_dir."/$HOUR";
	}
	
	#echo "Calling getOtherImages: $root_dir<br>";
    $o_images = [];
    if(file_exists("$root_dir/others.txt")){
        $other_file = fopen("$root_dir/others.txt","r");
        while(!feof($other_file)){
            $line = rtrim(fgets($other_file));
            if($line!=''){
                array_push($o_images,$line);
            }
        }
        fclose($other_file);
    }else{
		$all_images = glob("$root_dir/{,*/,*/*/,*/*/*/}*.jpg", GLOB_BRACE);
		
		if($CAMERA!="Gate"){
			$all_images = glob("$root_dir/*/jpg/$HOUR/*/*.jpg", GLOB_BRACE);
		}
		
        #$all_images = glob("$root_dir/*.jpg");
        $p_images = getPersonImages($root_dir);
        $o_images = array_flip(array_diff_key(array_flip($all_images),array_flip($p_images)));
        #$o_images = array_map("basename",$o_images);
    }

    sort($o_images);
    #printArray($o_images);
    return $o_images;
}

function videosExists(){
    global $GATE_VIDEO_DIR;
    return file_exists($GATE_VIDEO_DIR);
}

function getThumbImage($CAMERA, $DATE, $HOUR){
    global $GATE_PHOTO_DIR;
    global $STAIRS_PHOTO_DIR;
    $cam = $CAMERA=="Gate"?"GateCamera":"StairsCamera";
    $cam_dir = $CAMERA=="Gate"?$GATE_PHOTO_DIR:$STAIRS_PHOTO_DIR;

    $p_images = getPersonImages("$cam_dir/$DATE/$HOUR");
    reset($p_images);
    $img = current($p_images);

    $p_count = count($p_images);
    if($p_count==0){
        $o_images = getOtherImages($CAMERA, $DATE, $HOUR);
        reset($o_images);
        $img = current($o_images);
    }

    $thumb_img = $img;
    if(file_exists("$cam_dir/$DATE/$HOUR/thumbnails/$img")){
        $thumb_img = "./$cam/$DATE/$HOUR/thumbnails/$img";
    }
    return $thumb_img;
}

?>
