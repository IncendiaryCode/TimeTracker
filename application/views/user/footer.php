<?php
    $this->load->library('session');
    $profile = $this->session->userdata('user_profile');
    $name = $this->session->userdata('username');
?>
    <div class="modal modal-transparent fade" id="change-profile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-header">
                <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times  main-modal-close"></i></button>
            </div>
            <div class="modal-content text-center">
                <img id="new_img" src="<?=base_url().USER_UPLOAD_PATH.$profile;?>" class="rounded-circle img-fluid" >

                <div class="edit">
                    <div class="img-icon">
                        <a href="#" class="text-white"><i class="change-image fas fa-camera" data-toggle="modal" data-target="#changeimage"></i></a>
                    </div>
                </div> 

                <h5 class="text-center mt-4 font-weight-light"><?php echo $name;?></h5>
                <ul class="text-center">
                    <!-- profile options -->
                    <li id="empplyee-profile"><a href="<?=base_url();?>index.php/user/load_my_profile">My profile</a></li>
                    <li><a href="<?=base_url();?>index.php/user/load_employee_activities">My activities</a></li>
                    <li><a href="<?=base_url();?>index.php/login/logout" data-toggle="modal" data-target="#logout-modal">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
<div class="modal" id="changeimage" data-backdrop="false">
    <!-- to change the profile picture of user-->
    <div class="modal-dialog animated">
        <div class="modal-content text-center">
            <div class="modal-header ">Upload image
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="upload-image" method="post" action="<?=base_url();?>index.php/user/upload_profile" enctype="multipart/form-data">
                    <p><input type="file" name="change_img" placeholder="Upload image" id="image"></p>
                    <p class="text-danger" id="imageerror"></p>
                    <button type="submit" class="btn btn-primary" id="submit-profile-pic">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="logout-modal" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header ">Leave for the day!!!
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
                    <div class="modal-body text-left">
                        <p class="mt-4 mb-1"><u>Note:</u></p>
                        <ul class="text-muted"><li>Do you want to leave for the day?</li>
                            <li>You can not login for the day once you logout.</li>
                        </ul>
                    </div>
                    <div class="modal-footer text-center">
                        <!-- <button type="submit" class="btn btn-secondary" onclick="window.location.href='<?=base_url();?>index.php/login/logout'"data-dismiss="modal">No</button> -->
                        <button type="button" class="btn btn-primary" onclick="window.location.href='<?=base_url();?>login/logout'">Yes</button>
                    </div>
            </div>
        </div>
    </div>

<div class="modal" id="pause-action" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header ">Leave for the day!!!
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
                <form method="post" action="<?=base_url();?>login/logout">
                    <div class="modal-body text-left">
                        <p class="mt-4 mb-1"><u>Note:</u></p>
                        <ul class="text-muted"><li>Do you want to leave for the day?</li>
                            <li>You can not login for the day once you logout.</li>
                        </ul>
                    </div>
                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="end-time-update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?=base_url();?>index.php/user/stop_timer?id=<?php echo $task_info['task_status'][0]['task_id'] ?>" id="update-endtime" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Stop now!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <p>Task name: <strong>
                                <?php echo $task_info['task_status'][0]['task_name'] ?></strong></p>
                    </div>
                    <div class="input-group">
                        <p>Started at: <strong id="old-start-date">
                        <?php echo $task_info['task_status'][0]['start_time'] ?></strong></p>
                    </div>
                    <div>
                        <label for="old-datepicker">Enter end time: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control  edit-date-time" id="old-datepicker" name="end_time">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                    <div class="pt-3">
                        <label for="task-description">Enter description: </label>
                        <input type="text" class="form-control " id="task-description" name="task-description">
                    </div>
                    <p class="text-center text-danger" id="old-date-error"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary stop-now-modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary stop-now-modal" id="save-changes">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="timestopmodal" tabindex="-1">
    <!-- Modal Timer to Stop or Confirm -->
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timestopmodalLabel">Confirm Timer Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
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
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script  src="//www.gstatic.com/charts/loader.js" type="text/javascript"></script>
<script src="//momentjs.com/downloads/moment.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="//unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>

<script src="//unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="<?=base_url();?>assets/user/plugins/calendar_view.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/add_task.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/employeeInfo.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/utils.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/change_password.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/plugins/bxslider/js/jquery.bxslider.min.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/employee_profile.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/bootstrap-datetimepicker.min.js?v=<?=VERSION?>"></script>
<script type="text/javascript">
 $(function() {
 $('.edit-date-time').datetimepicker({
          useCurrent: false, format: 'YYYY-MM-DD hh:mm A',
     });
 });
 $(function() {
 $('.edit-date').datepicker({
          useCurrent: false, format: 'yyyy-mm-dd',
     });
 });
$(function() {
$('.edit-time').timepicker({
      useCurrent: false, format: 'hh:mm:ss',
 });
});


</script>
</body>
</html>