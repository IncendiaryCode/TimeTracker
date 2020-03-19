<?php
defined('BASEPATH') or exit('No direct script access allowed');
//Login timer
// print_r($tasks_list);
$this->load->helper('date');
if (isset($task_info['login_status'])) {
    $login_time = $task_info['login_status']['start_time'];
    $login_time_display = date('g:i A', strtotime($task_info['login_status']['start_time']));
    $login = new DateTime($login_time, new DateTimeZone('UTC'));
    $logintime = $login->getTimestamp();
} else //Disable punchin card for the day
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

if(empty($task_info['login_status'])){
$not_logged = 1;
}else{
$not_logged = 0;
}
if (isset($task_info['login_status'])) {
    if ($task_info['login_status']['end_time'] == NULL) {
        $timerClass = 'fa-stop';
    } else {
        $timerClass = 'fa-play';
    }
} else {
    $timerClass = 'fa-play';
}
if (isset($task_info['login_status']['end_time']) && ($task_info['login_status']['end_time']) != NULL) {
    $already_logged = 1;
} else {
    $already_logged = 0;
}
?>
<?php
if (!empty($task_info['login_run'])) { ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $("#previous-punch-in").modal("show");
    </script>
<?php } ?>
<script type="text/javascript">
    //this will be send to JS for timer to start
    var __timeTrackerLoginTime = "<?= $logintime ?>"; /*start date and time of the task.*/
    var stopped = "<?= $already_logged ?>"; /*to check for punch out action*/
    var not_logged = "<?= $not_logged ?>"; /*to check for punch in action*/
    var task_arr =  JSON.parse('<?php echo json_encode($tasks_list) ?>');

</script>
<!-- new scoll for task -->
<div class="container timer-slider">
    <div class="row">
        <div class="col-md-12">
            <div id="timer-slider">
                <!-- slider for login activity -->
                <div>
                    <div class="section-slider" id="login-timer-details">
                        <p class="mb-0 font-weight-light time-font text-center login-time" id="login-time">
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
                if (!empty($task_info['task_status'])) {
                    $id = 1;
                    //task timer
                    foreach ($task_info['task_status'] as $taskinfo) {
                        $timer = $taskinfo['start_time'];
                        $timer_display = date('g:i A', strtotime($taskinfo['start_time']));
                        $datetime2 = new DateTime($timer, new DateTimeZone('UTC'));
                        $timer_start = $datetime2->getTimestamp();
                        $task_start = strtotime($timer);
                        $task_id = $taskinfo['task_id'];
                        ?>
                        <div id="slider<?= $task_id ?>">
                            <!-- slider for all task -->
                            <div class="section-slider task-slider" id="login-timer-details<?= $id ?>">
                                <input type="hidden" id="<?php echo $taskinfo['task_id'] ?>" value="<?php echo $timer_start ?>">
                                <input type="hidden" id="id<?= $id ?>" value="<?php echo $taskinfo['task_id'] ?>">
                                <p class="font-weight-light time-font text-center login-time" id="start-time<?= $id ?>">
                                    Started at <?php echo $timer_display; ?>
                                </p>
                                <div class="font-weight-light text-center primary-timer start-task-timer" id="task-timer<?= $taskinfo['task_id'] ?>" data-type="" data-time="">
                                    00:00:00
                                </div>
                                <p class="font-weight-light text-center taskName" id="task-name<?= $id ?>">
                                    <?php echo $taskinfo['task_name']; ?>
                                </p>
                            </div>
                        </div>
                <?php $id++;
                    }
                } ?>
            </div>
        </div>
    </div>
