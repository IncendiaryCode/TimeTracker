<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if($this->input->get()){ ?>
    <script type="text/javascript">
        var edit = 1;
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var edit = 0;
    </script>
<?php } ?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-sm-6 offset-sm-3">
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
                            <p id="alartmsg" class="text-center"></p>
                    </div>
                    <?php if($GLOBALS['page_title'] == 'Edit task'){ ?>
                    <form action="<?=base_url();?>index.php/user/edit_task?type=edit" method="post" id="editTask" class="mt-5 add-task">
                        <input type="hidden" name="task_id" id="curr-taskid" value="<?=$task_data[0][0]['task_id'];?>">
                    <?php } else { ?>
                        <form action="<?=base_url();?>user/add_tasks" method="post" id="addTask" class="mt-5 add-task">
                        <?php } ?>
                        <div class="form-group">
                            <label for="task-name ">Write the task name</label>
                            <?php if($this->input->get()){ ?>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=$task_data[0][0]['task_name']?>">
                            <?php } else { ?>
                                <input type="text" class="form-control" name="task_name" id="Taskname">
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <?php if($this->input->get()){ ?>
                            <textarea class="form-control" id="description" name="task_desc" rows="4" value="<?=$task_data[0][0]['description']?>"></textarea>
                        <?php } else { ?>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"></textarea> <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <?php if($this->input->get()){ ?>
                                <select readonly="" type="number" class="form-control" id="choose-project" name="project">
                                <option selected value=<?php echo $task_data[0][0]['project_id']?>>
                                    <?=$task_data[0][0]['name'];?>
                                </option>
                            </select>
                            <?php } else { ?>
                            <select type="number" class="form-control project_name" id="choose-project" name="project" value="<?=$task_data[0][0]['name']?>">
                                <option>Select Project</option>
                                <?php
                                 foreach($result as $p){ ?>
                                <option value=<?php echo $p['id']?> >
                                    <?php echo $p['name']; ?>
                                </option>
                                <?php }?>
                            </select>
                            <?php }?>
                        </div>
                        <div class="form-group">
                            <label for="choose-module">Choose project module</label>
                            <select type="number" class="form-control project_name" id="choose-module" name="project_module" value="<?=$task_data[0][0]['module_name']?>">
                                <option>Select module</option>

                            </select>
                        </div>
                        <strong><p class="display-5 pt-4">Timeline</p></strong>
                        <div id="task-times">
                            <div id="show_list">
                                <div class="row">
                                    <div class="col-4 col-md-6">
                                        <p>Date</p>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <p>Start time</p>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <p>End time</p>
                                    </div>
                                </div>
                            </div>
                        <?php if($this->input->get()){ ?>

                            <div class="row" id="total-row">
                            <input type="hidden" id="task-len" value="<?=sizeof($task_data[0])?>">
                            <?php $num = 0;
                            foreach($task_data[0] as $task){
                                ?>
                                <p class="new-task-entry"><div class="col-4 col-md-6">
                                    <div class="input-group mt-3">
                                        <input type="text" class="form-control datepicker" id="date<?=$num?>" name="time[<?=$num?>][date]" data-date-format="yyyy-mm-dd" value="<?=$task['task_date'];?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <button class="btn p-0 fa fa-calendar " type="button"></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3 mt-3 edit-timings">
                                    <input class="timepicker<?=$num?> form-control " type="text" id="start<?=$num?>" name="time[<?=$num?>][start]" value="<?=$task['start_time'];?>">
                                </div>
                                <div class="col-4 col-md-3 mt-3">
                                    <input class="form-control timepicker<?=$num+1?>" type="text" id="end<?=$num?>" name="time[<?=$num?>][end]" value="<?=$task['end_time'];?>">
                                </div>
                                <div class="col-10 mt-3 mb-5">
                                    <input type="text" class="form-control" name="time[0][task_description]" value="<?=$task['task_description'];?>">
                                </div>
                                <div class="col-2 mt-3 mb-5">
                                    <a href="javascript:void(0);" id="delete-task">
                                    
                                        <i class="fas fa-trash text-danger pt-2 icon-plus" name="time[<?=$num?>][deleted_time_range]" ><input type="hidden" value="<?=$task['table_id'];?>" name="time[<?=$num?>][table_id]" id="table_id<?=$num?>"></i>
                                    </a>
                                </div>
                                <?php $num=$num+2;
                                } ?>
                                <div class="col-12 text-left mb-4">
                                    <div id="add-time">
                                        <a href="javascript:void(0);" >
                                            <i class="fas fa-plus pt-2 icon-plus"></i>
                                        </a>Add time
                                    </div>
                                </div>
                                <div id="add-here"></div>
                                <div class="col-12 text-right" id="show-plus">
                                    <a href="javascript:void(0);" id="add-new-time" title="Add">
                                        <i class="fas fa-plus pt-2 icon-plus"></i>
                                    </a>
                                </div>
                            </p>
                            </div>

                            <?php } ?>
                            <!-- Add time  -->

                            <div id="task-add-time">
                                <div class="primary-wrap">
                                    <div class="row">
                                        <div class="col-4 col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control datepicker-0" name="time[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-start-0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text datepicker ">
                                                        <button type="button" class="btn p-2 fa fa-calendar"></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <div class="input-group date">
                                                <input id="start-time-0" class="form-control timepicker-a" name="time[0][start]" placeholder="hh:mm" />
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <div class="input-group date">
                                                <input id="end-time-0" class="form-control timepicker-b" name="time[0][end]"  placeholder="hh:mm" />
                                            </div>
                                        </div>
                                        <div class="col-10 text-center">
                                            <input id="description-0" class="form-control" name="time[0][task_description]" placeholder="description" />
                                        </div>

                                        <div class="col-2 text-center">
                                            <a href="javascript:void(0);" id="add-new-time" title="Add">
                                                <i class="fas fa-plus pt-2 icon-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END: Add time  -->
                            <div class="text-left text-danger">
                                <p id="datetime-error" class="pt-4"></p>
                            </div>
                        </div>
                        <p id="taskError" class=" text-danger"></p>
                        <p>&nbsp;</p>
                        <div class="text-right">
                        <button type="submit" class="btn btn-primary save-task">Save Task</button><!-- to store the task entry. -->
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <footer class="footer">
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
