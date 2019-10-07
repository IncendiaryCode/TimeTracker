<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        TimeTracker
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>

<body>
    <header class="container-fluid main-header">
        <div class="row padding-around">
            <div class="col-6 time-tracker">
                <img src="../images/logo-white.png" height="50px">
            </div>
            <div class="col-5 text-right ">
                <form method="get" action="addTask.html">
                    <button id="new-task" type="submit" onclick="timeUpdate()"><i class="fas fa-plus icon-White "></i>
                        <span class="time-tracker"> Task</span></button>
                </form>
            </div>
            <div class="col-1 text-left" id="append">
                <!-- to chage image -->
                <h2><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#changeProfile" data-toggle="tooltip" data-placement="top" title="User Profile" onclick="timeUpdate()"></i></h2>
            </div>
        </div>
    </header>
    <div>
        <p class="font-weight-light time-font text-center" id="login-time">Started at 9:00AM</p>
        <p class=" font-weight-light text-center" id="timeUsed">
            <label id="hours">00</label>:<label id="minutes">00</label>:<label id="seconds">00</label>
        </p>
        <p class="font-weight-light text-center" id="taskName">Punch in/out</p>
    </div>
    <main class="container-fluid-main">
        <div class="  md main-container-employee">
            <div class="text-center  main-container-inner topWidth" id="stopTime" onclick="pause()">
                <h3><i class=" row fas fa-stop "></i> <i class=" row fas fa-play "></i> </h3>
            </div>
            <div class="container sufee-alert font-weight-light alert with-close alert-dark fade show p-4">
                <i class=" text-danger  fas fa-exclamation-triangle"></i>
                As task "Create login API for mobile" has not been ended.
                <a href="#" class="forgot-color" onclick="pauseTime()"> Stop now!
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="container">
                <div class=" row mb-3">
                    <div class="col-6">
                        <h4 class="font-weight-light text-left pt-5">Recent Activites</h4>
                    </div>
                    <div class="col-6 dropdown text-right">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sort by
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#" onclick="sortByTaskName();">Task name</a>
                            <a class="dropdown-item" href="#" onclick="sortByDate();">Created date</a>
                        </div>
                    </div>
                </div>
                <div class='row mb-5 attach-card'>
                </div>
                <hr>
                <footer>
                    <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
                </footer>
            </div>
        </div>
        <div class="modal  fade " id="changeProfile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="fasle" data-backdrop="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-header bg-white">
                    <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-content modal-width">
                    <div class="container-fluid-main">
                        <div class="  md main-container-employee text-center">
                            <img src="../images/images.png" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeimage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
                            <h5 class="text-center mt-4 font-weight-light">John Doe</h5>
                            <div class="container">
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal"><a href="employeeProfile.html" class="text-dark">Profile</a></h3>
                                </div>
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal"><a href="employeeActivities.html" class="text-dark">Login Activities</a></h3>
                                </div>
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal" onclick="logout()"><a href="../index.html" class="text-dark">Logout</a></h3>
                                </div>
                                <div class="row hr"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="changeimage">
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
        <!-- <div class='modal-content modal fade' id='newModal' role="dialog">
            <div class='modal-header '>Upload image<button type='button' class='close text-danger' data-dismiss='modal'>×</button></div>
            <div class='modal-body'>
                <form id='uploadImage'>
                    <p><input type='file' name='change image' placeholder='Upload image' id='image'></p>
                    <p class='text-danger' id='imageErr'></p><button type='submit' class='btn save-task'>Upload</button>
                </form>
            </div>
        </div> -->
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="../javascript/employeeInfo.js"></script>
    <script src="../javascript/addTask.js"></script>
</body>

</html>