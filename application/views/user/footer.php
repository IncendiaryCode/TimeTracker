<?php
// $this->load->library('session');
$profile = $this->session->userdata('user_profile');
$name = $this->session->userdata('username');
?>
<div class="modal modal-transparent fade m-0" id="change-profile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-header">
            <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times  main-modal-close"></i></button>
        </div>
        <div class="modal-content text-center">
            <img id="new_img" src="<?= base_url() . USER_UPLOAD_PATH . $profile; ?>" class="rounded-circle img-fluid">

            <h5 class="text-center mt-4 font-weight-light"><?php echo $name; ?></h5>
            <ul class="text-center">
                <!-- profile options -->
                <li id="empplyee-profile"><a href="<?= base_url(); ?>index.php/user/load_my_profile">My profile</a></li>
                <li><a href="<?= base_url(); ?>user/login_activity">Login activities</a></li>
                <li><a href="<?= base_url(); ?>login/logout">Logout</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="modal fade" id="changeimage" tabindex="-1" role="dialog" aria-labelledby="changeimageLabel" aria-hidden="true">
    <!-- to change the profile picture of user-->
    <div class="modal-dialog animated">
        <div class="modal-content text-center">
            <div class="modal-header ">Upload image
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="upload-image" method="post" action="<?= base_url(); ?>index.php/user/upload_profile" enctype="multipart/form-data">
                    <input type="file" class="form-control" name="change_img" placeholder="Upload image" id="image">
                    <p class='text-left mt-4'> Formates allowed: gif, jpg, png, jpeg.</p>
                    <p class="text-danger" id="imageerror"></p>
                    <div class="row">
                        <div class="col-12 text-right">
                        <button type="submit" class="btn btn-primary" id="submit-profile-pic">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pause-action" tabindex="-1" role="dialog" aria-labelledby="pause-actionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h4>Punch Out</h4>
            </div>
            <div class="modal-body text-left">
                <ul class="text-muted">
                    <li>Hope all your task are done for today...!</li>
                    <li>Hope all your task are done for today...!</li>
                    <li>You can't punch for the day after punch out.</li>
                </ul>
                <div class="input-group date">
                    <input type="text" class="timerpicker-punchout check-for-utc form-control" id="timerpicker-punchout" name="punch_out_time" placeholder="hh:mm">
                </div>
            </div>
                <p id="punch-out-invalid" class="text-danger"></p>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">No</button>
                <button type="submit" class="btn btn-primary col-6" id="punch-out">Yes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="end-time-update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url(); ?>index.php/user/stop_timer?id=<?php echo $task_info['task_status'][0]['task_id'] ?>" id="update-endtime" method="post">
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
                        <input type="text" class=" form-control " id="old-datepicker" name="end_time">
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
                    <button type="button" class="btn btn-primary  col-6 stop-now-modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary col-6 stop-now-modal" id="save-changes">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="upload-demo" class="center-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">Cancel</button>
                <button type="button" id="cropImageBtn" class="btn btn-primary col-6">Done</button>
            </div>
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="//unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script src="<?= base_url(); ?>assets/plugins/bxslider/js/jquery.bxslider.min.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/momet_copy.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/infinite_scroll.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/croppie.min.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/utils.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/moment_zone.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/employeeInfo.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/add_task.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/plugins/calendar_view.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/employee_profile.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/change_password.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/app.js?v=<?= VERSION ?>"></script>
<script>
$('.timerpicker-punchout').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		uiLibrary: 'bootstrap4'
	});
</script>
</body>
</html>