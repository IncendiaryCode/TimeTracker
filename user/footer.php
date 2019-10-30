<div class="modal modal-transparent fade" id="change-profile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-header">
            <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times  main-modal-close"></i></button>
        </div>
        <div class="modal-content text-center">
            <img id="new_img" src="<?=BASE_URL?>assets/images/user_profiles/<?=$_SESSION['user_image'];?>" class="rounded-circle img-fluid" data-toggle="modal" data-target="#changeimage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
            <h5 class="text-center mt-4 font-weight-light">
                <?php echo $_SESSION['user_name'];?>
            </h5>
            <ul class="text-center">
                <li><a href="<?=BASE_URL?>user/employee_profile.php">My profile</a></li>
                <li><a href="<?=BASE_URL?>user/change_password.php">Change password</a></li>
                <li><a href="<?=BASE_URL?>user/employee_activities.php">My activities</a></li>
                <li><a href="<?=BASE_URL?>php/logout.php" onclick="logout()">Logout</a></li>
            </ul>
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
                    <button type="submit" class="btn save-task" id="submit-profile">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Timer Stop Confirm -->
<div class="modal fade" id="timestopmodal" tabindex="0" role="dialog" aria-labelledby="timestopmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timestopmodalLabel">Confirm Timer Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3>Would you like to stop or complete the task?</h3>
                <p class="mt-4 mb-1">Note:</p>
                <ul class="text-muted">
                    <li>Stopping task will pause the timer but not complete the task.</li>
                    <li>Completing task ends the task and not editable again.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" id="timestopmodal-complete-task" class="btn btn-danger">Complete Task</button>
                <button type="button" id="timestopmodal-stop-task" class="btn btn-primary">Stop Timer</button>
            </div>
        </div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="//momentjs.com/downloads/moment.js"></script>
<script src="<?=BASE_URL?>assets/javascript/employeeInfo.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/javascript/addTask.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/javascript/employeeActivities.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/javascript/change_password.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/javascript/employee_profile.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/javascript/bootstrap-datetimepicker.min.js?v=<?=VERSION?>"></script>
<script src="<?=BASE_URL?>assets/plugins/daily-chart.js"></script>
<script src="<?=BASE_URL?>assets/plugins/week-chart.js"></script>
<script src="<?=BASE_URL?>assets/plugins/utils.js?v=<?=VERSION?>"></script>
</body>
</html>