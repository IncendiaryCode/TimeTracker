<?php
include("header.php");
if(!isset($_SESSION['user'])){
      header("location:../index.php");
      die();
}

include("../php/task_status.php");

// print_r($task_status); exit();

$timer = '';
$timerClass = 'fa-play';
$tasktype = 'login';
if (!empty($task_status)) {
    $tasktype = 'project';
    $timerClass = 'fa-stop';
    $timer = strtotime($task_status[0]['t_date'].$task_status[0]['start_time']);
}
?>
<script type="text/javascript">
    //this will be send to JS for timer to start
    var __timeTrackerStartTime = "<?=$timer?>";
</script>
<div>
    <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
    <p class="font-weight-light time-font text-center" id="login-time">Started at <?=$task_status[0]['start_time']?></p>
    <p class="font-weight-light text-center" id="primary-timer">
        00:00:00
    </p>
    <p class="font-weight-light text-center" id="taskName"><?=$task_status[0]['task_name']?></p>
</div>
<main class="container-fluid-main">
    <div class="md main-container-employee container timer">
        <div class="text-center shadow-lg topWidth stop-time" id="stop-time" data-tasktype="<?=$tasktype?>" data-id="<?=$task_status[0]['t_id']?>">
            <h3><i class="fas action-icon <?=$timerClass?>"></i></h3>
        </div>        
        <div class="container">
            <!-- <div class="sufee-alert font-weight-light alert with-close alert-dark fade show p-4">
                <i class="text-danger  fas fa-exclamation-triangle"></i>
                As task "Create login API for mobile" has not been ended.
                <a href="#" class="forgot-color"> Stop now!
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div> -->
            <div class="row mb-3 pt-4">
                <div class="col-6">
                    <h4 class="font-weight-light text-left ">Recent Activites</h4>
                </div>
                <div class="col-6">
                    <div class="dropdown text-right" id="dropdown-recent-acts">
                        <i class="fas fa-sliders-h"  id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                            <a class="dropdown-item" href="#" data-type="task_asc">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date_asc">Created date</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row mb-5' id="attach-card">
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
