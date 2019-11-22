<?php
$GLOBALS['page_title'] = 'Edit Task';
$this->load->library('session');
$profile = $this->session->userdata('user_profile');
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-6 offset-3">
                    <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                    <?php } ?>
                    <div class="alert-success"><?php echo isset($success)?$success:""; ?></div>
                    <form action="<?=base_url();?>index.php/user/edit_task" method="post" id="addTask" class="mt-5 ">
                        <input type="hidden" name="task_id" value="<?=$task_data['task_id'];?>">
                        <div class="form-group  ">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=$task_data['task_name'];?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"><?=$task_data['description'];?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select readonly="" type="number" class="form-control" id="choose-project" name="project_name" >
                                <option selected value=<?php echo $task_data['project_id']?>><?=$task_data['name'];?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Started on</label>
                            <div class="input-group">
                                <?php $start = $task_data['task_date']." ".$task_data['start_time']; ?>
                                <input readonly="" type="text" class="form-control" id="started-date" name="start_date" aria-describedby="date-start" value="<?=$start;?>">
                                <div class="input-group-append">
                                <span class="input-group-text" id="date-start"><i class="fas fa-calendar"></i></span>
                              </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end-date">Ended on</label>
                            <div class="input-group">
                                <?php $end = (($task_data['end_time'] == '0000-00-00 00:00:00') || ($task_data['end_time'] == null)) ? '' : $task_data['task_date']." ".$task_data['end_time']; ?>
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
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js">
            $(function() {
               $('#end_date').datetimepicker();
             });
        </script>
        <footer class="footer">
            <hr>
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>

    </div>
</main>