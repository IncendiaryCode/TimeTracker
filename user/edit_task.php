<?php
$GLOBALS['page_title'] = 'Edit Task';
include("header.php");
include("../php/activity.php");
foreach($project_data as $data){

}
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-6 offset-3">
                    <form action="<?=BASE_URL?>php/save_task.php" method="post" id="addTask" class="mt-5 ">
                        <div class="form-group">
                            <label for="task-name ">Write the task name</label>

                            <input type="text" class="form-control" name="task-name" id="Taskname" value="<?=$data['task_name'];?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select type="number" class="form-control" id="chooseProject" name="chooseProject">
                                <option selected><?=$data['name'];?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="started-date">Started on</label>
                            <input type="datetime-local" class="form-control" name="startedDate" value="<?=$data['t_date'];?>">
                        </div>
                        <div class="form-group">
                            <label for="ended-date">Ended on</label>
                            <input type="datetime-local" class="form-control" id="ended" name="endedDate" value="<?=$data['end_time'];?>">
                        </div>
                        <p id="taskError" class=" text-danger"></p>
                        <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
                        <p id="taskError" class=" text-danger"></p>
                        <button type="submit" class="save-task">Save Task</button>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <footer>
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
<?php include("footer.php"); ?>
