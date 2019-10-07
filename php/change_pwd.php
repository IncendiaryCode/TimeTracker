<?php
	include("con.php");
	session_start();
	$user_id=$_POST['user_id'];
	$old=mysqli_real_escape_string($con,$_POST['psw1']);
	$new_p=mysqli_real_escape_string($con,$_POST['psw11']);
	//print_r($_POST);exit();
	$confirm_p=mysqli_real_escape_string($con,$_POST['psw22']);
	if(empty($new_p) && empty($confirm_p)){//Empty Password
		echo "Please enter your new password.";
	}
	else if($new_p === $confirm_p){ //if passwords match
		$pass=md5($new_p);
		$oldd=md5($old);
		$sql="SELECT password FROM login WHERE password='".$oldd."'";
		//print_r($sql);exit();
		$res=mysqli_query($con,$sql);
		$rows=mysqli_num_rows($res);
		if($rows==0){
			echo "Enter your current Password.";
			
		}else{
			$sql2="UPDATE login SET password='".$pass."' WHERE id='".$user_id."'";
			$ress=mysqli_query($con,$sql2);
		//print_r($res);exit();
			if($ress){

				echo "Password changed.";
				header("Refresh:1;URL=index.php");
			}else//if UPDATE fails
			{
				echo "Error: ".$sql2."<br>".mysqli_error($con);
			}
		}
	}
?>