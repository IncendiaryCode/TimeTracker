<?php

//print_r("forgot"); exit();
	include("con.php");
	//session_start();
	$q = $_POST['email'];
	//print_r($q);exit();
	//$q=$_REQUEST['email']
	if(isset($q)){
		$email=mysqli_real_escape_string($con,$q);
		$sql_q="SELECT * FROM login WHERE email='$email' AND type='user'";
        $res_q=mysqli_query($con,$sql_q);
        $count=mysqli_num_rows($res_q);

        if($count==1){
            //$_SESSION['mail']=$email;

            //Send mail
            $row = mysqli_fetch_assoc($res_q);
			//$password = $row['password'];
			$to = $row['email'];
			//$subject->>>unique id generated via uniqid() function.
			//$subject = uniqid();
			$subject = "OTP for create new password.";
			$token=substr(mt_rand(), 0, 6);
			
			$message = "Please use this OTP to create bew password ".$token;
			$headers = "From : admin@printgreener.com";
			//Inserting token into reset_token column
			$sql="UPDATE login SET reset_token = '".$token."' WHERE email= '".$email."'";

			//print_r($sql); exit();
			if(mysqli_query($con,$sql)){
				/*$email_result = mail('swasthika@printgreener.com', $subject, $message);
				var_dump($email_result); exit();*/
				//Send mail after storing token
				if(!mail($to, $subject, $message, $headers)){
					//echo "Your Password has been sent to your email id";
					//header("location:check_otp.php");
					//print_r(error_get_last());exit();
					//var_dump(error_get_last()['rotate-text']);exit();

				}else{//if mail not sent
				
					echo "Mail sent.";
					//echo "Failed to Recover your password, try again";
				}
			}else{//if unable to insert into DB
				echo "Error: " . $sql . "<br>" . mysqli_error($con);
			}
        }else{//if SELECT query fails
        	//echo "Email does not exist in database.";
        	echo "Not a valid account present with this email id";
        }
	}else{//If $_POST['Email'] fails
		echo "Enter email field.";
	}

?>