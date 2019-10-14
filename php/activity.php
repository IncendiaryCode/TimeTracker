<?php
	include('_con.php');
	function get_activities($task_type){
		//login check
		if (!$_SESSION['user']) {
			return FALSE;
		}
		//Choose task info from db
		$q = "SELECT * FROM time_details WHERE ref_id=".$_SESSION['user_id'];
		if ($task_type == 'task') {
			$q .= " AND type='task'";
		}
		$r = mysqli_query($GLOBALS['db_connection'], $q);
		$num = mysqli_num_rows($r);
		$activity_details = array();
		if($num > 0){	
		$activity_details = mysqli_fetch_all($r,MYSQLI_ASSOC);
	}
	echo json_encode($activity_details);
}
		//To get total time used
	function timeUsed($t11,$t22){
		$t1 = strtotime($t11);
		$t2 = strtotime($t22);
		$hours =$t2 - $t1;
		$res=gmdate('H:i:s',$hours);
		return $res;
	}
	if ($_GET) {	
		if (isset($_GET['type'])) {
		session_start();
		get_activities('task');
		}
	}
?>

