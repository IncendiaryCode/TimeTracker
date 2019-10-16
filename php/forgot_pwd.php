<?php
	include("_con.php");
	session_start();
	$get_email = $_POST['email'];
	if(isset($get_email)){
		$email = mysqli_real_escape_string($GLOBALS['db_connection'],$get_email);
		$sql_q = "SELECT * FROM login WHERE email='$email' AND type='user'";
        $res_q = mysqli_query($GLOBALS['db_connection'],$sql_q);
        $count = mysqli_num_rows($res_q);

        if($count == 1){
            $_SESSION['mail']=$email;
            //Send mail
            $row = mysqli_fetch_assoc($res_q);
			//$password = $row['password'];
			$to = $row['email'];
			$subject = "OTP for create new password.";
			$token = substr(mt_rand(), 0, 6);
			$message = "Please use this OTP to create bew password ".$token;
			$headers = "From : admin@printgreener.com";
			//Inserting token into reset_token column
			$sql_query = "UPDATE login SET reset_token = '".$token."' WHERE email= '".$email."'";
			if(mysqli_query($GLOBALS['db_connection'],$sql_query)){
				//$email_result = mail('swasthika@printgreener.com', $subject, $message);
				//Send mail after storing token
				if(!mail($to, $subject, $message, $headers)){
					//echo "Your Password has been sent to your email id";
					//header("location:check_otp.php");
					//print_r(error_get_last());exit();
					//var_dump(error_get_last()['rotate-text']);exit();

				}else{//if mail not sent
				
					echo "Mail sent.";
				}
			}else{//if unable to insert into DB
				echo "Error: " . $sql_query . "<br>" . mysqli_error($GLOBALS['db_connection']);
			}
        }else{//if SELECT query fails
        	echo "Not a valid account present with this email id";
        }
	}else{//If $_POST['Email'] fails
		echo "Enter email field.";
	}
?>
