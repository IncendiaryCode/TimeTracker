<?php
$GLOBALS['page_title'] = 'Edit Task';
include("header.php");
include("../php/activity.php");
// echo '<pre>'; print_r($project_data); exit;
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-6 offset-3">
                    <form action="<?=BASE_URL?>php/save_task.php" method="post" id="addTask" class="mt-5 ">
                        <div class="form-group  ">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=$project_data[0]['task_name'];?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"><?=$project_data[0]['description'];?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select readonly="" type="number" class="form-control" id="choose-project" name="project_name">
                                <option selected><?=$project_data[0]['name'];?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Started on</label>
                            <div class="input-group">
                                <?php $start = $project_data[0]['t_date']." ".$project_data[0]['start_time']; ?>
                                <input readonly="" type="text" class="form-control datetimepicker" id="started-date" name="start_date" aria-describedby="date-start" value="<?=$start;?>">
                                <div class="input-group-append">
                                <span class="input-group-text" id="date-start"><i class="fas fa-calendar"></i></span>
                              </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end-date">Ended on</label>
                            <div class="input-group">
                                <?php $end = ($project_data[0]['end_time'] == '00:00:00') ? '' : $project_data[0]['t_date']." ".$project_data[0]['end_time']; ?>
                                <input type="text" class="form-control datetimepicker" id="end-date" name="end_date" aria-describedby="date-end" value="<?=$end;?>">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="date-end"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>                        
                        <p id="taskError" class=" text-danger"></p>
                        <p>&nbsp;</p> 
                        <hr/>
                        <button type="submit" class="btn btn-primary">Save Task</button>
                    </form>
                </div>
            </div>
        </div>
        
        <footer class="footer">
            <hr>
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
<?php include("footer.php"); ?>