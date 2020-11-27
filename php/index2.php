<?php
require_once('authorize.php');
require 'vars.php';
include ('dbcommon.php');

$conn = getConn();
$result = mysqli_query($conn, $get_dates_query);
$dates = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($dates, $row["date"]);
}

$free_space = HumanSize(disk_free_space($HDD_ROOT));
$per_full = (1- disk_free_space($HDD_ROOT)/disk_total_space($HDD_ROOT))*100;
$per_full = sprintf('%1.2f' , $per_full)
?>

<html>
<head>
    <title>Narahari Home CCTV Monitoring System</title>
    <head><h1><center>Narahari Home CCTV Monitoring System</center></h1>
        <center><b>Last Updated on : <?php echo date("Y-m-d h:i:sa")?>
                &emsp;Storage: <?php echo "$per_full % Full, $free_space" ?> Available</b></center>
    </head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
<br>
<br>
<table style=width:100%>
    <tr>
        <th><h2>Gate Photos</h2></th>
        <th><h2>Stairs Photos</h2></th>
        <?php
        echo "<th><h2>Gate Videos</h2></th>";
        echo "<th><h2>Stairs Videos</h2></th>";
        echo '</tr>';
        foreach($dates as $k => $v){
            $v = basename($v);
            echo '<tr>';
            echo "<td><h2><a href='./photos.php?camera=Gate&date=$v'> $v </a></h2></td>";
            if(file_exists("$STAIRS_PHOTO_DIR/$v")){
                echo "<td><h2><a href='./photos.php?camera=Stairs&date=$v'> $v </a></h2></td>";
            }else{
                echo "<td><h2>Not Available</h2></td>";
            }
            echo "<td><h2><a href='./videos.php?camera=Gate&date=$v'> $v </a></h2></td>";
            if(file_exists("$STAIRS_PHOTO_DIR/$v")){
                echo "<td><h2><a href='./videos.php?camera=Stairs&date=$v'> $v </a></h2></td>";
            }else{
                echo "<td><h2>Not Available</h2></td>";
            }

            echo '</tr>';
        }
        echo '</table>';
        if(file_exists("$HDD_ROOT/logs/internet_autologin")){
            echo "<h2><a href='./logs/internet_autologin/log.txt'> Internet Autologin</a></h2>";
        }
        if(file_exists("$HDD_ROOT/logs/nightly")){
            echo "<h2><a href='./logs/nightly/log.txt'> Nightly Log </a></h2>";
        }
        ?>
        <h2><a href='./logs.php'> Logs </a></h2>
        <h2><a href='./summary.php'> Summary </a></h2>

</body>
</html>
