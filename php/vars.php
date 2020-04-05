<?php
$HDD_ROOT = "/mnt/hdd/tmp";
$CACHE_ROOT = "/mnt/hdd/tmp/cache";
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

function getCacheDir($CAMERA, $DATE, $HOUR){
    global $HDD_ROOT;
    global $CACHE_ROOT;
    global $GATE_PHOTO_DIR;
    global $STAIRS_PHOTO_DIR;
	$cam = $CAMERA=="Gate"?"GateCamera":"StairsCamera";
    $cam_dir = $CAMERA=="Gate"?$GATE_PHOTO_DIR:$STAIRS_PHOTO_DIR;
    $cam_dir = str_replace($HDD_ROOT, $CACHE_ROOT, $cam_dir);
	$root_dir = "$cam_dir/$DATE/$HOUR";
    return $root_dir;
}

function getImages($CAMERA, $DATE, $HOUR){
	global $GATE_PHOTO_DIR;
    global $STAIRS_PHOTO_DIR;
	$cam = $CAMERA=="Gate"?"GateCamera":"StairsCamera";
    $cam_dir = $CAMERA=="Gate"?$GATE_PHOTO_DIR:$STAIRS_PHOTO_DIR;
	$root_dir = "$cam_dir/$DATE";
	if ($CAMERA=="Gate") {
		$root_dir = $root_dir."/$HOUR";
	}

    $all_images = glob("$root_dir/{,*/,*/*/,*/*/*/}*.jpg", GLOB_BRACE);
		
    if($CAMERA!="Gate"){
        $all_images = glob("$root_dir/*/jpg/$HOUR/*/*.jpg", GLOB_BRACE);
    }
		
    return $all_images;
}

function getPersonImages($CAMERA, $DATE, $HOUR){
    $cache_dir = getCacheDir($CAMERA, $DATE, $HOUR);
    $p_images = [];
    if(file_exists("$cache_dir/person.txt")){
        $person_file = fopen("$cache_dir/person.txt","r");
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
    sort($p_images);
    return $p_images;
}

function getOtherImages($CAMERA, $DATE, $HOUR){
    $cache_dir = getCacheDir($CAMERA, $DATE, $HOUR);
    $o_images = [];
    if(file_exists("$cache_dir/others.txt")){
        $other_file = fopen("$cache_dir/others.txt","r");
        while(!feof($other_file)){
            $line = rtrim(fgets($other_file));
            if($line!=''){
                array_push($o_images,$line);
            }
        }
        fclose($other_file);
    }else{
		$o_images = getImages($CAMERA, $DATE, $HOUR);
    }

    sort($o_images);
    return $o_images;
}

function videosExists(){
    global $GATE_VIDEO_DIR;
    return file_exists($GATE_VIDEO_DIR);
}

function getThumbnailPath($CAMERA, $DATE, $HOUR, $IMG){
    global $HDD_ROOT;
    global $CACHE_ROOT;
    $thumbPath = str_replace($HDD_ROOT, $CACHE_ROOT, $IMG);
    $index = strpos($thumbPath,$DATE);
    $thumbPath = substr($thumbPath, 0, $index).$DATE."/".$HOUR;
    if ($CAMERA=="Gate") {
        $thumbPath = $thumbPath."/".basename($IMG);
    } else {
        $sp = explode("/",$IMG);
        $cnt = count($sp);
        $thumbPath =  $thumbPath."/".($sp[$cnt-3].".".$sp[$cnt-2].".".$sp[$cnt-1]);
    }
    
    return $thumbPath;

}

function getThumbImage($CAMERA, $DATE, $HOUR){
    global $GATE_PHOTO_DIR;
    global $STAIRS_PHOTO_DIR;
    global $HDD_ROOT;
    global $CACHE_ROOT;
    $cam = $CAMERA=="Gate"?"GateCamera":"StairsCamera";
    $cam_dir = $CAMERA=="Gate"?$GATE_PHOTO_DIR:$STAIRS_PHOTO_DIR;

    $p_images = getPersonImages($CAMERA, $DATE, $HOUR);
    reset($p_images);
    $img = current($p_images);

    $p_count = count($p_images);
    if($p_count==0){
        $o_images = getOtherImages($CAMERA, $DATE, $HOUR);
        reset($o_images);
        $img = current($o_images);
    }

    $thumb_img = getThumbnailPath($CAMERA, $DATE, $HOUR, $img);

    if(!file_exists("$thumb_img")){
        $thumb_img = $img;
    }

    return $thumb_img;
}

?>
