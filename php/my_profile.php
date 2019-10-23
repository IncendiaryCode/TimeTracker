<?php
include("_con.php");
session_start();
$user_name = $_SESSION['user_name'];
if(isset($_GET['type']) == 'profile'){
	//print_r("hii");
	$sql_query = "SELECT * FROM users WHERE name='$user_name'";
	$result = mysqli_query($GLOBALS['db_connection'],$sql_query);
	if($result == TRUE){
		$row = mysqli_fetch_assoc($result);
		$values = json_encode($row);
		echo $values;
	}else{
		//echo"bye";
	}
}

?>