<?php
    include("header.php");
    include("../php/get_number.php");
?>
<body>
    <main class="container-fluid container-fluid-main">
        <div class="container-fluid">
            <div class="main-container-inner">
                <h1 class="text-center m-4">Assign Task</h1>
                <div class="container mt-5">
                        <form action="../php/add_task.php" id="addTask" method="post">
                            <div class="form-group mt-3 row">
                                <label for="user-name" class="col-md-3">Enter the name of user<span class="text-danger">*</span></label>
                                <input type="text" class="form-control col-md-9" name="user-name" id="Username">
                            </div>
                            <div class="form-group row mt-5">
                                <label for="task-name" class="col-md-3">Enter the Task name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control col-md-9" id="task_name" name="task_name">
                            </div>
                            <div class="form-group row mt-5">
                                <label for="description" class="col-md-3">Write a small description</label>
                                <textarea class="form-control col-md-9" rows="4" id="description" name="description" ></textarea>
                            </div>
                            <div class="form-group row mt-5">
                                <label for="choose-project" class="col-md-3">Choose a project<span class="text-danger">*</span></label>
                                <select class="form-control col-md-9" id="chooseProject" name="chooseProject">
                                <option>Select Project</option>
                                <?php 
                                    foreach($fetch_proj_name as $p){ ?>
                                    <option><?=$p['name']; ?></option>
                                <?php } ?>
                            </select>
                            </div>
                            <p id="taskError" class=" text-danger"></p>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary save-task">Assign Task</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
<?php include("footer.php"); ?>