<?php
	include("_con.php");
	if(isset($_GET)){
		$get_users_q = "SELECT * FROM users";
		$query_result = mysqli_query($GLOBALS['db_connection'],$get_users_q);
		if($query_result == FALSE){
			echo "Unable to update:<br>".$get_users_q."  :  ".mysqli_error($GLOBALS['db_connection']);
		}else{
			$row = mysqli_num_rows($query_result);
		}
		$get_task_q = "SELECT * FROM time_details WHERE type = 'task'";
		$q_result = mysqli_query($GLOBALS['db_connection'],$get_task_q);
		if($q_result == TRUE){
			$row_task = mysqli_num_rows($q_result);
		}else{
			echo "Error: ".$get_task_q." : ".mysqli_error($GLOBALS['db_connection']);
		}
		$get_proj_q = "SELECT * FROM project";
		$query_res = mysqli_query($GLOBALS['db_connection'],$get_proj_q);
		if($query_res == TRUE){
			$row_proj = mysqli_num_rows($query_res);
			if($row_proj > 0){
			$fetch_proj_name = mysqli_fetch_all($query_res,MYSQLI_ASSOC);
			}
		}else{
			echo "Error: ".$get_proj_q." : ".mysqli_error($GLOBALS['db_connection']);
		}				
	}	    
?>