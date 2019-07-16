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
?>