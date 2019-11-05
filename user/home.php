<?php
include("header.php");
if(!isset($_SESSION['user'])){
      header("location:../index.php");
      die();
}

include("../php/task_status.php");

$timer = '';
$timerClass = 'fa-play';
$task_type = 'login';
$task_id = 0;
$start_text = 'Start punch in/out';
$task_id = '';
$task_name = '';

if (!empty($task_status)) {     /*fetching task details*/   
    $start_text = 'Started at '.$task_status[0]['start_time'];
    $task_type = ($task_status[0]['type'] == 'login') ? 'login' : 'project';
    $task_name = $task_status[0]['task_name'];
    $timerClass = 'fa-stop';
    $task_id = $task_status[0]['t_id'];

    // $timer = strtotime($task_status[0]['t_date'].$task_status[0]['start_time']);
    $timer =(($task_status[0]['t_date'].$task_status[0]['start_time']));
    $datetime1 = new DateTime();
    $datetime2 = new DateTime($timer);

    $interval = date_diff($datetime1, $datetime2);
    $timer =  strtotime($task_status[0]['t_date'] . $interval->format(' %h:%i:%s'));
}
?>
<script type="text/javascript">
    //this will be send to JS for timer to start
    var __timeTrackerStartTime = "<?=$timer?>";    /*start date and time of the task.*/ 
</script>
<div>
    <p class="font-weight-light time-font text-center" id="login-time"><?=$start_text?></p>
    <p class="font-weight-light text-center" id="primary-timer">
        00:00:00
    </p>
    <p class="font-weight-light text-center" id="taskName"><?=$task_name?></p>
</div>
<main class="container-fluid-main">
    <div class="md main-container-employee container timer">
        <div class="text-center shadow-lg topWidth stop-time" id="stop-time" data-tasktype="<?=$task_type?>" data-id="<?=$task_id?>">
            <h3><i class="fas action-icon <?=$timerClass?>"></i></h3>
        </div>        
        <div class="container">
            <div class="sufee-alert font-weight-light alert with-close alert-dark fade show p-4">   <!-- TODO... -->
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
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">   <!-- sorting options -->
                            <a class="dropdown-item" href="#" data-type="task_asc">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date_asc">Created date</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row mb-5' id="attach-card">     <!-- recent task details -->
                <div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>
            </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
            </footer>
        </div>
    </div>
</main>
<?php include("footer.php"); ?>
