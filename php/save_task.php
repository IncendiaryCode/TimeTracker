<?php
	include('con.php');
	session_start();
	$tabl_id=$_SESSION['table_id'];
	//$user=$_SESSION['user'];
	$user_id=$_SESSION['user_id'];
	//$user_id=$_POST['user_id'];
	$task_name=mysqli_real_escape_string($con,$_POST['task-name']);
	//$task_desc=mysqli_real_escape_string($con,$_POST['td']);
	$choose_p=mysqli_real_escape_string($con,$_POST['chooseProject']);
	$task_start=mysqli_real_escape_string($con,$_POST['startedDate']);
	$td=strtotime($task_start);
	//$task_start_time=date('H:i:s',$td);
	$task_date=date('Y-m-d',$td);
	//print_r($task_date);exit();
	$task_end=mysqli_real_escape_string($con,$_POST['endedDate']);
	//print_r($_POST);exit();
	$sql="INSERT INTO time_details(type,ref_id,project_id,task_name,t_date,start_time,end_time,created_on) VALUES('task','".$user_id."','".$choose_p."','".$task_name."','".$task_date."','".$task_start."','".$task_end."','".date('Y-m-d H:i:s')."')";
	//print_r($sql);exit();
	$res=mysqli_query($con,$sql);
	if(($res)==TRUE){

		$s="SELECT * FROM time_details WHERE ref_id='".$user_id."' AND type='task' ";
		$r=mysqli_query($con,$s);
		$num=mysqli_num_rows($r);
		$row = mysqli_fetch_all($r,MYSQLI_ASSOC);
	}
		/*echo $num;
		if($num>0){
		
			$row = mysqli_fetch_all($r,MYSQLI_ASSOC);
			
}

	}else{
		echo "<br>";
		echo "Error: ".$sql."<br>".mysqli_error($con);
	}*/
?>