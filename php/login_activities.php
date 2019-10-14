<?php
	include('_con.php');
	$user_id=$_SESSION['user_id'];

	$s="SELECT * FROM time_details WHERE ref_id=2 AND type='login'";
	$r=mysqli_query($GLOBALS['db_connection'],$s);
	$num=mysqli_num_rows($r);
	if($num>0){		
		$row = mysqli_fetch_all($r,MYSQLI_ASSOC);
	}

	function timeUsed($t11,$t22){
		$t1 = strtotime($t11);
		$t2 = strtotime($t22);
		$hours =$t2 - $t1;
		$res=gmdate('H:i:s',$hours);
		return $res;
	}
?>

