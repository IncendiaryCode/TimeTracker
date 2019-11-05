<?php
	include('_con.php');
	//session_start();
	$task_status ='';
	$user_id = $_SESSION['user_id'];
	$sql_query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id AS t_id,p.id,p.name,t.type FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.end_time = '00:00:00' AND t.type ='task' AND t.ref_id=".$user_id." LIMIT 1";
	//$sql_query .= " AND t.project_id = '$proj_id' AND t.task_name='$task' AND type='task'";

	$q_result = mysqli_query($GLOBALS['db_connection'],$sql_query);
	if($q_result == TRUE){
		$num_rows = mysqli_num_rows($q_result);
		$project_data = array();
		if($num_rows > 0){
			$task_status = mysqli_fetch_all($q_result,MYSQLI_ASSOC);
			/*echo $task_status;*/
		}
		else
		{
			$sql_query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id AS t_id,p.id,p.name,t.type FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.end_time = '00:00:00' AND t.type ='login' AND t.ref_id=".$user_id." LIMIT 1";

			$q_result = mysqli_query($GLOBALS['db_connection'],$sql_query);
			if($q_result == TRUE){
				$num_rows = mysqli_num_rows($q_result);
				$project_data = array();
				if($num_rows > 0){
					$task_status = mysqli_fetch_all($q_result,MYSQLI_ASSOC);
							//echo $project_data;
				}
			}else{
				echo "Error: ".mysqli_error($GLOBALS['db_connection']);
			}
		}
	}else{
		echo "Error: ".mysqli_error($GLOBALS['db_connection']);
	}
?>
