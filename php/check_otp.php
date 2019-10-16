<?php
	include("_con.php");
	//check for VALID OTP
	$otp = mysqli_real_escape_string($GLOBALS['db_connection'], $_POST['otp']);
	$sql_check = "SELECT * FROM login WHERE reset_token = '$otp' AND type='user'";
	$result = mysqli_query($GLOBALS['db_connection'], $sql_check);
	$row = mysqli_num_rows($result);
	if($row == 0){
		die("Wrong OTP");
	}
?>
