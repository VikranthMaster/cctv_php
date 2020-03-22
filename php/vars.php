<?php
$HDD_ROOT = "/mnt/hdd/tmp";
$GATE_PHOTO_DIR = $HDD_ROOT."/GateCamera";
$STAIRS_PHOTO_DIR = $HDD_ROOT."/StairsCamera";
$GATE_VIDEO_DIR = $HDD_ROOT."/GateVideos";
$STAIRS_VIDEO_DIR = $HDD_ROOT."/StairsVideos";

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
    $root_dir = $root_dir."/jpg";
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

function getOtherImages($root_dir){
    $o_images = [];
    $root_dir = $root_dir."/jpg";
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
        #$all_images = glob("$root_dir/*.jpg");
        $p_images = getPersonImages($root_dir);
        $o_images = array_flip(array_diff_key(array_flip($all_images),array_flip($p_images)));
        $o_images = array_map("basename",$o_images);
    }

    sort($o_images);
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
        $o_images = getOtherImages("$cam_dir/$DATE/$HOUR");
        reset($o_images);
        $img = current($o_images);
    }

    $thumb_img = "./$cam/$DATE/$HOUR/jpg/$img";
    if(file_exists("$cam_dir/$DATE/$HOUR/thumbnails/$img")){
        $thumb_img = "./$cam/$DATE/$HOUR/thumbnails/$img";
    }
    return $thumb_img;
}

?>
