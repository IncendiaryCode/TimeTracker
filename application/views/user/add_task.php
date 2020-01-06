<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'Add Task';
?>
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
                    <form action="<?=base_url();?>index.php/user/add_tasks" method="post" id="addTask" class="mt-5 ">
                        <button type="submit" id="save-and-start" class="text-center shadow-lg icon-width start-time">
                            <div data-tasktype="Task" data-id="80">
                                <h3> <i class=" fas action-icon fa-play"></i></h3>
                            </div>
                        </button>
                        <div class="form-group">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=set_value('task_name')?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4" value="<?=set_value('task_desc')?>"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select type="number" class="form-control project_name" id="choose-project" name="project" value="<?=set_value('project_name')?>">
                                <option>Select Project</option>
                                <?php
                                 foreach($result as $p){ ?>
                                <option value=<?php echo $p['id']?> label= <?php echo $p['name']; ?> >
                                    <?php echo $p['name']; ?>
                                </option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="choose-module">Choose project module</label>
                            <select type="number" class="form-control project_name" id="choose-module" name="project_module" value="<?=set_value('project_module')?>">
                                <option>Select module</option>

                            </select>
                        </div>
                        <div class="form-group pl-4">
                            <!-- new or completed tasks..-->
                            <div class="radio">
                                <label for="radio1" class="form-check-label">
                                    <input type="radio" id="newTask" name="task_type" class="form-check-input" checked>New task
                                </label>
                                <label for="radio1" class="form-check-label ml-5">
                                    <input type="radio" id="editTask" name="task_type" class="form-check-input">Completed task
                                </label>
                            </div>
                        </div>
                        <!-- if task is completed -->
                        <div id="task-times" class="display-none">
                            <div id="show_list">
                                <div class="row">
                                    <div class="col-3">
                                        <p><b>Date</b></p>
                                    </div>
                                    <div class="col-3">
                                        <p><b>Start time</b></p>
                                    </div>
                                    <div class="col-3">
                                        <p><b>End time</b></p>
                                    </div>
                                    <div class="col-3">
                                        <p><b>Description</b></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Add time  -->
                            <div id="task-add-time">
                                <div class="primary-wrap">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control datepicker-0" name="daterange[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-start">
                                                <div class="input-group-append">
                                                    <span class="input-group-text datepicker ">
                                                        <span class="fa fa-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-group date">
                                                <input id="start-time-0" class="form-control timepicker" name="daterange[0][start]" />
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-group date">
                                                <input id="end-time-0" class="form-control timepicker1" name="daterange[0][end]" />
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            <input id="description-0" class="form-control" name="daterange[0][description]" />
                                        </div>

                                        <div class="col-1 text-center">
                                            <a href="javascript:void(0);" id="add-new-time" title="Add">
                                                <i class="fas fa-plus icon-plus text-success"></i>
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
                        <hr />
                        <button type="submit" class="btn btn-primary">Save Task</button><!-- to store the task entry. -->
                    </form>
                </div>
            </div>
        </div>
            <hr class="mt-4">
        <footer class="footer">
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
