<?php
defined('BASEPATH') or exit('No direct script access allowed');
$local_start_time = 0;
if ($this->input->get('t_id')) { ?>
    <script type="text/javascript">
        var edit = 1;
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var edit = 0;
    </script>
<?php }
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row">
                <div class="col-sm-10 offset-sm-1 mt-5">
                    <?php if (!empty($this->session->flashdata('failure'))) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo (!empty($this->session->flashdata('failure'))) ? $this->session->flashdata('failure') : ''; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('success'))) { ?>
                        <div class="alert alert-success mb-5">
                            <?php echo (!empty($this->session->flashdata('success'))) ? $this->session->flashdata('success') : ''; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if(!empty($this->session->flashdata('date_failure'))) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           <?php echo (!empty($this->session->flashdata('date_failure'))) ? $this->session->flashdata('date_failure') : ''; ?>
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if(!empty($this->session->flashdata('start_end'))) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           <?php echo (!empty($this->session->flashdata('start_end'))) ? $this->session->flashdata('start_end') : ''; ?>
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if (isset($task_data['task_id'])) { ?>
                        <form action="<?= base_url(); ?>index.php/user/edit_task?id=edit" method="post" id="editTask" class="add-task">
                            <input type="hidden" name="task_id" id="curr-taskid" value="<?= $task_data['task_id']; ?>">
                        <?php } else { ?>
                            <form action="<?= base_url(); ?>user/add_tasks" method="post" id="addTask" class="add-task">
                            <?php } ?>
                            <div class="form-group">
                                <label for="task-name ">Write the task name</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                    <input type="text" class="form-control" name="task_name" id="Taskname" value="<?= isset($task_data['task_name'])?$task_data['task_name']:'' ?>">
                                <?php } else { ?>
                                    <input type="text" class="form-control" name="task_name" id="Taskname">
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="description">Write a small description</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                    <textarea class="form-control" id="description" name="task_desc" rows="4" value="<?= isset($task_data['description'])?$task_data['description']:'' ?>"><?= isset($task_data['description'])?$task_data['description']:'' ?></textarea>
                                <?php } else { ?>
                                    <textarea class="form-control" id="description" name="task_desc" rows="4"></textarea> <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="choose-project">Choose a project</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                    <select type="number" class="form-control project_name" id="choose-project" name="project">
                                        <option value=<?php echo isset($task_data['project_id'])?$task_data['project_id']:'' ?>>
                                            <?= isset($task_data['project_name'])?$task_data['project_name']:''; ?>
                                        </option>
                                        <?php
                                        foreach ($project_list as $project) {
                                            if(($project['name'] != $task_data['project_name'])) {
                                            ?>
                                            <option value=<?php echo $project['id'] ?>>
                                                <?php echo $project['name']; ?>
                                            </option>
                                        <?php } } ?>
                                    </select>
                                <?php } else { ?>
                                    <select type="text" class="form-control project_name" id="choose-project" name="project" value="<?=isset($task_data['project_name'])?$task_data['project_name']:''?>">
                                        <option>Select Project</option>
                                        <?php
                                        foreach ($result as $p) { ?>
                                            <option value=<?php echo $p['id'] ?>>
                                                <?php echo $p['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="choose-module">Choose project module</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                <select type="text"  class="form-control" id="choose-module" name="project_module">
                                    <?php if(isset($project_module_list)){
                                        foreach($project_module_list AS $module){ ?>
                                        <?php if($task_data['module_id'] == $module->id){ ?>
                                            <option value="<?=$module->id;?>" selected><?=$module->name;?></option>
                                        <?php  }else{ ?>
                                            <option value="<?=$module->id;?>"><?=$module->name;?></option>
                                    <?php } ?>
                                    <!-- <option value=<?=$task_data['module_name']?>><?=$task_data['module_name']?></option> -->
                                <?php } ?></select>
                                <?php } }else { ?>
                                    <select type="text" class="form-control" id="choose-module" name="project_module" value="<?= $task_data['module_name'] ?>">
                                        <option>Select module</option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            
                            <div id="task-times">
                            <div class="row pt-4">
                                <div class="col-6 text-left">
                                    <p class="display-5"><strong>Timeline</strong></p>
                                </div>
                                <div class="col-6 text-right space_right">
                                    <a href="javascript:void(0);" class="add-new-time" data-tooltip="tooltip" id="add-new-time">
                                        <i class="fas fa-plus pt-2 icon-plus"></i>
                                    </a>
                                </div>
                                <div class="col-12">
                                    <p id="taskError" class="text-danger"></p>
                                </div>
                            </div><hr>
                                <div id="show_list">
                                    <div class="row">
                                        <div class="col-4 col-md-5">
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
                                <?php if ($this->input->get('t_id')) { ?>
                                    <input type="hidden" id="task-len" value="<?= isset($timeline_data)?sizeof($timeline_data):'' ?>">
                                    <!-- Add time: EDIT case  -->
                                    <div id="task-add-time">
                                        <div class="primary-wrap">
                                            <?php $tnum = 0;
                                            if(isset($timeline_data)){
                                            foreach ($timeline_data as $key => $task) {
                                            ?>
                                                <div class="time-section pt-3 pb-3">
                                                    <div class="row">
                                                        <div class="col-4 col-md-5">
                                                            <div class="input-group date mb-3">
                                                                <input type="text" class="date-utc form-control datepicker pl-3" id="date-picker-<?= ($key+1) ?>" name="time[<?= ($key+1) ?>][date]" data-date-format="yyyy-mm-dd" value="<?= $task['task_date']; ?>">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <button type="button" class="btn fa fa-calendar p-0"></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group">
                                                                <!-- converting utc start time to local time -->

                                                                <?php
                                                                    if($task['start_time'])
                                                                        $start = date('H:i',strtotime($task['start_time']));
                                                                    else
                                                                        $start = '';
                                                                ?>
                                                                <input type="text" class="date-utc timepicker-<?= $tnum ?> form-control" id="start-time-<?= ($key+1) ?>" name="time[<?= ($key+1) ?>][start]" placeholder="hh:mm" value="<?= $start ?>">

                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group">
                                                                <!-- converting utc end time to local time -->

                                                                <?php
                                                                    if($task['end_time'])
                                                                        $end = date('H:i',strtotime($task['end_time']));
                                                                    else
                                                                        $end = '';
                                                                ?>
                                                                <input type="text" class="date-utc form-control timepicker-<?= ($tnum + 1) ?>" id="end-time-<?= ($key+1) ?>" name="time[<?= ($key+1) ?>][end]" value="<?= $end ?>" placeholder="hh:mm">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-11 col-10 text-center mb-3">
                                                            <input type="text" class="form-control" id="description-<?= ($key+1) ?>" name="time[<?= ($key+1) ?>][task_description]" value="<?= $task['task_description']; ?>" placeholder="Description">
                                                            <input type="hidden" value="<?= $task['table_id']; ?>" name="time[<?= ($key+1) ?>][table_id]" id="table_id<?= ($key+1) ?>">
                                                        </div>
                                                        <div class="col-md-1 col-2 text-center mb-3">
                                                            <a href="javascript:void(0);" class="ml-0 delete-task" id="delete-task-<?= $key+1 ?>">
                                                                <i class="fas fa-minus text-white pt-2 icon-plus" name="time[<?= ($key+1) ?>][deleted_time_range]">
                                                                    <input type="hidden" value="<?= $task['table_id']; ?>" name="time[<?= ($key+1) ?>][table_id]" id="table_id<?= ($key+1) ?>">
                                                                </i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $tnum = $tnum + 2;
                                            } }?>
                                        </div>
                                        <?php if ($this->input->get('t_id')) { ?>
                                            <div class="text-right pr-2">
                                                <!-- <a href="javascript:void(0);" class="ml-0 add-timeline" id="add-new-time">
                                                    <i class="fas fa-plus icon-plus"></i>
                                                </a> -->
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!-- END: Add time  -->
                                <?php
                                } else { ?>
                                    <!-- Add time: New Case -->
                                    <div id="task-add-time">
                                        <div class="primary-wrap">
                                            <div class="row remove-first-timeline">
                                                <div class="col-4 col-md-5">
                                                    <div class="input-group date mb-3">
                                                        <input type="text" class="date-utc form-control datepicker pl-3" name="time[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-0">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <button type="button" class="btn fa fa-calendar datepicker-icon p-0"></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group">
                                                        <input id="start-time-0" class="date-utc form-control timepicker-a" name="time[0][start]" placeholder="hh:mm" />
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group">
                                                        <input id="end-time-0" class="date-utc form-control timepicker-b" name="time[0][end]" placeholder="hh:mm" />
                                                    </div>
                                                </div>
                                                <div class="col-10 col-md-11 text-center">
                                                    <input id="description-0" class="form-control" name="time[0][task_description]" placeholder="description" />
                                                </div>

                                                <div class="col-2 col-md-1 text-center">
                                                    <a href="javascript:void(0);"  class="ml-0 remove-timeline" id="remove-time-0">
                                                        <i class="fas fa-minus pt-2 icon-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Add time  -->
                                <?php } ?>
                            </div>
                            <p>&nbsp;</p>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary save-task" id="save-tasks">Save Task</button><!-- to store the task entry. -->
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <footer class="footer">
                    <p class="text-center ">Copyright © 2020 Printgreener.com</p>
                </footer>
            </div>

            <div class="modal fade" id="alert_for_delete" tabindex="-1" role="dialog" aria-labelledby="alert_for_deleteLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="alert_for_deleteLabel">Delete confirmation</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this timeline?</p>
                            <h6 class="text-muted font-weight-light">This action can't be undone.</h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary col-6" id= "alert_for_delete_true">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
