<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Login timer
$this->load->helper('date');
if (isset($task_info['login_status'])) {
    $login_time = $task_info['login_status']['start_time'];
    $login_time_display = date('g:i:s A',strtotime($task_info['login_status']['start_time']));
    $login = new DateTime($login_time,new DateTimeZone('UTC'));
    $logintime = $login->getTimestamp();
}
else //Disable punchin card for the day
{
    //Add default values;
    $login_time_display = '00:00:00';
    $logintime = time();
}
$timer = '';
$timerClass = 'fa-play';
$task_type = 'login';
$task_id = 0;
$start_text = 'Start punch in/out';
$task_id = '';
$task_name = 'Punch In/Out';
if(isset($task_info['login_status'])){
if($task_info['login_status']['end_time'] == NULL){
    $timerClass = 'fa-stop';
}else{
    $timerClass = 'fa-play';
}
}else{
    $timerClass = 'fa-play';
}

if(isset($task_info['login_status']['end_time']) && ($task_info['login_status']['end_time']) != NULL){
$flag =1;
}
else{
    $flag = 0;
}
?>
<script type="text/javascript">
//this will be send to JS for timer to start
var __timeTrackerLoginTime = "<?=$logintime?>"; /*start date and time of the task.*/
var stopped = "<?=$flag?>"; /*to check for punch out action*/
</script>
<!-- new scoll for task -->
<div class="container timer-slider">
    <div class="row">
        <div class="col-md-12">
            <div id="timer-slider">     <!-- slider for login activity -->
                <div>
                    <div class="section-slider" id="login-timer-details">
                        <p class="font-weight-light time-font text-center login-time" id="login-time">
                            Punch in at <?php echo $login_time_display; ?>
                        </p>
                        <div class="font-weight-light text-center primary-timer" id="primary-timer">
                            00:00:00
                        </div>
                        <p class="font-weight-light text-center taskName" id="taskName">
                            <?php echo "Punch In/Out"; ?>
                        </p>
                    </div>
                </div>
                <?php 
            if(!empty($task_info['task_status'])){
                $id=1;
                //task timer
                foreach($task_info['task_status'] as $taskinfo){
                    $timer = $taskinfo['start_time'];
                    $timer_display = date('g:i:s A', strtotime($taskinfo['start_time']));
                    $datetime2 = new DateTime($timer,new DateTimeZone('UTC'));
                    $timer_start = $datetime2->getTimestamp();
                    $task_start = strtotime($timer);
                    $task_id = $taskinfo['task_id'];

                    $offset_min = ((date('Z'))/60);
                    $local_time_min = ((substr($taskinfo['start_time'],11,2)*60)+(substr($taskinfo['start_time'],14,2)))+$offset_min;
                    $local_time = round(($local_time_min/60),0) . ':' . ($local_time_min%60);
                     ?>
                <div id="slider<?=$task_id?>">  <!-- slider for all task -->
                    <div class="section-slider task-slider" id="login-timer-details<?=$id?>">
                        <input type="hidden" id="<?php echo $taskinfo['task_id'] ?>" value="<?php echo $timer_start?>">
                        <input type="hidden" id="id<?=$id?>" value="<?php echo $taskinfo['task_id']?>">
                        <p class="font-weight-light time-font text-center login-time" id="start-time<?=$id?>">
                            Started at <?php  echo $local_time; ?>
                        </p>
                        <div class="font-weight-light text-center primary-timer start-task-timer" id="task-timer<?=$taskinfo['task_id']?>" data-type="" data-time="">
                            00:00:00
                        </div>
                        <p class="font-weight-light text-center taskName" id="task-name<?=$id?>">
                            <?php echo $taskinfo['task_name']; ?>
                        </p>
                    </div>
                </div>
                <?php $id++; }
            } ?>
            </div>
        </div>
    </div>
