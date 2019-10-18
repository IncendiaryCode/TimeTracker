<?php
	include('_con.php');
	session_start();
	$user_id = $_SESSION['user_id'];
	$task_name = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task-name']);
	$task_desc=mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task_desc']);
	$task_start = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['startedDate']);
	$choose_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['chooseProject']);
	$time_start = ($task_start) ? strtotime($task_start) : time();
	$task_start_time = date('H:i:s',$time_start);
	$task_start_date = date('Y-m-d',$time_start);
	$task_end = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['endedDate']);
	if(empty($task_end)){ //if end time is not specified
    	$task_end_time = '00:00:00';
    }
    else
    {
    	$time_end = ($task_end) ? strtotime($task_end) : time();
		$task_end_time = date('H:i:s',$time_end);
    }	
    
	//Insertion of task info into db
    $sql_query = "SELECT id FROM project WHERE name = '$choose_p'";
    $result = mysqli_query($GLOBALS['db_connection'],$sql_query);
    if($result == TRUE){
	    //$num_of_rows = mysqli_num_rows($result);
	    $row = mysqli_fetch_assoc($result);
	    if (isset($_POST['task_id'])) {
	    	$task_id = $_POST['task_id'];
	    	$sql = "UPDATE time_details SET project_id = ".$row['id'].",task_name = '".$task_name."',description= '".$task_desc."',t_date = '".$task_start_date."',start_time = '".$task_start_time."',end_time = '".$task_end_time."',modified_on = '".date('Y-m-d H:i:s')."' WHERE id='$task_id'";
	    }
	    else
	    {
	    	$sql = "INSERT INTO time_details(type,ref_id,project_id,task_name,description,t_date,start_time,end_time,created_on) VALUES('task','".$user_id."','".$row['id']."','".$task_name."','".$task_desc."','".$task_start_date."','".$task_start_time."','".$task_end_time."','".date('Y-m-d H:i:s')."')";
	    }

		$sql_res = mysqli_query($GLOBALS['db_connection'],$sql);
		if($sql_res == TRUE){
			header("location:../user/home.php");
		}
	}
?>
