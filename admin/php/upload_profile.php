<?php
	include('_con.php');
	session_start();
	$user_id = $_SESSION['admin_id'];
	$img = $_POST['change_image'];
	$image_name = $_FILES['change_image']['name'];
	$target_dir = "/var/www/html/time_tracker/admin/images/";
    $target_file = $target_dir.basename($_FILES['change_image']['name']);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $extensions_arr = array("jpg","jpeg","png","gif");
       /* $file_ext=strtolower(end(explode('.',$_FILES['change_img']['name'])));
      $expensions= array("jpeg","jpg","png", "mp3",
                         "acc", "wav", "3gpp", "mp4", "3gp", "m4a", "amr", "avi",
                        "flv", "gif");*/
      	// Check extension
    if(in_array($imageFileType,$extensions_arr)){
    	$sql_query = "UPDATE users SET profile='".$image_name."' WHERE id='$user_id' AND type='admin'";
        $query_res = mysqli_query($GLOBALS['db_connection'],$sql_query);
    	if($query_res == TRUE){
      		// Upload file
    		if(is_uploaded_file($_FILES['change_image']['tmp_name'])){
                if(move_uploaded_file($_FILES['change_image']['tmp_name'],$target_dir.$image_name)){
      			    $sql_q = "SELECT profile FROM users WHERE id='".$user_id."'";
                    $result = mysqli_query($GLOBALS['db_connection'],$sql_q);
                    $row = mysqli_fetch_assoc($result);
                    $imag = $row['profile'];
                    $img_src = "../admin/images/".$imag;
                    $_SESSION['user_image'] = $imag;
                    //echo "<img src='".$img_src."'>";
                    header('location:../ui/adminProfile.php');
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
?> <!--<img src='<?php //echo $img_src; ?>'/>-->