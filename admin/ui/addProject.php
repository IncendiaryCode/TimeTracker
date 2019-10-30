<?php include("header.php"); ?>
<body>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <h1 class="text-center m-4">Add Project</h1>
                <div class="container pt-5">
                        <form action="../php/add_task.php" id="addProject">
                            <div class="form-group mt-3 row">
                                <label for="projectNname" class="col-md-3">Enter the Project name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control col-md-8" name="projectNname" id="project-name">
                            </div> 
                           
                            <div class="form-group mt-5 row">
                            <label for="project-logo" class="col-md-3">Choose a project logo<span class="text-danger">*</span></label>
                                <input type="file" class="form-control col-md-8" id="project-logo" name="projectLogo">
                            </div>

                            <div class="form-group mt-5 row">
                                <label for="projectColor" class="col-md-3">Select color for the project<span class="text-danger">*</span></label>
                                <input type="color" class="form-control col-md-8" name="projectColor" id="project-color">
                            </div> 
                            <div class="form-group mt-5 row">
                                <label for="projectStart" class="col-md-3">Enter start date for the project</label>
                                <input type="text" class="form-control datetimepicker col-md-8" name="projectStart" id="Project-start">
                            </div>
                            <div class="form-group mt-5 row">
                                <label for="projectEnd" class="col-md-3">Enter end date for the project</label>
                                <input type="text" class="form-control datetimepicker col-md-8" name="projectEnd" id="project-end">
                            </div>

                            <div class="text-right">
                            <p id="projectError" class=" text-danger"></p>
                               <button type="submit" class="btn btn-primary save-task">Add Project</button>
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