</div>
<main class="container-fluid-main">
    <div class="md main-container-employee container timer">
        <div class="text-center shadow-lg topWidth stop-time" id="stop-time" data-tasktype="<?=$task_type?>" data-id="<?=$task_id?>">
            <h2><i id="icon-for-task" class="fas action-icon <?=$timerClass?>"></i></h2>
        </div>


        <div class="container">
            <?php if(!empty($task_info['task_status'])){
                $first_dislpay = 0;
               foreach($task_info['task_status'] as $taskinfo){ 
                $today_date = date("Y-m-d");
                $task_date = substr($taskinfo['start_time'],0,10);
                if(strcmp($today_date,$task_date) != 0) {
                    if($first_dislpay != 1 ) {
                    $first_dislpay = 1;
                ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script src="//stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script><!-- check for running tasks of previous date -->
            <script type="text/javascript">
                $.ajax({
                    type: "POST",
                    url: timeTrackerBaseURL + "index.php/user/get_running_task",
                    dataType: "json",
                    success: function(res) {
                    console.log(res['data']['task_data'].length);
                    $("#stop-now").modal("show");
                    }
                });
            </script>

        <?php } } } } ?>

            <div class="row mb-3 pt-2">
                <div class="col-6">
                    <h5 class="font-weight-light text-left recent-activites">Recent Activities</h5>
                </div>
                <div class="col-6">
                    <div class="dropdown text-right" id="dropdown-recent-acts">
                        <i class="fas fa-sliders-h sorting-icon" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                            <!-- sorting options -->
                            <a class="dropdown-item" href="#" data-type="name">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date">Created date</a>
                        </div>
                    </div>
                </div>
                <div class="col-12"><p id="alarmmsg" class="text-center"></p></div>
            </div>

            <div class='mb-5' id="attach-card">                
                <div class="row" id="activites-result"></div>
                <div class="text-center section-loader" id="section-loader">
                    <div class="loader-inner"><i class="fas fa-circle-notch fa-spin"></i> Loading...</div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright © <?=Date('Y')?> Printgreener.com</p>
        </footer>
        <!-- modal form for tasks that started onprevious date -->
        <div class="modal modal-stop-now fade" id="stop-now" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <form action="<?=base_url();?>index.php/user/stop_timer?id=<?php echo $task_info['task_status'][0]['task_id'] ?>" id="update-stop-now" method="post">
                        <div class="modal-header text-center">
                            <h5 class="modal-title">Stop now!</h5>
                        </div>
                        <div class="modal-body ">
                            <div class="input-group">
                                <p>Please stop the task "<strong>
                                    <?php echo $task_info['task_status'][0]['task_name'] ?> </strong>" that is already running.
                                </p>
                            </div>
                            <div class="input-group">
                                <p>Started at: <strong id="old-start-date">
                                <?php echo $task_info['task_status'][0]['start_time'] ?></strong></p>
                            </div>
                            <input type="hidden" id="previous-date" name="" value="<?=$task_info['task_status'][0]['start_time'] ?>">
                            <div>
                                
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php if (validation_errors()) { ?>
                                       <?php echo validation_errors(); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                              <?php  } ?>
                            </div>
                            <label for="old-datepicker">Enter end time: <span class="text-danger">*</span></label>
                            <input  class="check-for-utc form-control timerpicker-stop-now" type="text" name="time" id="stop-end-time" placeholder="End time" >
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <label for="task-description">Enter description: </label>
                            <input type="text" class="form-control "  name="task-description">
                        </div>
                    </div>
                        <p class="text-danger text-center" id="stop-now-error"></p>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-stop-now fade" id="previous-punch-in" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <form action="<?=base_url();?>index.php/user/stop_timer?id=<?php echo $task_info['task_status'][0]['task_id'] ?>" id="update-punch-in" method="post">
                        <div class="modal-header text-center">
                            <h5 class="modal-title">Punch out</h5>
                        </div>
                        <div class="modal-body ">
                            <div class="input-group">
                                <p>Please stop the enter punch out time for the last punch in.
                                </p>
                            </div>
                            <div class="input-group">
                                <p>Punched in at: <strong id="old-punch-in">
                                <?php echo $task_info['task_status'][0]['start_time'] ?></strong></p>
                            </div>
                            <input type="hidden" id="previous-punchout" name="" value="<?=$task_info['task_status'][0]['start_time'] ?>">
                            <div>
                                
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php if (validation_errors()) { ?>
                                       <?php echo validation_errors(); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                              <?php  } ?>
                            </div>
                            <label for="old-datepicker">Enter end time: <span class="text-danger">*</span></label>
                            <input  class="check-for-utc form-control timerpicker-stop-now" type="text" name="time" id="punchout-time" placeholder="End time" >
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <label for="punchout-description">Enter description: </label>
                            <input type="text" class="form-control "  name="punchout-description">
                        </div>
                    </div>
                        <p class="text-danger text-center" id="punchout-error"></p>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


        <div class="modal" id="play-timer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="<?=base_url();?>index.php/user/save_login_time" id="starting-timer" method="post">
                        <div class="modal-header ">
                            <button type="button" class="close text-danger" data-dismiss="modal">×</button>
                        </div>
                        <div class="modal-body ">
                            <div><p>You have not punched in for the day.</p>
                                <label for="old-datepicker">Please enter start time: <span class="text-danger">*</span></label>
                                <input type="text" class="check-for-utc form-control  timerpicker-c"  name="start-login-time" id="start-login-time" placeholder="hh:mm">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                        </div>
                            <p class="text-danger text-center" id="stop-timer-error"></p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="start-punchIn">Punch In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</main>
