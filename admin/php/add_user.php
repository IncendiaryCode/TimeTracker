<?php
	include("_con.php");
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
		print_r($tab_id);
		$login_query = "INSERT INTO login(email,password,type,ref_id,created_on) VALUES('".$user_email."','".$pwd."','user','".$tab_id."','".date('Y:m:d H:i:s')."')";
		$res = mysqli_query($GLOBALS['db_connection'],$login_query);
		if($res == TRUE){
			$get_users_q = "SELECT * FROM users";
			$query_result = mysqli_query($GLOBALS['db_connection'],$get_users_q);
			if($query_result == TRUE){
				if(mysqli_num_rows($query_result) == 1){
					$row = mysqli_fetch_assoc($query_result);
				    header('location:../ui/index.php');
				}
			}
		}else{
			echo "Unable to update:<br>".$login_query."  :  ".mysqli_error($GLOBALS['db_connection']);
		}
	}else
	{
		echo "Unable to update:<br>".$user_query."  :  ".mysqli_error($GLOBALS['db_connection']);
	}
	
?>