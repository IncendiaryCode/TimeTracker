<?php 
<<<<<<< HEAD
	 if(!isset($_SESSION['user'])){
?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	    <script src="<?=BASE_URL?>assets/javascript/script.js"></script>
	
	<?php } else { ?>

	    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	    <script src="<?=BASE_URL?>assets/javascript/employeeInfo.js"></script>
	    <script src="<?=BASE_URL?>assets/javascript/addTask.js"></script>
	    <script src="<?=BASE_URL?>assets/javascript/employeeActivities.js"></script>
	    <script src="<?=BASE_URL?>assets/javascript/script.js"></script>
	<?php } ?>
=======
	/*include("configurations/constants.php");
	session_start();*/
?>
<?php if(!isset($_SESSION['user'])){ ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?=BASE_URL?>assets/javascript/employeeInfo.js"></script>
    <script src="<?=BASE_URL?>assets/javascript/addTask.js"></script>
    <script src="<?=BASE_URL?>assets/javascript/employeeActivities.js"></script>
<?php } else { ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?=BASE_URL?>assets/javascript/script.js"></script>	
    
<?php } ?>
>>>>>>> 8f08aeaa5781360dc38d4f4cb6e97c2997478051
</body>
</html>