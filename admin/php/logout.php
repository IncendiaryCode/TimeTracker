<?php
	//Logout code
	include('_con.php');
	session_start();
	if(session_destroy()){
		header('Location:/time_tracker/index.php');
	}
?>