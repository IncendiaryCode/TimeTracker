<?php
	include('_con.php');
	$user_id = $_SESSION['user_id'];
	//select login details from db
	$select_query = "SELECT * FROM time_details WHERE ref_id=".$user_id." AND type='login'";
	$q_result = mysqli_query($GLOBALS['db_connection'],$select_query);
	$num = mysqli_num_rows($q_result);
	if($num > 0){		
		$row = mysqli_fetch_all($q_result,MYSQLI_ASSOC);
	}

	function timeUsed($time11,$time22){
		$time1 = strtotime($time11);
		$time2 = strtotime($time22);
		$hours = $time2 - $time1;
		$res = gmdate('H:i:s',$hours);
		return $res;
	}
?>
