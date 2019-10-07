<?php
	include('con.php');
	//session_start();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>
			Change Password
		</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	    <link rel="stylesheet" type="text/css" href="css/new.css">
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
	</head>
	<body>
		<div id="formPsw" class="animated fadeInRightBig">
	        <form id="reEnterPsw" novalidate method="post" action="change_pwd.php">
	            <div class="text-center"> <img src="images/logo.png"></div>
	            <div class=" logo-space">
	                <h5 class="text-center">Change Password</h5>
	            </div>
	            <div class="form-group">
	                <div class="input-group mb-3 top-space">
	                    <input type="password" class="form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter new password">
	                </div>
	            </div>
	            <div class="form-group otp">
	                <div class="input-group mb-3  top-space">
	                    <input type="password" class="form-control top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm password">
	               	</div>
	                <p class="error" id="cnfrmPsw"></p>
	            </div>
	            <div class="row top-space" style="width: 100%;">
	                <a href="index.php" class="col-6">back to login</a>
	                <button type="submit" class="col-3 offset-3 btn btn-primary" id="count">Submit</button>
	            </div>
	        </form> 
	    </div>
	    <div id="success" class="animated bounce">
	            
	        <p class="text-center">success</p>
	                
	    </div>   
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	    <script src="javascript/script.js"></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	</body>
</html>
