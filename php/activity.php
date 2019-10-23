<?php
	include('_con.php');
	function get_activities($task_type){
		session_start();
		//login check
		if (!isset($_SESSION['user'])) {
			return FALSE;
		}
		//Choose task info from db
		$query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id AS t_id,p.id,p.name,t.description FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.ref_id=".$_SESSION['user_id'];
		if ($task_type == 'task') {
			$query.=" AND t.type='task' ORDER BY t.id DESC";
		}else if($task_type == 'task_asc'){
			$query .= " AND t.type='task' ORDER BY t.task_name";
		}else if($task_type == 'date_asc'){
			$query .= " AND t.type='task' ORDER BY t.t_date";
		}else if($task_type == 'login'){
			$query .= " AND t.type='login'";
		}
		$query_result = mysqli_query($GLOBALS['db_connection'], $query);
		if($query_result == TRUE){
			$num = mysqli_num_rows($query_result);
			$activity_details = array();
			if($num > 0){
				$activity_details = mysqli_fetch_all($query_result,MYSQLI_ASSOC);
			}
		}
		echo json_encode($activity_details);
	}
	
	if ($_GET) {
		if (isset($_GET['type'])) { 
			if($_GET['type'] == 'task'){ //to fetch task activities
				get_activities('task');
			}else if($_GET['type'] == 'task_asc' ){ //to fetch task details sorted by task name
				get_activities('task_asc');
			}else if($_GET['type'] == 'date_asc'){ //to fetch task details sorted by task date
				get_activities('date_asc');
			}else if($_GET['type'] == 'login'){
				get_activities('login');
			}
		}else if(isset($_GET['t_id'])){ //to fetch task details into edit_task page

			//$proj_id = $_GET['id'];
			//$task = $_GET['t_name'];
			$sql_query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id AS t_id,p.id,p.name,t.description FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.id=".$_GET['t_id'];
			//$sql_query .= " AND t.project_id = '$proj_id' AND t.task_name='$task' AND type='task'";

			$q_result = mysqli_query($GLOBALS['db_connection'],$sql_query);
			if($q_result == TRUE){
				$num_rows = mysqli_num_rows($q_result);
				$project_data = array();
				if($num_rows > 0){
					$project_data = mysqli_fetch_all($q_result,MYSQLI_ASSOC);
							//echo $project_data;
				}
			}else{
				echo "Error: ".mysqli_error($GLOBALS['db_connection']);
			}
		}else if(isset($_GET['add'])){
			$sql_q = "SELECT name FROM project";
			$result_q = mysqli_query($GLOBALS['db_connection'],$sql_q);
			$project_names=array();
			if(mysqli_num_rows($result_q) > 0){
				$project_names = mysqli_fetch_all($result_q,MYSQLI_ASSOC);
				//print_r($project_names);
			}
		}
		else if(isset($_GET['id'])){
			$s_query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id AS t_id,p.id,p.name,t.description FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.id=".$_GET['id'];
			$result = mysqli_query($GLOBALS['db_connection'],$s_query);
			if($result == TRUE){
				$num_of_rows = mysqli_num_rows($result);
				$task_data = array();
				if($num_of_rows > 0){
					$task_data = mysqli_fetch_all($result,MYSQLI_ASSOC);
					//print_r($task_data);
					echo json_encode($task_data);
				}
			}else{
				echo "Error: ".mysqli_error($GLOBALS['db_connection']);
			}
		}
	}
?>