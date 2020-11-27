<?php
include ('dbcommon.php');

// Change below two lines to test query
$cols = array("date");
$query = $get_dates_query;

$conn = getConn();
$result = mysqli_query($conn, $query);
if (is_bool($result) && (mysqli_num_rows($result) == 0)) {
    // print($error_msg, "Query ERROR: Failed to get summary data<br>" . __FILE__ ." line:". __LINE__ );
}

$values = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $entry = array();
    foreach($cols as $k => $v) {
	array_push($entry, strval($row[$v]));    
    }
    array_push($values, $entry);
}
?>

<!DOCTYPE html>
<html>
<head>

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
		<table>
			<tr>
<?php
    foreach($cols as $k => $v) {
	echo "<th><h2>$v</th>\n";		
    }?>
			</tr>
     
    <?php
    foreach($values as $k1 => $v1) {
	echo "<tr>\n";
	foreach($v1 as $k2 => $v2) {
	    echo "<td><h3>$v2</td>\n";
	}
	echo "</tr>\n";
    }
?>
   
	</table>
</body>
</html>

