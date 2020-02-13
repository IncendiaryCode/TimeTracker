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
                    <?php
                    if (validation_errors()) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                             <?php echo validation_errors();
                             echo (!empty($this->session->flashdata('failure'))) ? $this->session->flashdata('failure') : ''; ?>
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
                                    <input type="text" class="form-control" name="task_name" id="Taskname" value="<?= $task_data['task_name'] ?>">
                                <?php } else { ?>
                                    <input type="text" class="form-control" name="task_name" id="Taskname">
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="description">Write a small description</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                    <textarea class="form-control" id="description" name="task_desc" rows="4" value="<?= $task_data['description'] ?>"><?= $task_data['description'] ?></textarea>
                                <?php } else { ?>
                                    <textarea class="form-control" id="description" name="task_desc" rows="4"></textarea> <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="choose-project">Choose a project</label>
                                <?php if ($this->input->get('t_id')) { ?>
                                    <select readonly="" type="number" class="form-control" id="choose-project" name="project">
                                        <option selected value=<?php echo $task_data['project_id'] ?>>
                                            <?= $task_data['project_name']; ?>
                                        </option>
                                    </select>
                                <?php } else { ?>
                                    <select type="text" class="form-control project_name" id="choose-project" name="project" value="<?=$task_data['project_name']?>">
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
                                    <?php foreach($project_module_list AS $module){ ?>
                                        <?php if($task_data['module_id'] == $module->id){ ?>
                                            <option value="<?=$module->id;?>" selected><?=$module->name;?></option>
                                        <?php  }else{ ?>
                                            <option value="<?=$module->id;?>"><?=$module->name;?></option>
                                    <?php } ?>
                                    <!-- <option value=<?=$task_data['module_name']?>><?=$task_data['module_name']?></option> -->
                                <?php } ?></select>
                                <?php } else { ?>
                                    <select type="text" class="form-control" id="choose-module" name="project_module" value="<?= $task_data['module_name'] ?>">
                                        <option>Select module</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <p class="display-5 pt-4"><strong>Timeline</strong></p>
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
                                <?php if ($this->input->get('t_id')) { ?>

                                    <input type="hidden" id="task-len" value="<?= sizeof($timeline_data) ?>">
                                    <!-- Add time: EDIT case  -->
                                    <div id="task-add-time">
                                        <div class="primary-wrap">
                                            <?php $tnum = 0;
                                            foreach ($timeline_data as $key => $task) {
                                            ?>
                                                <div class="time-section pt-3 pb-4">
                                                    <div class="row">
                                                        <div class="col-4 col-md-6">
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="date-utc form-control datepicker" id="date-picker-<?= $key ?>" name="time[<?= $key ?>][date]" data-date-format="yyyy-mm-dd" value="<?= $task['task_date']; ?>">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <button type="button" class="btn fa fa-calendar p-0"></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group date">
                                                                <!-- converting utc start time to local time -->

                                                                <?php
                                                                    if($task['start_time'])
                                                                        $start = date('H:i',strtotime($task['start_time']));
                                                                    else
                                                                        $start = '';
                                                                ?>
                                                                <input type="text" class="date-utc timepicker-<?= $tnum ?> form-control" id="start-time-<?= $key ?>" name="time[<?= $key ?>][start]" placeholder="hh:mm" value="<?= $start ?>">

                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group date">
                                                                <!-- converting utc end time to local time -->

                                                                <?php
                                                                    if($task['end_time'])
                                                                        $end = date('H:i',strtotime($task['end_time']));
                                                                    else
                                                                        $end = '';
                                                                ?>
                                                                <input type="text" class="date-utc form-control timepicker-<?= $tnum + 1 ?>" id="end-time-<?= $key ?>" name="time[<?= $key ?>][end]" value="<?= $end ?>" placeholder="hh:mm">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-11 col-10 text-center mb-3">
                                                            <input type="text" class="form-control" id="description-<?= $key ?>" name="time[<?= $key ?>][task_description]" value="<?= $task['task_description']; ?>" placeholder="Description">
                                                            <input type="hidden" value="<?= $task['table_id']; ?>" name="time[<?= $key ?>][table_id]" id="table_id<?= $key ?>">
                                                        </div>
                                                        <div class="col-md-1 col-2 text-center mb-3">
                                                            <a href="javascript:void(0);" class="ml-0 delete-task" id="delete-task-<?= $key ?>">
                                                                <i class="fas fa-minus text-white pt-2 icon-plus" name="time[<?= $key ?>][deleted_time_range]">
                                                                    <input type="hidden" value="<?= $task['table_id']; ?>" name="time[<?= $key ?>][table_id]" id="table_id<?= $key ?>">
                                                                </i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $tnum = $tnum + 2;
                                            } ?>
                                        </div>
                                        <?php if ($this->input->get('t_id')) { ?>
                                            <div class="text-right pr-2">
                                                <a href="javascript:void(0);" class="ml-0" id="add-new-time" title="Add">
                                                    <i class="fas fa-plus icon-plus"></i>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!-- END: Add time  -->
                                <?php
                                } else { ?>
                                    <!-- Add time: New Case -->
                                    <div id="task-add-time">
                                        <div class="primary-wrap">
                                            <div class="row">
                                                <div class="col-4 col-md-6">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="date-utc form-control datepicker " name="time[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-0">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <button type="button" class="btn fa fa-calendar p-0"></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group date">
                                                        <input id="start-time-0" class="date-utc form-control timepicker-a" name="time[0][start]" placeholder="hh:mm" />
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group date">
                                                        <input id="end-time-0" class="date-utc form-control timepicker-b" name="time[0][end]" placeholder="hh:mm" />
                                                    </div>
                                                </div>
                                                <div class="col-10 col-md-11 text-center">
                                                    <input id="description-0" class="form-control" name="time[0][task_description]" placeholder="description" />
                                                </div>

                                                <div class="col-2 col-md-1 text-center">
                                                    <a href="javascript:void(0);"  class="ml-0" id="add-new-time" title="Add">
                                                        <i class="fas fa-plus pt-2 icon-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Add time  -->
                                <?php } ?>
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
                    <p class="text-center ">Copyright © 2020 Printgreener.com</p>
                </footer>
            </div>
        </main>