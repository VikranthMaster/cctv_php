<?php
include ('dbcommon.php');
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
			<th><h2>Camera</th>
			<th><h2>Date</th>
			<th><h2>Photo Count</th>
		</tr>
     
    <?php
    $conn = getConn();
    $result = mysqli_query($conn, $get_summary_query);
    if (is_bool($result) && (mysqli_num_rows($result) == 0)) {
        // print($error_msg, "Query ERROR: Failed to get summary data<br>" . __FILE__ ." line:". __LINE__ );
    }
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        ?>

    <tr>
			<td><h3><?php echo $row["camName"]; ?></td>
			<td><h3><?php echo $row["date"]; ?></td>
			<td><h3><?php echo $row["count"];?></td>
		</tr>
   
   <?php
   }
   mysql_close($conn);?>
   	
	</table>
</body>
</html>

