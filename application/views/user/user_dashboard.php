<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Login timer
$this->load->helper('date');
$login_time = $task_info['login_status']['start_time'];
$login = new DateTime($login_time,new DateTimeZone('UTC'));
$logintime = $login->getTimestamp();
$time_login = strtotime($login_time);
$timer = '';
$timerClass = 'fa-stop';
//print_r($task_info['login_status']['start_time']);exit;
$task_type = 'login';
$task_id = 0;
$start_text = 'Start punch in/out';
$task_id = '';
$task_name = 'Login';
?>
<script type="text/javascript">
//this will be send to JS for timer to start
var __timeTrackerLoginTime = "<?=$logintime?>"; /*start date and time of the task.*/
</script>
<!-- new scoll for task -->
<div class="container timer-slider">
    <div class="row">
        <div class="col-md-12">
            <div id="timer-slider">
                <div>
                    <div class="section-slider" id="login-timer-details">
                        <p class="font-weight-light time-font text-center login-time" id="login-time">
                            Loged in at: <?php echo unix_to_human($time_login); ?>
                        </p>
                        <div class="font-weight-light text-center primary-timer" id="primary-timer">
                            00:00:00
                        </div>
                        <p class="font-weight-light text-center taskName" id="taskName">
                            <?php echo "Login"; ?>
                        </p>
                    </div>
                </div>
                <?php 
            if(!empty($task_info['task_status'])){
                $id=1;
                //task timer
                foreach($task_info['task_status'] as $taskinfo){
                    $timer = $taskinfo['start_time'];
                    $datetime2 = new DateTime($timer,new DateTimeZone('UTC'));
                    $timer_start = $datetime2->getTimestamp();
                    $task_start = strtotime($timer);
                    $task_id = $taskinfo['task_id'];
                     ?>
                <div id="slider<?=$task_id?>">
                    <div class="section-slider task-slider" id="login-timer-details<?=$id?>">
                        <input type="hidden" id="<?php echo $taskinfo['task_id'] ?>" value="<?php echo $timer_start?>">
                        <input type="hidden" id="id<?=$id?>" value="<?php echo $taskinfo['task_id']?>">
                        <p class="font-weight-light time-font text-center login-time" id="start-time<?=$id?>">
                            Started at: <?php  echo unix_to_human($task_start);?>
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
            <h3><i id="icon-for-task" class="fas action-icon <?=$timerClass?>"></i></h3>
        </div>









        <!-- <div class="modal modal-transparent fade" id="stop-now" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-header">
                    <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times  main-modal-close"></i></button>
                </div>
                <div class="modal-content text-center">
                    
                </div>
            </div>
        </div> -->


        <div class="modal modal-transparent fade" id="stop-now" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <form action="<?=base_url();?>index.php/user/stop_timer?id=<?php echo $task_info['task_status'][0]['task_id'] ?>" id="update-stop-now" method="post">
                        <div class="modal-header text-center">
                            <h5 class="modal-title">Stop now!</h5>
                        </div>
                        <div class="modal-body ">
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
                                <input type="text" class="form-control  timepicker"  name="stop_end_time">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                            <div class="pt-3">
                                <label for="task-description">Enter description: </label>
                                <input type="text" class="form-control "  name="stop_task-description">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" >Next</button>
                        </div>
                    </form>
                </div>
            </div>
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
                    
          <!--   <div class="sufee-alert font-weight-light alert with-close alert-dark fade show p-4 alert-box">
                <i class="text-danger  fas fa-exclamation-triangle"></i>
                As task "<?php echo $taskinfo['task_name'] ?>" has not been ended.
                <a href="#" class="forgot-color" id="stop-now" data-toggle="modal" data-target="#end-time-update"> Stop now!</a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>  -->


        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script src="//stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
            <script type="text/javascript">
                $.ajax({
                    type: "POST",
                    url: timeTrackerBaseURL + "index.php/user/get_running_task",
                    dataType: "json",
                    success: function(res) {
                    }
                });
                    $("#stop-now").modal("show");
            </script>

        <?php } } } } ?>


            

            <div class="row mb-3 pt-4">
                <p id="alarmmsg" class="text-center"></p>
                <div class="col-6">
                    <h4 class="font-weight-light text-left ">Recent Activites</h4>
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
            </div>

            <!-- <div class="row empty-card ">
                <div class="col-6 ">
            <div class = 'card card-style background content-overlay' >
                <div class='card-body' >
                    
                   <div class="row" >
                    <div class='col-6'>
                    <div class="card-body">
                        <i class="fas action-play fa-play animated fadeInRight" data-toggle="tooltip" data-placement="top" title="Resume"></i>
                        <i class="fas action-edit fa-edit animated fadeInRight" data-toggle="tooltip" data-placement="top" title="Resume"></i>
                    </div>
                    </div>
                    <div class='col-6 text-right card-actions' id='footer-right'>
                    </div>
                </div></div></div> </div></div>
 -->
            <div class='row mb-5' id="attach-card">
                <!-- recent task details -->



                     
                   
                    


                <div class="col text-center">
                    <div class="spinner-border" role="status" aria-hidden="true"></div> Loading...
                </div>
            </div>
        </div>
        <hr>
        <footer>
            <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>