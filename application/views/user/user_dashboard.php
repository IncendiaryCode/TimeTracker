<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$login_time = $this->session->userdata('login_time');
$login = new DateTime($login_time);
$logintime = $login->getTimestamp();
$timer = '';
$timerClass = 'fa-stop';

$task_type = 'login';
$task_id = 0;
$start_text = 'Start punch in/out';
$task_id = '';
$task_name = 'Login';
if (!empty($task_info)) {     /*fetching task details*/ 
    //for ($i=0; $i < sizeof($task_info); $i++) { 
        foreach($task_info['task_status'] as $taskinfo){
        //print_r($taskinfo['task_name']);
    $start_text = 'Started at '.$taskinfo['start_time'];
    
    $task_name = $taskinfo['task_name'];
    $timerClass = 'fa-stop';
    $task_id = $taskinfo['task_id'];

    $timer = $taskinfo['start_time'];
   // $timer =($task_info[0]['task_date'].$task_info[0]['start_time']);
    $datetime1 = new DateTime();
    $datetime2 = new DateTime($timer);

    $interval = date_diff($datetime1, $datetime2);
    $timer =  strtotime($taskinfo['task_date'] . $interval->format(' %h:%i:%s'));

}
}
?>
<script type="text/javascript">
//this will be send to JS for timer to start
var __timeTrackerLoginTime = "<?=$logintime?>"; /*start date and time of the task.*/
</script>
<!-- new scoll for task -->
<div class="container timer-slider">
    <div class="row">
        <div class="col-md-12" id="here">
            <div id="timer-slider">
                <div>
                    <div class="section-slider" id="login-timer-details">
                        <p class="font-weight-light time-font text-center login-time" id="login-time">
                            <?=$logintime;?>
                        </p>
                        <div class="font-weight-light text-center primary-timer" id="primary-timer" data-type="" data-time="">
                            00:00:00
                        </div>
                        <p class="font-weight-light text-center taskName" id="taskName">
                            <?php echo "Login"; ?>
                        </p>
                    </div>
                </div>
            <?php 
            if(!empty($task_info)){
                $id=1;
                foreach($task_info['task_status'] as $taskinfo){ ?>
                    <div>
                        <div class="section-slider task-slider" id="login-timer-details<?=$id?>">
                            <input type="hidden" id="$taskinfo['task_id']" value="<?php echo $taskinfo['start_time']?>">
                            <p class="font-weight-light time-font text-center login-time" id="start-time<?=$id?>">
                                <?php echo $taskinfo['start_time'];?>
                                
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
            <h3><i class="fas action-icon <?=$timerClass?>"></i></h3>
        </div>
        <div class="container">
            <div class="sufee-alert font-weight-light alert with-close alert-dark fade show p-4">
                <!-- TODO... -->
                <i class="text-danger  fas fa-exclamation-triangle"></i>
                As task "Create login API for mobile" has not been ended.
                <a href="#" class="forgot-color"> Stop now!
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="row mb-3 pt-4">
                <div class="col-6">
                    <h4 class="font-weight-light text-left ">Recent Activites</h4>
                </div>
                <div class="col-6">
                    <div class="dropdown text-right" id="dropdown-recent-acts">
                        <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                            <!-- sorting options -->
                            <a class="dropdown-item" href="#" data-type="name">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date">Created date</a>
                        </div>
                    </div>
                </div>
            </div>
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
