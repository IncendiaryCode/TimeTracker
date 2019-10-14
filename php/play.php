<?php
	include('_con.php');
	session_start();
	//echo "HIII".$_SESSION['user'];
	$user=$_SESSION['user'];
	$user_id=$_POST['user_id'];
	//$task_id=$_SESSION['id'];
//$task_id=$_POST['task_id'];
	//print_r($_POST);exit();
	$j=$_POST['info'];
	$info=json_decode($j,true);
	$td=strtotime($info['date']);
	$task_date = date('Y-m-d',$td);
	$start_t=$_SESSION['login_time'];
	$s="SELECT project_id FROM project_assignee WHERE user_id='".$user_id."'";
	$r=mysqli_query($GLOBALS['db_connection'],$s);
	$row = mysqli_fetch_assoc($r);
	$sql="INSERT INTO time_details(ref_id,project_id,t_date,start_time,end_time,created_on) VALUES('".$user_id."','".$row['project_id']."','".$task_date."','".$info['started']."','".$info['ended']."','".date('Y-m-d H:i:s')."')";
	
	$res=mysqli_query($GLOBALS['db_connection'],$sql); 
	if($res == TRUE){
		//echo "updated.";
		$last_id = mysqli_insert_id($GLOBALS['db_connection']);
		$_SESSION['table_id']=$last_id;
	}
	else{
		echo"Unable to update:<br>".mysqli_error($GLOBALS['db_connection']);
	}
?>