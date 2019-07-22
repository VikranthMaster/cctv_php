<?php
$username = 'admin';
$password = 'ericsson';
if(!isset($_SERVER['PHP_AUTH_USER'])
	|| !isset($_SERVER['PHP_AUTH_PW'])
	||($_SERVER['PHP_AUTH_USER']!=$username)
	||($_SERVER['PHP_AUTH_PW']!=$password)){
    header('WWW-Authenticate: Basic realm="cctv_php"');
    header('HTTP/1.0 401 Unauthorized');
    exit('<h2>Narahari Home CCTV Monitoring System</h2>Sorry, you must enter a valid user name and password to access this page.');
}
?>
