<?php
$GLOBALS['page_title'] = 'Edit task';
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                    <div class="alert alert-danger">
                        <?php echo validation_errors();
                        echo (!empty($this->session->flashdata('failure')))?$this->session->flashdata('failure'):'';
                         ?>
                    </div>
                    <?php } ?>
                    <div class="alert-success">
                        <?php echo (!empty($this->session->flashdata('success')))?$this->session->flashdata('success'):''; ?>
                         </div>
                    <form action="<?=base_url();?>index.php/user/edit_task?type=edit" method="post" id="editTask" class="mt-5 ">
                        <input type="hidden" name="task_id" id="curr-taskid" value="<?=$task_data[0][0]['task_id'];?>">
                        <?php if($task_data[1]['running_task'] == 1)
                        { ?>
                        <button type="button" id="stop-or-complete" class="text-center shadow-lg icon-width stop-or-complete">
                            <div data-tasktype="Task" data-id="80">
                                <h3> <i class=" fas action-icon fa-stop"></i></h3>
                            </div>
                        </button>
                        <?php } ?>
                        <div class="form-group">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=$task_data[0][0]['task_name'];?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"><?=$task_data[0][0]['description'];?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select readonly="" type="number" class="form-control" id="choose-project" name="project">
                                <option selected value=<?php echo $task_data[0][0]['project_id']?>>
                                    <?=$task_data[0][0]['name'];?>
                                </option>
                            </select>
                        </div>
                        <h4 class="mt-4 text-center">Task activities</h4>
                        <div class="row">
                            <div class="col-1 col-md-1"><b>#</b></div>
                            <div class="col-4 col-md-4"><b>Start time</b></div>
                            <div class="col-3 col-md-4"><b>End time</b></div>
                            <div class="col-4 col-md-3"><b>Description</b></div>
                        </div>
                        <div class="row" id="total-row">
                            <?php $num = 0;
                          foreach($task_data[0] as $task){
                            ?>
                                <div class="col-1 col-md-1  mt-3">
                                <input type="hidden" name="time[<?=$num?>][table_id]" value="<?php echo $task['id']?>" >
                                    <?=$num;?>
                                </div>
                                <div class="col-4 col-md-4 mt-3">
                                    <input class="form-control edit-date-time" type="text" id="start<?=$num?>"  name="time[<?=$num?>][start]" value="<?=$task['start_time'];?>" placeholder="<?=$task['start_time'];?>">
                                </div>
                                <div class="col-3 col-md-4 mt-3">
                                    <input class="form-control edit-date-time" type="text" id="end<?=$num?>"  name="time[<?=$num?>][end]" value="<?=$task['end_time'];?>" placeholder="<?=$task['end_time'];?>">
                                </div>
                                <div class="col-4 col-md-3 mt-3">
                                    <input type="text" class="form-control" name="time[<?=$num?>][task_description]" value="<?=$task['task_description'];?>">
                                </div>
                            <?php $num=$num+1;
                        } ?>
                        </div>
                            <p id="taskError" class=" text-danger mt-3"></p>
                            <p id="user-alerting" class=" text-danger mt-3"></p>
                            <p>&nbsp;</p>
                            <hr />
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