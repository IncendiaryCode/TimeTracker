<?php
	include("_con.php");
	session_start();
	
	//$email = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST["user-email"]);
	$user_mail = $_POST['mail'];
	$new_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw11']);
	$confirm_p = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw22']);
	$pass = md5($new_p);
	if(empty($new_p) && empty($confirm_p)){//Empty Password
		echo "Please enter your new password.";
	}
	else if($new_p === $confirm_p){ //if passwords match
		if(!isset($_SESSION['user'])){	//for forgot/reset password
			//UPDATE the new password in db
			$sql_query = "UPDATE login SET password = '".$pass."' WHERE email='$user_mail'";
			$result = mysqli_query($GLOBALS['db_connection'],$sql_query);
			if($result){
				header("Refresh:1;URL=../index.php");
			}else//if UPDATE fails
			{
				echo "Error: ".$sql_query."<br>".mysqli_error($GLOBALS['db_connection']);
			}
		}else{ //For change password
			$email = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['Username']);
			$old_pwd = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['psw1']);
			$user_id = $_SESSION['user_id'];
			$old_pass = md5($old_pwd);
			$sql_q = "SELECT password FROM login WHERE ref_id={$user_id} AND password='".$old_pass."'";
			$result_q = mysqli_query($GLOBALS['db_connection'],$sql_q);
			if($result_q == TRUE){
				if(mysqli_num_rows($result_q) == 1){
					$sql_qry = "UPDATE login SET password='".$pass."' WHERE ref_id={$user_id}";
					$q_res = mysqli_query($GLOBALS['db_connection'],$sql_qry);
					if($q_res){
						header("Location:../index.php");
					}else{
						echo "Error: ".$sql_qu."<br>".mysqli_error($GLOBALS['db_connection']);
					}
				}
			}else{
				echo "Error: ".$sql_q."<br>".mysqli_error($GLOBALS['db_connection']);
			}
		}
	}
?>