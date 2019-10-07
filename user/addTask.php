<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TimeTracker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header class="container-fluid main-header">
        <div class="row">
            <div class="col-6 time-tracker">
                <img src="images/logo-white.png" height="40px" onclick="window.location.href='employeeInfo.php'">
            </div>
            <div class="col-6 text-right">
                <h2><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#changeProfile1" data-placement="top" title="User Profile"></i></h2>
            </div>
            <div class="col-12">
                <p class="display-4 pb-  text-center">Add Task</p>
            </div>
        </div>
    </header>
    <main class="container-fluid container-fluid-main">
        <div class="main-container">
            <div class="main-container-inner">
                <div class="row ">
                    <div class="col-6 offset-3">
                        <form action="employeeInfo.php" method="post" id="addTask" class="mt-5 ">
                            <p id="taskError" class=" text-danger"></p>
                            <div class="form-group  ">
                                <label for="task-name ">Write the task name</label>
                                <input type="text" class="form-control" name="task-name" id="Taskname">
                            </div>
                            <div class="form-group">
                                <label for="description">Write a small description</label>
                                <textarea class="form-control" id="description" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="choose-project">Choose a project</label>
                                <select type="number" class="form-control" id="chooseProject" name="chooseProject">
                                    <option>Select Project</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="started-date">Started on</label>
                                <input type="text" class="form-control" id="setCurrentDate" name="startedDate">
                            </div>
                            <div class="form-group">
                                <label for="ended-date">Ended on</label>
                                <input type="datetime-local" class="form-control" id="ended" name="endedDate">
                            </div>
                            
                            <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
                            <button type="submit" class="save-task">Save Task</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal  fade " id="changeProfile1" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="false">
                <div class="modal-dialog modal-xl">
                    <div class="modal-header bg-white">
                        <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-content modal-width">
                        <div class="container-fluid-main">
                            <div class="  md main-container-employee text-center">
                                <img src="images/images.png" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeimage1" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
                                <h5 class="text-center mt-4 font-weight-light">John Doe</h5>
                                <div class="container">
                                    <div class="row">
                                        <h3 class="hr pt-4 font-weight-normal"><a href="employeeProfile.php" class="text-dark">Profile</a></h3>
                                    </div>
                                    <div class="row">
                                        <h3 class="hr pt-4 font-weight-normal"><a href="employeeActivities.php" class="text-dark">Login Activities</a></h3>
                                    </div>
                                    <div class="row">
                                        <h3 class="hr pt-4 font-weight-normal" onclick="logout()"><a href="index.php" class="text-dark">Logout</a></h3>
                                    </div>
                                    <div class="row hr"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal" id="changeimage1" data-backdrop="false">
                <div class="modal-dialog animated fadeInDown">
                    <div class="modal-content text-center">
                        <div class="modal-header ">Upload image
                            <button type="button" class="close text-danger" data-dismiss="modal">×</button>
                        </div>
                        <div class="modal-body">
                            <form id="uploadImage">
                                <p><input type="file" name="change image" placeholder="Upload image" id="image"></p>
                                <p class="text-danger" id="imageerror"></p>
                                <button type="submit" class="btn save-task submitProfile">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <footer>
                <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
            </footer>
        </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="javascript/addTask.js"></script>
</body>

</html>