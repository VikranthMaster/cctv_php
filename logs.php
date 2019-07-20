<?php
$LOG_ROOT = "/var/www/html/logs";
$TEMPERATURE_LOGS = $LOG_ROOT."/temperature";
?>

<html>
    <head>
        <head><h1><center>CCTV Monitoring System Logs</center></h1>
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
        <table style=width:100%>
            <tr>
                <th><h2>Process footage</h2></th>
                <th><h2>Person Detect</h2></th>
                <th><h2>Temperature</h2></th>
            </tr>

<?php
$logs = glob("$TEMPERATURE_LOGS/*.txt");
arsort($logs);
foreach($logs as $k => $v){
    $v = basename($v);
    $date = substr($v,4,10);
    echo '<tr>';
    if(file_exists($LOG_ROOT."/process/$v")) {
        echo "<td><h2><a href='./logs/process/$v'> $date </a></h2></td>";
    }else{
        echo "<td><h2>Not Available</h2></td>";
    }
    if(file_exists($LOG_ROOT."/person_detect/$v")){
        echo "<td><h2><a href='./logs/person_detect/$v'> $date </a></h2></td>";
    }else{
        echo "<td><h2>Not Available</h2></td>";
    }
    echo "<td><h2><a href='./logs/temperature/$v'> $date </a></h2></td>";
    echo '</tr>';
}
?>
        </table>
    </body>
</html>
