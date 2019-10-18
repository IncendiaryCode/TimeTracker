<?php
	include('_con.php');

	$data = array();
	$data['status'] = FALSE;
	$data['message'] = 'Something went wrong!';

	if ($_POST) {
		session_start();	
		$tabl_id = $_POST['id'];
		$stop_time = date('H:i:s');
		//update end time in time details
		$sql_query = "UPDATE time_details SET end_time='".$stop_time."' WHERE id='$tabl_id'";
		$result = mysqli_query($GLOBALS['db_connection'],$sql_query);
		// echo $sql_query;exit;		
		if($result != FALSE){
			//@TODO dump error to file not UI
			$data['status'] = TRUE;
			$data['message'] = 'Successful!';
		} else {
			echo"Error:".mysqli_error($GLOBALS['db_connection']);			
		}
	}

	// header('location:../user/home.php');
	echo json_encode($data);
?>
