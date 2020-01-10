<?php
$GLOBALS['page_title'] = 'Edit task';
print_r($task_data);
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
                        <P class="pt-3">Time line</P>
                        <div class="row">
                            <div class="col-4 col-md-4"><b>Date</b></div>
                            <div class="col-3 col-md-4"><b>Start time</b></div>
                            <div class="col-4 col-md-4"><b>End time</b></div>
                        </div>
                        <div class="row" id="total-row">
                            <input type="hidden" id="task-len" value="<?=sizeof($task_data[0])?>">
                            <?php $num = 0;
                          foreach($task_data[0] as $task){
                            ?>
                                <div class="col-4">
                                    <div class="input-group mt-3">
                                        <input type="text" class="form-control datepicker" id="date<?=$num?>" name="time[<?=$num?>][date]" data-date-format="yyyy-mm-dd" value="<?=$task['task_date'];?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <button class="btn p-2 fa fa-calendar" type="button"></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4  mt-3 edit-timings">
                                    <input class="edit<?=$num?> timepicker<?=$num?> form-control " type="text" id="start<?=$num?>" name="time[<?=$num?>][start]" value="<?=$task['start_time'];?>">
                                    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                                    <script type="text/javascript">
                                        
                                    </script>
                                </div>
                                <div class="col-4 mt-3">
                                    <input class="form-control timepicker<?=$num+1?>" type="text" id="end<?=$num?>" name="time[<?=$num?>][end]" value="<?=$task['end_time'];?>">
                                </div>
                                <div class="col-12 mt-3 mb-5">
                                    <input type="text" class="form-control" name="time[<?=$num?>][task_description]" value="<?=$task['task_description'];?>">
                                </div>
                                <input type="hidden" value="<?=$task['table_id'];?>" name="time[<?=$num?>][table_id]" id="table_id<?=$num?>">
                            <?php $num=$num+2;
                        } ?>
                        </div>
                            <p id="taskError" class=" text-danger mt-3"></p>
                            <p id="user-alerting" class=" text-danger mt-3"></p>
                            <p>&nbsp;</p>
                            <hr />
                            <div class="text-right">
                            <button type="submit" class="btn btn-primary text-right">Save Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       <footer class="footer">
            <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>