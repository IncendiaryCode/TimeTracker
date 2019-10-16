<?php
	include('_con.php');
	function get_activities($task_type){
		//login check
		if (!isset($_SESSION['user'])) {
			return FALSE;
		}
		//Choose task info from db
		$query = "SELECT t.task_name,t.t_date,t.start_time,t.end_time,t.id,p.id,p.name FROM time_details AS t JOIN project AS p ON t.project_id=p.id WHERE t.ref_id=".$_SESSION['user_id'];
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
		if($query_result==TRUE){
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
			session_start();
			if($_GET['type'] == 'task'){ //to fetch task activities
				get_activities('task');
			}else if($_GET['type'] == 'task_asc' ){ //to fetch task details sorted by task name
				get_activities('task_asc');
			}else if($_GET['type'] == 'date_asc'){ //to fetch task details sorted by task date
				get_activities('date_asc');
			}
			else if($_GET['type'] == 'login'){
				get_activities('login');
			}
		}
	}
?>
