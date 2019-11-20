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
            <img id="new_img" src="<?=base_url();?>assets/user/images/user_profiles/<?=$profile;?>" class="rounded-circle img-fluid" data-toggle="modal" data-target="#changeimage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
            <h5 class="text-center mt-4 font-weight-light">
                <?php echo $name;?>
            </h5>
            <ul class="text-center">    <!-- profile options -->
                <li><a href="<?=base_url();?>index.php/user/load_my_profile">My profile</a></li>
                <li><a href="<?=base_url();?>index.php/user/change_password">Change password</a></li>
                <li><a href="<?=base_url();?>index.php/user/load_employee_activities">My activities</a></li>
                <li><a href="<?=base_url();?>index.php/login/logout" onclick="logout()">Logout</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="modal" id="changeimage" data-backdrop="false" >  <!-- to change the profile picture of user-->
    <div class="modal-dialog animated zoomIn" >
        <div class="modal-content text-center">
            <div class="modal-header ">Upload image
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="uploadImage" method="post" action="<?=base_url();?>index.php/user/employee_profile" enctype="multipart/form-data">
                    <p><input type="file" name="change_img" placeholder="Upload image" id="image"></p>
                    <p class="text-danger" id="imageerror"></p>
                    <button type="submit" class="btn save-task" id="submit-profile">Upload</button>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="timestopmodal" tabindex="0" role="dialog" aria-labelledby="timestopmodalLabel" aria-hidden="true">  <!-- Modal Timer to Stop or Confirm -->
    <div class="modal-dialog modal-lg" role="document">
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
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
<script src="//momentjs.com/downloads/moment.js"></script>
<script src="//unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="<?=base_url();?>assets/user/plugins/chart.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/addTask.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/employeeInfo.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/user/javascript/change_password.js?v=<?=VERSION?>"></script>
<script src="<?=base_url();?>assets/plugins/bxslider/js/jquery.bxslider.min.js?v=<?=VERSION?>"></script>
<!-- <script src="<?=base_url();?>assets/user/javascript/employeeActivities.js?>"></script> -->
<script src="<?=base_url();?>assets/user/javascript/employee_profile.js?v=<?=VERSION?>"></script>
<script type="text/javascript">
    function logout()
    {
        localStorage.clear();
        localStorage.setItem("firstTime", null);
    }
</script>
</body>
</html>
