<?php
	include('con.php');
	session_start();
	$tabl_id=$_SESSION['table_id'];
	$user_id=$_POST['user_id'];
	$j=$_POST['info'];
	//$user=$_SESSION['user'];
	
	//$task_id=$_POST['task_id'];
	//print_r($_POST);exit();

	$info=json_decode($j,true);
	//print_r($info['started']);
	//print_r($_COOKIE['login_date']);
	$start_t=$_SESSION['login_time'];
	$sql="UPDATE time_details SET end_time='".$info['ended']."', start_time='".$info['started']."' WHERE id='".$tabl_id."' AND ref_id='".$user_id."'";
	//print_r($sql);
	$res=mysqli_query($con,$sql);
	if($res==FALSE){
		//echo "Updated.";
		//header('Refresh:1;URL=employeeActivities.php?logout='.$row['end_time']);
		echo"Error:".mysqli_error($con);
	}
?>