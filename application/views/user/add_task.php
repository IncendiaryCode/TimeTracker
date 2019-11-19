<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'Add Task';
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
                    <form action="<?=base_url();?>index.php/user/add_tasks" method="post" id="addTask" class="mt-5 ">
                        <div class="form-group  ">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select type="number" class="form-control" id="choose-project" name="project_name">

                                <option>Select Project</option>
                                <?php 
                                 foreach($result as $p){ 
                                    ?>
                                <option><?=$p['name'];  ?></option>
                            <?php }?>
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
                                <div class="row" >
                                    <div class="col-3">
                                        <p><b>Date</b></p>
                                    </div>
                                    <div class="col-3">
                                        <p><b>Start time</b></p>
                                    </div>
                                    <div class="col-3">
                                        <p><b>End time</b></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Add time  -->
                            <div id="task-add-time">
                                <div class="primary-wrap">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control datepicker" name="date-0" data-date-format="dd/mm/yyyy" id="date-picker-start">
                                              <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </span>
                                              </div>                                          
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-group date">
                                                <input id="start-time-0" class="form-control timepicker" name="start-time-0" />
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-group date">
                                                <input id="end-time-0" class="form-control timepicker1" name="end-time-0" />
                                            </div>
                                        </div>
                                        <div class="col-3 text-center">
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
                        <button type="button" class="btn btn-primary ml-5" id="save-and-start">Save and Start</button><!-- to store the task entry. -->
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
