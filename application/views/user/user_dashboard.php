<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//$this->load->library('session');
$timer = '';
$timerClass = 'fa-stop';

$task_type = 'login';
$task_id = 0;
$start_text = 'Start punch in/out';
$task_id = '';
$task_name = 'Login';
if (isset($task_status)) {     /*fetching task details*/ 
    $start_text = 'Started at '.$task_status['start_time'];
    //print_r($task_status); exit();
    $task_type = ($type == 'login') ? 'login' : 'task';
    $task_name = $task_status['task_name'];
    $timerClass = 'fa-stop';
    $task_id = $task_status['task_id'];

    $timer = $task_status['start_time'];
   // $timer =($task_status[0]['task_date'].$task_status[0]['start_time']);
    $datetime1 = new DateTime();
    $datetime2 = new DateTime($timer);

    $interval = date_diff($datetime1, $datetime2);
    $timer =  strtotime($task_status['task_date'] . $interval->format(' %h:%i:%s'));
}

?>
<script type="text/javascript">
    //this will be send to JS for timer to start

    var __timeTrackerStartTime = "<?=$timer?>";    /*start date and time of the task.*/ 
</script>

<!-- new scoll -->

<!-- <div style="height: 250px">
<span id="new_scoll">
    <section data-anchor="gallery 1">
        <div></div>
    </section>
    <section data-anchor="gallery 2">
        <div></div>
    </section>
    <section data-anchor="gallery 3">
        <div></div>
    </section>
    <section data-anchor="gallery 4">
        <div></div>
    </section>
    <section data-anchor="gallery 5">
        <div></div>
    </section>
    <section data-anchor="gallery 6">
        <div></div>
    </section>
</span>
</div> -->


<!-- <nav id="nav">
    <ul>
        <li class="nav nav-prev"><button type="button"></button></li>
        <li class="nav nav-next"><button type="button"></button></li>
    </ul>
</nav> -->


<div class="page-progress"style="display: none">
<svg class="radial-svg" width="50" height="100" transform="rotate(-90 0 0)" >
    <circle class="radial-fill" stroke-width="5" r="14" cx="20" cy="20"></circle>
</svg>
</div>


<!-- END new scoll -->




<div id="task-timer">
    <div id="login-timer-details">
        <p class="font-weight-light time-font text-center" id="login-time">
            <?=$start_text?>
        </p>
        <div class="text-center">
            <span class="font-weight-light text-center primary-timer" id="primary-timer">
                00:00:00
            </span>
        </div>
        <p class="font-weight-light text-center taskName" id="taskName">
            <?php
         echo "Login"; ?>
        </p>
    </div>
    <p class="text-center text-white pb-5">
        <?php     // if there is not task
    for ($taskNo=0; $taskNo < 2; $taskNo++) {
        ?>
        <span>.</span>
        <?php
    }
    ?>         
    </p>
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
                            <a class="dropdown-item" href="#" data-type="name">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date">Created date</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row mb-5' id="attach-card">     <!-- recent task details -->
                <div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>
            </div>
        </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
            </footer>
    </div>
</main>