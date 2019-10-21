<?php
	include("_con.php");
	if(isset($_POST)){
	$user_name = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task_name']);
	$pass = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['task_pass']);
	$user_email = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['user_email']);
	$user_phone = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['contact']);
	$pwd = md5($pass);
	$user_query = "INSERT INTO users(name,email,phone,type,created_on) VALUES('".$user_name."','".$user_email."','".$user_phone."','user','".date('Y:m:d H:i:s')."')";
	$result = mysqli_query($GLOBALS['db_connection'],$user_query);
	if($result == TRUE){
		$last_id = mysqli_insert_id($GLOBALS['db_connection']);
		$_SESSION['table_id'] = $last_id;
		$tab_id = $_SESSION['table_id'];
		$login_query = "INSERT INTO login(email,password,type,ref_id,created_on) VALUES('".$user_email."','".$pwd."','user','".$tab_id."','".date('Y:m:d H:i:s')."')";
		$res = mysqli_query($GLOBALS['db_connection'],$login_query);
		if($res == FALSE){
			echo "Unable to update:<br>".$login_query."  :  ".mysqli_error($GLOBALS['db_connection']);
		}else{
			
			header('location:../ui/index.php');
		}
	}else{
		echo "Unable to update:<br>".$user_query."  :  ".mysqli_error($GLOBALS['db_connection']);
	}
}
	if(isset($_GET)){
			$get_users_q = "SELECT * FROM users";
			$query_result = mysqli_query($GLOBALS['db_connection'],$get_users_q);
			if($query_result == FALSE){
				echo "Unable to update:<br>".$login_query."  :  ".mysqli_error($GLOBALS['db_connection']);
			}else{
				$row = mysqli_num_rows($query_result);
					echo json_encode($row);
				
			}
					
			}	    	
?>