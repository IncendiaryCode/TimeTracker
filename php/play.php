<?php
	include('_con.php');
	session_start();
	$user = $_SESSION['user'];
	if ($_POST['action'] == 'login') {
		$type = 'login';
	}
	else
	{
		$type = 'task';
	}
	$user_id = $_SESSION['user_id'];
	//$json_data = $_POST['info'];
	$task_date = date('Y:m:d');
	$task_time = date('H:i:s');
	$start_t = $_SESSION['login_time'];
	$end_time = "00:00:00";
	/*$s="SELECT project_id FROM project_assignee WHERE user_id='".$user_id."'";
	$r=mysqli_query($GLOBALS['db_connection'],$s);
	$row = mysqli_fetch_assoc($r);*/
	$prject_id = 1; // default take 1 for login
	//@TODO fetch project_id from config in future
	$sql_query = "INSERT INTO time_details(ref_id,project_id,t_date,start_time,end_time,created_on) VALUES(".$user_id.",".$prject_id.",'".$task_date."','".$task_time."','".$end_time."','".date('Y-m-d H:i:s')."')";
	$result = mysqli_query($GLOBALS['db_connection'],$sql_query); 
	if($result == TRUE){
		//echo "updated.";
		$last_id = mysqli_insert_id($GLOBALS['db_connection']);
		$_SESSION['login_row_id'] = $last_id;
	}
	else{
		echo"Unable to update:<br>".mysqli_error($GLOBALS['db_connection']);
	}
?>