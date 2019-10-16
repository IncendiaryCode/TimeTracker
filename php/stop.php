<?php
	include('_con.php');
	session_start();
	$tabl_id = $_SESSION['login_row_id'];
	$user_id = $_POST['user_id'];
	$json_data = $_POST['info'];

	$timer_stop_info = json_decode($json_data,true);
	//update end time in time details
	$sql_query = "UPDATE time_details SET end_time='".$timer_stop_info['ended']."' WHERE id=".$tabl_id;
	$result = mysqli_query($GLOBALS['db_connection'],$sql_query);
	if($result == FALSE){
		//@TODO dump error to file not UI
		echo"Error:".mysqli_error($GLOBALS['db_connection']);
	}
?>
