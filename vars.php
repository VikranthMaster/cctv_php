<?php
$HDD_ROOT = "/mnt/hdd";
$GATE_PHOTO_DIR = $HDD_ROOT."/GatePhotos";
$STAIRS_PHOTO_DIR = $HDD_ROOT."/StairsPhotos";
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
    $p_images = [];
    if(file_exists("$root_dir/person.txt")){
        $person_file = fopen("$root_dir/person.txt","r");
        while(!feof($person_file)){
            $line = rtrim(fgets($person_file));
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
    return $p_images;
}

function getOtherImages($root_dir){
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
        $all_images = glob("$root_dir/*.jpg");
        $p_images = getPersonImages($root_dir);
        $o_images = array_flip(array_diff_key(array_flip($all_images),array_flip($p_images)));
        $o_images = array_map("basename",$o_images);
    }
    return $o_images;
}

?>
