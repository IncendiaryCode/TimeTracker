<?php
	include('_con.php');
	session_start();
	$user_id = $_SESSION['user_id'];
	$img = $_POST['change_img'];
	$image_name = $_FILES['change_img']['name'];
	$target_dir = "/var/www/html/time_tracker/assets/images/user_profiles/";
    $target_file = $target_dir.basename($_FILES['change_img']['name']);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $extensions_arr = array("jpg","jpeg","png","gif");
       
      	// Check extension
    if(in_array($imageFileType,$extensions_arr)){
    	$sql_query = "UPDATE users SET profile='".$image_name."' WHERE id={$user_id}";
        $query_res = mysqli_query($GLOBALS['db_connection'],$sql_query);
    	if($query_res == TRUE){
      		// Upload file
    		if(is_uploaded_file($_FILES['change_img']['tmp_name'])){
                if(move_uploaded_file($_FILES['change_img']['tmp_name'],$target_dir.$image_name)){
      			    $sql_q = "SELECT profile FROM users WHERE id='$user_id'";
                    $result = mysqli_query($GLOBALS['db_connection'],$sql_q);
                    $row = mysqli_fetch_assoc($result);
                    $imag = $row['profile'];
                    $img_src = "/var/www/html/time_tracker/assets/images/user_profiles/".$imag;
                    $_SESSION['user_image'] = $imag;
                    echo $_SESSION['user_image'];
                    echo $_SESSION['user_name'];
                    //echo "<img src='".$img_src."'>";
                    header('location:../user/home.php');
              	}else{
              		echo "Upload error.";	
              	}
            }else{
              	echo "Failed to upload.";
            }
    	}else{
    	    echo "Error: ".mysqli_error($GLOBALS['db_connection']);
    	}
    }
?>
