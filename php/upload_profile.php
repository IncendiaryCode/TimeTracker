<?php
	include('con.php');
	session_start();
	$user_id=$_SESSION['user_id'];
	//print_r($user_id);exit();
	$img=$_POST['change_img'];
	//$target=addslashes (file_get_contents($_FILES['change_image']['name']));
	$image_name=$_FILES['change_img']['name'];
	//$target = "users/".basename($image_name);
 
	$target_dir = "images/";
  	$target_file = $target_dir . basename($_FILES['change_img']['name']);
  	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  	$extensions_arr = array("jpg","jpeg","png","gif");

  	// Check extension
  	if( in_array($imageFileType,$extensions_arr) ){
  		// Convert to base64 
   // $image_base64 = base64_encode(file_get_contents($_FILES['change_image']['tmp_name']) );

   // $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;
//print_r($image);
	$sql = "UPDATE users SET profile='".$image_name."' WHERE id='".$user_id."'";
  	//print_r($sql);exit();
  	$res=mysqli_query($con,$sql);
	if($res==TRUE){
  		// Upload file
		if(is_uploaded_file($_FILES['change_img']['tmp_name'])){
			//print_r(($_FILES['change_image']['tmp_name']));
  	
     		if(move_uploaded_file($_FILES['change_img']['tmp_name'],$target_dir.$image_name)){
  	//print_r($_FILES);exit();
  				
  	/*if (move_uploaded_file($_FILES['change_image']['tmp_name'], $target)) {
  		echo "Image uploaded successfully";
  	}else{
  		echo "Failed to upload image: ".mysqli_error($con);
  	}*/
  			//	echo "Upload successful.".$image_name."<br>";
  				$sqll = "SELECT profile FROM users WHERE id='".$user_id."'";
                $result = mysqli_query($con,$sqll);

                $row = mysqli_fetch_assoc($result);
                //print $row['profile'];exit();
                $imag= $row['profile'];
               // header("Content-type: image/png");
                $img_src="images/".$imag;
                
               // $res_image=json_encode($img_src);
            //print $img_src;
//echo "<img src='".$img_src."'>";
  			header('location:employeeInfo.php?img='.$img_src);
 				

  			}else{
  				echo "Upload error.";	
  			}
  		}else{
  			echo "Failed to upload.";
		}
	}else{
	echo "Error: ".mysqli_error($con);
	}
}
?>
<!--<img src='<?php //echo $img_src; ?>'/>-->