</div>
<main class="container-fluid-main">
    <div class="md main-container-employee container timer">
        <div class="text-center shadow-lg topWidth stop-time" id="stop-time" data-tasktype="<?= $task_type ?>" data-id="<?= $task_id ?>">
            <h2><i id="icon-for-task" class="fas action-icon <?= $timerClass ?>"></i></h2>
        </div>
        <div class="container">
            <div class="row mb-3 pt-2">
                <div class="col-md-6 col-3">
                    <h5 class="font-weight-light text-left recent-activites">Recent Activities</h5>
                </div>
                <div class="col-md-6 col-9 text-right">
                    <ul class="nav float-right">
                        <li class="nav-item" id="today-filter">
                            <span class="pt-2">Today</span>
                            <label class="switch switch-default switch-pill switch-primary mr-2">
                                <input type="checkbox" class="switch-input" id="today-input">
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </li>
                        <li class="nav-item " id="clear-filter">
                            <a class="nav-link pt-0" href="#"><i class="far fa-times-circle"></i> Clear filter</a>
                        </li>                       
                        <li class="nav-item">
                            <a class="nav-link pt-0" id="action-filter" href="#navbarToggleExternalContent" data-toggle="collapse" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle Filter">
                                <i class="fas fa-sliders-h"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                
                <div class="col-12">
                    <div class="collapse" id="navbarToggleExternalContent">
                        <div class="p-4">
                            <form action="#" id="dashboard-filter">
                                <div class="row">
                                    
                                    <div class="col-4" id="sorting">
                                        <h5 class="pb-2">Sort by</h5>
                                        <div class="form-check">
                                            <input class="form-check-input" data-type="name" type="radio" name="exampleRadios" id="date-radio" value="date" checked>
                                            <label class="form-check-label" for="date-radio">
                                                Date
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" data-type="name" type="radio" name="exampleRadios" id="task-radio" value="task">
                                            <label class="form-check-label" for="task-radio">
                                                Task
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" data-type="project" type="radio" name="exampleRadios" id="project-radio" value="project">
                                            <label class="form-check-label" for="project-radio">
                                                Project
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" data-type="duration" type="radio" name="exampleRadios" id="duration-radio" value="duration">
                                            <label class="form-check-label" for="duration-radio">
                                                Duration
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4" id="filtering">
                                        <h5 class="pb-2">Projects</h5>
                                        <?php
                                            for($p=0; $p<sizeof($project_list); $p++) { ?>                                        
                                            <div class="form-check custom-control custom-checkbox">
                                                <input class="form-check-input " type="checkbox" id="check-<?=$p?>">
                                                    <input type="hidden" class="custom-control-input" value=<?=$project_list[$p]['id'] ?>>
                                                    <label class="form-check-label" for="check-<?=$p?>">
                                                    <?=$project_list[$p]['name'] ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-4 text-right">
                                        <button type="submit" class="btn btn-primary">Apply </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                    </div>
                </div>
                 <div class="col-12">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-task" placeholder="search">
                        <div class="input-group-append" id="show-loader">
                            <div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div></div> 
                        </div>
                    </div>
                    <div id="append-search-results"></div>
                    <p id="search-error"></p>
                </div>
                <div class="col-12">
                    <p id="alarmmsg" class="text-center"></p>
                </div>
            </div>

            <div class='mb-5' id="attach-card">
                <div class="row" id="activites-result"></div>
                <div class="row" id="no-data">
                    <div class="col-12 text-center no-data">
                        <img src="https://github.com/IncendiaryCode/TimeTracker/blob/dev_web_ci/assets/images/no-data.png?raw=true" class="img-fluid">
                        <h3>No task available for today. <a class="" href="<?= base_url(); ?>user/load_add_task" onclick="return check_for_punchIn()">Add a task</a></h3>
                    </div>
                </div>

                <div class="text-center section-loader" id="section-loader">
                    <div class="loader-inner"><i class="fas fa-circle-notch fa-spin"></i> Loading...</div>
                </div>
            </div>
        
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright ©  <?=date("Y") ?> | TimeTracker.com</p>
        </footer>
        <!-- modal form for tasks that started onprevious date -->
        <?php if(!empty($task_info['task_run'])) { ?>
        <div class="modal modal-stop-now fade" id="stop-now" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <form action="<?= base_url(); ?>index.php/user/stop_timer?id=<?php echo $task_info['task_run'][0]['task_id'] ?>" id="update-stop-now" method="post">
                        <div class="modal-header text-center">
                            <h5 class="modal-title">Stop now!</h5>
                        </div>
                        <div class="modal-body ">
                            <div class="input-group">
                                <p>Please stop the task "<strong><?php echo $task_info['task_run'][0]['task_name'] ?> </strong>" that is already running.
                                </p>
                            </div>
                            <div class="input-group">
                                <p>Started at: <strong id="old-start-date"><?php echo $task_info['task_run'][0]['start_time'] ?></strong></p>
                            </div>
                            <input type="hidden" id="previous-date" name="" value="<?= $task_info['task_run'][0]['start_time'] ?>">
                            <div>
                                <?php if (validation_errors()) { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo validation_errors(); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php  } ?>
                                
                                <label for="old-datepicker">Enter end time: <span class="text-danger">*</span></label>
                                <input class="check-for-utc form-control timerpicker-stop-now" type="text" name="time" id="stop-end-time" placeholder="End time">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                            <div class="pt-3">
                                <label for="task-description">Enter description: </label>
                                <input type="text" class="form-control " name="task-description">
                            </div>
                        </div>
                        <p class="text-danger text-center" id="stop-now-error"></p>
                        <div class="modal-footer text-center">
                            <button type="submit" class="btn btn-primary col-4">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } if(!empty($task_info['login_run'])){?>
        <div class="modal modal-stop-now fade" id="previous-punch-in" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="false">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <form action="" id="update-punch-in" method="post">
                        <input type="hidden" id="login-id" name="" value="<?= $task_info['login_run']['id'] ?>"><!-- keep value as login id -->
                        <div class="modal-header text-center">
                            <h5 class="modal-title">Previous punch out</h5>
                        </div>
                        <div class="modal-body ">
                            <div class="input-group">
                                <p>Please enter punch out time for the day.<strong id="old-punch-in"><?php echo $task_info['login_run']['start_time'] ?></strong>
                                </p>
                            </div>
                            <input type="hidden" id="previous-punchout" name="" value="<?= $task_info['login_run']['start_time'] ?>">
                            <div>
                                <?php if (validation_errors()) { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo validation_errors(); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                <?php  } ?>
                                <input class="check-for-utc form-control timerpicker-stop-now" type="text" name="time" id="punchout-time" placeholder="HH:mm">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>

                        </div>
                        <p class="text-danger text-center" id="punchout-error"></p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php } ?>
    </div>
</main>