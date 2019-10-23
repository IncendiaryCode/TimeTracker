<?php
	include("../_con.php");
	if(isset($_POST)){
		$user_name = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['user-name']);
		$task_name = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task_name']);
		$description = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['description']);
		$choose_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['chooseProject']);
		$task_end_time = "00:00:00";
		$query = "SELECT id FROM users WHERE name='$user_name'";
		$query_result = mysqli_query($GLOBALS['db_connection'],$query);
		if($query_result == TRUE){
			$user_id = mysqli_fetch_assoc($query_result);
			$sql_query = "SELECT id FROM project WHERE name = '$choose_p'";
	    	$result = mysqli_query($GLOBALS['db_connection'],$sql_query);
	    	if($result == TRUE){
		    	$row = mysqli_fetch_assoc($result);
		    	$sql_q = "INSERT INTO time_details(type,ref_id,project_id,task_name,description,t_date,start_time,end_time,created_on) VALUES('task','".$user_id['id']."','".$row['id']."','".$task_name."','".$description."','".date('Y:m:d')."','".date('H:i:s')."','".$task_end_time."','".date('Y-m-d H:i:s')."')";
		    	$res = mysqli_query($GLOBALS['db_connection'],$sql_q);
		    	if($res == TRUE){
		    		header('Location:../ui/index.php');
		    	}else{
		    		echo "Error: ".$sql_q." : ".mysqli_error($GLOBALS['db_connection']);
		    	}
			}else{
				echo "Error : ".$sql_query." : ".mysqli_error($GLOBALS['db_connection']);
			}
		}else{
			echo "Error: ".$query. " : ".mysqli_error($GLOBALS['db_connection']);
		}
	}else{
		echo "Fail";
	}
?>