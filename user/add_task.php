<?php
$GLOBALS['page_title'] = 'Add Task';
include("header.php");
include("../php/activity.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row">
                <div class="col-8 offset-2">
                    <form action="<?=BASE_URL?>php/save_task.php" method="post" id="addTask" class="mt-5 ">
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
                                <?php foreach($project_names as $p){ ?>
                                <option>
                                    <?=$p['name']; ?>
                                </option>
                                <?php } ?>
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
                        <div id="Checked">
                            <!-- <div class="form-group">
                                <label for="start_date">Started on</label>
                                <div class="input-group">
                                  <input type="text" class="form-control datetimepicker" id="started-date" name="start_date" aria-describedby="date-start">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="end-date">Ended on</label>
                                <div class="input-group">
                                  <input type="text" class="form-control datetimepicker" id="end-date" name="end_date" aria-describedby="date-end">
                                </div>
                            </div> -->
                            <div class="page-wrapper">
                                <div class="user-data m-b-30">
                                    <div class="table-data">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>Date</td>
                                                    <td>Start time</td>
                                                    <td>End time</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody id="show_list">
                                            </tbody>
                                        </table>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="table-data__info">Date<span class="text-danger">*</span>
                                                            <div class='input-group date'>
                                                                <input class="form-control-file p-1 border-top-0 border-left-0 border-right-0 border-dark" data-date-format="dd/mm/yyyy" id="date-picker">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-time"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-5">Start time<span class="text-danger">*</span>
                                                        <div class="input-group date">
                                                            <input id="timepicker1" class="form-control-file border-top-0 border-left-0 border-right-0 rounded-0 border-dark" />
                                                        </div>
                                                    </td>
                                                    <td class="pl-5">End time<span class="text-danger">*</span>
                                                        <div class="input-group date">
                                                            <input id="timepicker2" class="form-control-file border-top-0 border-left-0 border-right-0 rounded-0 border-dark" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="more p-4 m-4">
                                                            <i class="fas fa-plus" onclick="__store_timings()" data-toggle="tooltip" data-placement="top" title="add"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="text-center text-danger">
                                            <p id="datetime-error"></p>
                                        </div>
                                    </div>
                                </div>
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
        <footer class="footer">
            <hr>
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
<?php include("footer.php"); ?>