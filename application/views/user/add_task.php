<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
                        <div class="alert alert-danger">
                            <?php echo validation_errors();
                            echo (!empty($this->session->flashdata('failure'))) ? $this->session->flashdata('failure') : '';
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('success'))) { ?>
                        <div class="alert alert-success mb-5">
                            <?php echo (!empty($this->session->flashdata('success'))) ? $this->session->flashdata('success') : ''; ?>
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
                                    <select type="number" class="form-control project_name" id="choose-project" name="project" value="<?= $task_data['project_name'] ?>">
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
                                <select type="number" class="form-control project_name" id="choose-module" name="project_module" value="<?= $task_data['module_name'] ?>">
                                    <?php if ($this->input->get('t_id')) { ?>
                                    <option value=<?=$task_data['module_id']?>><?= $task_data['module_name'] ?></option>
                                    <?php } else { ?>
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
                                                                <input type="text" class="check-for-utc form-control datepicker" id="date-picker-<?= $key ?>" name="time[<?= $key ?>][date]" data-date-format="yyyy-mm-dd" value="<?= $task['task_date']; ?>">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <button type="button" class="btn fa fa-calendar p-0"></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group date">  <!-- converting utc start time to local time -->
                                                                <?php 
                                                                    $offset_mins = ((date('Z'))/60);
                                                                    $local_time_mins = (substr($task['start_time'],0,2)*60)+(substr( $task['start_time'],3,2))+$offset_mins;
                                                                    $local_time_hr;
                                                                    $local_time_min;
                                                                    if(strlen(round(($local_time_mins/60),0)) == 1)
                                                                    {
                                                                        $local_time_hr = '0'.round(($local_time_mins/60-1),0);
                                                                    }else
                                                                    {
                                                                        $local_time_hr = round(($local_time_mins / 60-1));
                                                                    }
                                                                    if(strlen($local_time_mins%60) == 1)
                                                                    {
                                                                        $local_time_min = '0'.($local_time_mins%60);
                                                                    }else
                                                                    {
                                                                        $local_time_min = ($local_time_mins%60);
                                                                    }
                                                                    $local_start_time = $local_time_hr . ':' . $local_time_min;
                                                                ?>
                                                                <input type="text" class=" check-for-utc timepicker-<?= $tnum ?> form-control" id="start-time-<?= $key ?>" name="time[<?= $key ?>][start]" value="<?= $task['start_time'] ?>" placeholder="hh:mm">
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-3 mb-3">
                                                            <div class="input-group date">  <!-- converting utc end time to local time -->
                                                                <?php 
                                                                if($task['end_time'] != '')
                                                                {
                                                                    $offset_min = ((date('Z')-30)/60);
                                                                    $local_time_min = (substr($task['end_time'],0,2)*60)+(substr($task['end_time'],3,2))+$offset_min;
                                                                    $local_time_hr;
                                                                    $local_time_min;
                                                                    if(strlen(round(($local_time_min/60),0)) == 1)
                                                                    {
                                                                        $local_time_hr = '0'.round(($local_time_min/60),0);
                                                                    }else
                                                                    {
                                                                        $local_time_hr = round(($local_time_min / 60));
                                                                    }
                                                                    if(strlen($local_time_min%60) == 1)
                                                                    {
                                                                        $local_time_min = '0'.($local_time_min%60+1);
                                                                    }else
                                                                    {
                                                                        $local_time_min = ($local_time_min%60+1);
                                                                    }
                                                                    $local_end_time = $local_time_hr . ':' . $local_time_min;
                                                                    }
                                                                ?>
                                                                <input type="text" class=" check-for-utc form-control timepicker-<?= $tnum + 1 ?>" id="end-time-<?= $key ?>" name="time[<?= $key ?>][end]" value="<?= $task['end_time'] ?>" placeholder="hh:mm">
                                                            </div>
                                                        </div>
                                                        <div class="col-10 text-center mb-3">
                                                            <input type="text" class="form-control" id="description-<?= $key ?>" name="time[<?= $key ?>][task_description]" value="<?= $task['task_description']; ?>" placeholder="Description">
                                                        </div>
                                                        <div class="col-2 text-right mb-3">
                                                            <a href="javascript:void(0);" id="delete-task-<?= $key ?>" class="delete-task">
                                                                <i class="fas fa-trash text-white pt-2 icon-plus" name="time[<?= $key ?>][deleted_time_range]">
                                                                    <input type="hidden" value="<?= $task['table_id']; ?>" name="time[<?= $key ?>][table_id]" id="table_id<?= $key ?>">
                                                                </i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($key == sizeof($timeline_data) - 1) { ?>
                                                    <div class="row">
                                                        <div class="col-12 text-right">
                                                            <hr />
                                                            <a href="javascript:void(0);" id="add-new-time" title="Add">
                                                                <i class="fas fa-plus pt-2 icon-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php $tnum = $tnum + 2;
                                            } ?>
                                        </div>
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
                                                        <input type="text" class="form-control datepicker " name="time[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-0">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <button type="button" class="btn fa fa-calendar p-0"></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group date">
                                                        <input id="start-time-0" class="check-for-utc form-control timepicker-a" name="time[0][start]" placeholder="hh:mm" />
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="input-group date">
                                                        <input id="end-time-0" class="check-for-utc form-control timepicker-b" name="time[0][end]" placeholder="hh:mm" />
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
                                <?php } ?>
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
            <p class="text-center ">Copyright © 2020 Printgreener.com</p>
        </footer>
    </div>
</main>