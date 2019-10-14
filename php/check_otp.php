<?php
	include("_con.php");
	$otp = mysqli_real_escape_string($GLOBALS['db_connection'], $_POST['otp']);
	//print_r($_POST);exit;
	$sql_check = "SELECT * FROM login WHERE reset_token = '$otp' AND type='user'";
	//echo $sql_check;
	$result = mysqli_query($GLOBALS['db_connection'], $sql_check);
	$row = mysqli_num_rows($result);
	if($row == 0){
		die("Wrong OTP");
	}
?>
