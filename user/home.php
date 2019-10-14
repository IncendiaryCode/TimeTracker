<?php
include("header.php");
if(!isset($_SESSION['user'])){
      header("location:../index.php");
      die();
  }
include("../php/activity.php");
?>
<div>
    <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
    <p class="font-weight-light time-font text-center" id="login-time">Started at 9:00AM</p>
    <p class=" font-weight-light text-center" id="timeUsed">
        <label id="hours">00</label>:<label id="minutes">00</label>:<label id="seconds">00</label>
    </p>
    <p class="font-weight-light text-center" id="taskName">Punch in/out</p>
</div>
<main class="container-fluid-main">
    <div class="md main-container-employee">
        <div class="text-center main-container-inner topWidth" id="stopTime" onclick="pause()">
            <h3><i class="row fas fa-stop "></i><i class=" row fas fa-play "></i></h3>
        </div>        
        <div class="container">
            <div class="sufee-alert font-weight-light alert with-close alert-dark fade show p-4">
                <i class="text-danger  fas fa-exclamation-triangle"></i>
                A task "Create login API for mobile" has not been ended.
                <a href="#" class="forgot-color"> Stop now!
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="row mb-3 pt-4">
                <div class="col-6">
                    <h4 class="font-weight-light text-left ">Recent Activites</h4>
                </div>
                <div class="col-6">
                    <div class="dropdown text-right" id="dropdown-recent-acts">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sort by
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                            <a class="dropdown-item" href="#" data-type="task_asc">Task name</a>
                            <a class="dropdown-item" href="#" data-type="date_asc">Created date</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row mb-5' id="attach-card">
                <div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>
            </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">© 2019 Printgreener.com</p>
            </footer>
        </div>
    </div>
</main>
<?php include("footer.php"); ?>
