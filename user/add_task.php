<?php
$GLOBALS['page_title'] = 'Add Task';
include("header.php");
include("../php/activity.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-6 offset-3">
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
                                <option><?=$p['name']; ?></option>
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
                            <div class="form-group">
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
                            </div>
                        </div>
                        <p id="taskError" class=" text-danger"></p>
                        <p>&nbsp;</p> 
                        <hr/>
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