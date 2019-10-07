<?php
	include("con.php");
	//print_r($_POST['otp']);exit();
	$otp=mysqli_real_escape_string($con,$_POST['otp']);
	$sql_check="SELECT * FROM login WHERE reset_token={$otp} AND type='user'";
	$result=mysqli_query($con,$sql_check);
	$row=mysqli_num_rows($result);
	if($row==1){
		//if token is valid
	  
	//echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
		echo("Valid Token.");
		//header('location:index.php');
		header("location:index_c.php");
	}else{
		//die("Error: " . $sql_check . "<br>" . mysqli_error($con));
	    echo("Wrong OTP.");
	    
	  // header('location:forgot_pwd.php');
	}
?>