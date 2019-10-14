	<div class="modal fade" id="changeProfile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-header bg-white">
                <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-content modal-width">
                <div class="container-fluid-main">
                    <div class="  md main-container-employee text-center">
                        <img id="new_img" src="<?=BASE_URL?>assets/images/user_profiles/<?=$_SESSION['user_image'];?>" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeimage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
                        <h5 class="text-center mt-4 font-weight-light"><?php echo $_SESSION['user_name'];?></h5>
                        <div class="container">
                            <div class="row">
                                <h3 class="hr pt-4 font-weight-normal"><a href="<?=BASE_URL?>user/employee_profile.php" class="text-dark">Profile</a></h3>
                            </div>
                            <div class="row">
                                <h3 class="hr pt-4 font-weight-normal"><a href="<?=BASE_URL?>user/employee_activities.php" class="text-dark">Login Activities</a></h3>
                            </div>
                            <div class="row">
                                <h3 class="hr pt-4 font-weight-normal"><a href="<?=BASE_URL?>/php/logout.php" class="text-dark" onclick="logout()">Logout</a></h3>
                            </div>
                            <div class="row hr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="changeimage" data-backdrop="false">
	    <div class="modal-dialog animated fadeInDown">
	        <div class="modal-content text-center">
	            <div class="modal-header ">Upload image
	                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
	            </div>
	            <div class="modal-body">
	                <form id="uploadImage" method="post" action="<?=BASE_URL?>php/upload_profile.php" enctype="multipart/form-data">
	                    <p><input type="file" name="change_img" placeholder="Upload image" id="image"></p>
	                    <p class="text-danger" id="imageerror"></p>
	                    <button type="submit" class="btn save-task submitProfile">Upload</button>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?=BASE_URL?>assets/javascript/employeeInfo.js?v=<?=VERSION?>"></script>
    <script src="<?=BASE_URL?>assets/javascript/addTask.js?v=<?=VERSION?>"></script>
    <script src="<?=BASE_URL?>assets/javascript/employeeActivities.js?v=<?=VERSION?>"></script>
    <script src="<?=BASE_URL?>assets/javascript/employeeProfile.js?v=<?=VERSION?>"></script>
    
</body>
</html>