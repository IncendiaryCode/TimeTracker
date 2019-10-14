<?php
  include("header.php");
  if(!isset($_SESSION['user'])){
      header("location:index.php");
      die();
  }
  
  //print_r($_SESSION);exit();
?>
<body>
    <header class="container-fluid main-header">
        <div class="row padding-around">
            <div class="col-6 time-tracker">
                <img src="<?=BASE_URL?>images/logo-white.png" height="50px">
            </div>
            <div class="col-5 text-right ">
                <form method="post" action="add_task.php">
                    <button id="new-task" type="submit" onclick="timeUpdate()"><i class="fas fa-plus icon-White "></i>
                        <span class="time-tracker "> Task</span></button>
                </form>
            </div>
            
                <div class="col-1 text-left" id="append">
                <!-- to chage image -->
                <h2><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#changeProfile" data-toggle="tooltip" data-placement="top" title="User Profile" onclick="timeUpdate()"></i></h2>
           </div>
        </div>
    </header>
    <div>
        <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
        <p class="font-weight-light time-font text-center" id="login-time">Started at 9:00AM</p>
        <p class=" font-weight-light text-center" id="timeUsed">
            <label id="hours">00</label>:<label id="minutes">00</label>:<label id="seconds">00</label>
        </p>
        <p class="font-weight-light text-center" id="taskName">Punch in/out</p>
    </div>
    <main class="container-fluid-main">
        <div class="  md main-container-employee">
            <div class="text-center main-container-inner topWidth" id="stopTime" onclick="pause()">
                <h3><i class=" row fas fa-stop "></i><i class=" row fas fa-play "></i></h3>
            </div>
            <div class="container sufee-alert font-weight-light alert with-close alert-dark fade show p-4">
                <i class=" text-danger  fas fa-exclamation-triangle"></i>
                As task "Create login API for mobile" has not been ended.
                <a href="#" class="forgot-color"> Stop now!
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
                            <a class="dropdown-item" href="home.php" onclick="sortByTaskName();">Task name</a>
                            <a class="dropdown-item" href="home.php" onclick="sortByDate();">Date</a>
                        </div>
                    </div>
                </div>
                <div class='row mb-5 attach-card'>
                </div>
                <hr>
                <footer>
                    <p class="text-center p-3 ">© 2019 Printgreener.com</p>
                </footer>
            </div>
        </div>
        <div class="modal  fade " id="changeProfile" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="false" data-backdrop="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-header bg-white">
                    <button type="button" class="close text-right" data-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-content modal-width">
                    <div class="container-fluid-main">
                        <div class="  md main-container-employee text-center">
                            <img id="new_img" src="<?=BASE_URL?>images/user_profiles/<?=$_SESSION['user_image'];?>" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeimage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
                            <h5 class="text-center mt-4 font-weight-light"><?php echo $_SESSION['user_name'];?></h5>
                            <div class="container">
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal"><a href="<?BASE_URL?>user/employee_profile.php" class="text-dark">Profile</a></h3>
                                </div>
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal"><a href="<?BASE_URL?>user/employee_activities.php" class="text-dark">Login Activities</a></h3>
                                </div>
                                <div class="row">
                                    <h3 class="hr pt-4 font-weight-normal"><a href="<?BASE_URL?>index.php" class="text-dark" onclick="logout()">Logout</a></h3>
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
                        <form id="uploadImage" method="post" action="<?BASE_URL?>php/upload_profile.php" enctype="multipart/form-data">
                            <p><input type="file" name="change_img" placeholder="Upload image" id="image"></p>
                            <p class="text-danger" id="imageerror"></p>
                            <button type="submit" class="btn save-task submitProfile">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>