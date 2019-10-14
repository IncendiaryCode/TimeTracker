<?php
	//DB connection
	$servername="localhost";
	$username="DB USERNAME";
	$password="DB PASSWORD";
	$database="DB NAME";

	$GLOBALS['db_connection'] = mysqli_connect($servername, $username, $password,$database);	
	if (!$GLOBALS['db_connection']) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_select_db($GLOBALS['db_connection'], $database);
?>