<?php if(!isset($_SESSION['user'])){ ?>
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
</body>
</html>