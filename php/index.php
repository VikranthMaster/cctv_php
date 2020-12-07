<?php
require_once ('authorize.php');
require 'vars.php';
include ('conn.php');
include ('queries.php');

$cols = array(
    "date"
);
$dates = runQuery($get_dates_query, $cols);

$free_space = HumanSize(disk_free_space($HDD_ROOT));
$per_full = (1 - disk_free_space($HDD_ROOT) / disk_total_space($HDD_ROOT)) * 100;
$per_full = sprintf('%1.2f', $per_full)?>

<html>
<head>
<title>Narahari Home CCTV Monitoring System</title>

<head>
<h1>
	<center>Narahari Home CCTV Monitoring System</center>
</h1>
<center>
	<b>Last Updated on : <?php echo date("Y-m-d h:i:sa")?>
                &emsp;Storage: <?php echo "$per_full % Full, $free_space" ?> Available</b>
</center>
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
	<table style="width: 100%">
		<tr>
			<th><h2>Gate Photos</h2></th>
			<th><h2>Stairs Photos</h2></th>
			<th><h2>Gate Videos</h2></th>
			<th><h2>Stairs Videos</h2></th>
		</tr>
		
        <?php
        foreach ($dates as $k => $v) {
            foreach ($v as $k2 => $dt) {
                ?>
   		<tr>
			<td><h2>
					<a href='./photos.php?camera=Gate&date=<?php echo $dt; ?>'><?php echo $dt; ?> </a>
				</h2></td>
			<td><h2>
					<a href='./photos.php?camera=Stairs&date=<?php echo $dt; ?>'><?php echo $dt; ?> </a>
				</h2></td>
			<td><h2>
					<a href='./videos.php?camera=Gate&date=<?php echo $dt; ?>'><?php echo $dt; ?> </a>
				</h2></td>
			<td><h2>
					<a href='./videos.php?camera=Stairs&date=<?php echo $dt; ?>'><?php echo $dt; ?> </a>
				</h2></td>
		</tr>
   
   <?php }} ?>
        </table>
	<h2>
		<a href='./logs.php'> Logs </a>
	</h2>
	<h2>
		<a href='./summary.php'> Summary </a>
	</h2>
    <h2>
        <a href='./temperature.php'> Temperature </a>
    </h2>
</body>
</html>
