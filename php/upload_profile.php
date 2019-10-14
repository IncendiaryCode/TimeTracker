<?php
	include('_con.php');
	session_start();
	$user_id=$_SESSION['user_id'];
	//print_r($user_id);exit();
	$img=$_POST['change_img'];
	//$target=addslashes (file_get_contents($_FILES['change_image']['name']));
	$image_name=$_FILES['change_img']['name'];
	//$target = "users/".basename($image_name);
	$target_dir = "/var/www/html/TimeTracker/assets/images/user_profiles/";
  	$target_file = $target_dir.basename($_FILES['change_img']['name']);
  	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  	$extensions_arr = array("jpg","jpeg","png","gif");
   /* $file_ext=strtolower(end(explode('.',$_FILES['change_img']['name'])));
  $expensions= array("jpeg","jpg","png", "mp3",
                     "acc", "wav", "3gpp", "mp4", "3gp", "m4a", "amr", "avi",
                    "flv", "gif");*/
//echo $_FILES['change_img']['name'];
  	// Check extension
  	if(in_array($imageFileType,$extensions_arr)){
  		// Convert to base64 
   // $image_base64 = base64_encode(file_get_contents($_FILES['change_image']['tmp_name']) );

   // $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;
//print_r($image);
	$sql = "UPDATE users SET profile='".$image_name."' WHERE id={$user_id}";
  	//print_r($sql);exit();
  	$res=mysqli_query($GLOBALS['db_connection'],$sql);
	if($res==TRUE){
  		// Upload file
		if(is_uploaded_file($_FILES['change_img']['tmp_name'])){
			//print_r(($_FILES['change_image']['tmp_name']));
     // print_r($target_file);
     		if(move_uploaded_file($_FILES['change_img']['tmp_name'],$target_file)){
  
  
  	/*if (move_uploaded_file($_FILES['change_image']['tmp_name'], $target)) {
  		echo "Image uploaded successfully";
  	}else{
  		echo "Failed to upload image: ".mysqli_error($GLOBALS['db_connection']);
  	}*/
  			//	echo "Upload successful.".$image_name."<br>";
  				$sqll = "SELECT profile FROM users WHERE id='".$user_id."'";
                $result = mysqli_query($GLOBALS['db_connection'],$sqll);

                $row = mysqli_fetch_assoc($result);
                //print $row['profile'];exit();
                $imag= $row['profile'];
               // header("Content-type: image/png");
                $img_src="<?=BASE_URL?>assets/images/user_profiles/".$imag;
                $_SESSION['user_image']=$imag;
              echo $_SESSION['user_image'];
               // $res_image=json_encode($img_src);
            //print $img_src;
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
<!--<img src='<?php //echo $img_src; ?>'/>-->

