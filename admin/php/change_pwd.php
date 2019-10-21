<?php
	include("_con.php");
	session_start();
	$old_pwd = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw1']);
	$new_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw11']);
	$confirm_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw22']);
	$pass = md5($new_p);
	if(empty($new_p) && empty($confirm_p)){//Empty Password
		echo "Please enter your new password.";
	}
	else if($new_p === $confirm_p){ //if passwords match
		if(isset($_SESSION['user'])){		
			$user_id = $_SESSION['admin_id'];
			$old_pass=md5($old_pwd);
			$sql_q = "SELECT password FROM login WHERE ref_id={$user_id} AND password='$old_pass'";
			$sql_q.= " AND type='admin'";
			$result_q = mysqli_query($GLOBALS['db_connection'],$sql_q);
			if($result_q == TRUE){
				if(mysqli_num_rows($result_q)==1){
					$sql3 = "UPDATE login SET password='".$pass."' WHERE ref_id={$user_id}";
					$res = mysqli_query($GLOBALS['db_connection'],$sql3);
					if($res){
						header("Location:../ui/index.php");
					}else{
						echo "Error: ".$sql3."<br>".mysqli_error($GLOBALS['db_connection']);
					}
				}
			}else{
				echo "Error: ".$sql_q."<br>".mysqli_error($GLOBALS['db_connection']);
			}
		}
	}
?>