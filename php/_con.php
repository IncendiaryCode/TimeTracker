<?php
	//DB connection
	$servername="localhost";
	$username="root";
	$password="gp.@1234";
	$database="time_tracker";

	$GLOBALS['db_connection'] = mysqli_connect($servername, $username, $password,$database);	
	if (!$GLOBALS['db_connection']) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_select_db($GLOBALS['db_connection'], $database);
?>