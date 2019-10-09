<?php
include("header.php");
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
        <div class="container pt-5">
            <div class=" row mb-3">
                <div class="col-6 space-left">
                    <h4 class="font-weight-light text-left ">Recent Activites</h4>
                </div>
                <div class="col-6  space-right dropdown text-right">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Sort by
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item"  onclick="sortByTaskName();">Task name</a>
                        <a class="dropdown-item"  onclick="sortByDate();">Date</a>
                    </div>
                </div>
            </div>
            <div class='row mb-5 attach-card'>
                <div class="d-flex align-items-center">
                  <strong>Loading...</strong></div>
                  <div class="spinner-border text-right ml-auto" role="status" aria-hidden="true">
                  </div>
                  <!-- ajax call -->
                  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js">
                    function loadDoc() {
                          var xhttp = new XMLHttpRequest();
                          xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                              console.log('error');
                              this.responseText;
                            }
                          };
                            console.log('success');
                          xhttp.open("GET", "<?=BASE_URL?>/php/activity.php", true);
                          xhttp.send();
                        }
                        loadDoc();
                  </script>

                <?php echo '<pre>'; print_r(get_activities('task')); ?>
            </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">© 2019 Printgreener.com</p>
            </footer>
        </div>
    </div>
</main>
<?php include("footer.php"); ?>