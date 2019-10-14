<?php
	include('_con.php');
	session_start();
	//$tabl_id=$_SESSION['table_id'];
	//$user=$_SESSION['user'];
	$user_id = $_SESSION['user_id'];
	//if(isset($_POST)){
	$task_name = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task-name']);
	//$task_desc=mysqli_real_escape_string($con,$_POST['td']);
	$task_start = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['startedDate']);
	$choose_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['chooseProject']);
	
	$td=strtotime($task_start);
	$task_start_time = date('H:i:s',$td);
	$task_date = date('Y-m-d',$td);
	$task_end = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['endedDate']);
	$tdd = strtotime($task_end);
	$task_end_time=date('H:i:s',$tdd);

	//Insertion of task info into db
	$sql="INSERT INTO time_details(type,ref_id,project_id,task_name,t_date,start_time,end_time,created_on) VALUES('task','".$user_id."','".$choose_p."','".$task_name."','".$task_date."','".$task_start_time."','".$task_end_time."','".date('Y-m-d H:i:s')."')";
	$res=mysqli_query($GLOBALS['db_connection'],$sql);
	if($res==TRUE){
		header("location:../user/home.php");
	}
?>