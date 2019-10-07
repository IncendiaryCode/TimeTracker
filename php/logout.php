<?php
	//Logout code
include('con.php');
	session_start();
	$time = date("H:i:s");
	$user_id=$_SESSION['user_id'];
	$_SESSION['logout_time']=$time;
	//print_r($time);
	$sql="UPDATE login SET logout_time='".$time."' WHERE ref_id='".$user_id."'";
	
	$res=mysqli_query($con,$sql);
	//$_SESSION['logout_flag']="Logged Out";
	if(session_destroy()){
		header("Refresh:1;URL=index.php");
	}
?>