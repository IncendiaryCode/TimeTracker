<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$profile = $this->session->userdata('user_profile');
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link pr-4">
                        <div class="dropdown" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <i class="far fa fa-qrcode"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <table class="table table-hover">
                                        <thead>Options</thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row"><a href="#">Assign Task</a></th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row"><a href="#" onclick=window.location.href="adminOptions.html">See all </a></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <img src="<?=base_url();?>assets/images/<?=$profile;?>" height="40px" class="rounded-circle">
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <p><a href="#" onclick='window.location.href="adminProfile.php"' class="text-display">Profile</a></p>
                                    <p><a href="#" onclick='window.location.href="../php/logout.php"' class="text-display"><i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
            <div class="au-card-title">
                <div class="bg-overlay bg-overlay--blue"></div>
                <h3>
                    <i class="zmdi zmdi-account-calendar"></i>25 sep, 2019</h3>
            </div>
            <div class="au-task js-list-load">
                <div class="au-task__title">
                    <p>All notifications</p>
                </div>
                <div class="au-task-list js-scrollbar3">
                    <div class="au-task__item au-task__item--danger">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Meeting about plan for Admin Template 2018</a>
                            </h5>
                            <span class="time">10:00 AM</span>
                        </div>
                    </div>
                    <div class="au-task__item au-task__item--warning">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Create new task for Dashboard</a>
                            </h5>
                            <span class="time">11:00 AM</span>
                        </div>
                    </div>
                    <div class="au-task__item au-task__item--primary">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Meeting about plan for Admin Template 2018</a>
                            </h5>
                            <span class="time">02:00 PM</span>
                        </div>
                    </div>
                    <div class="au-task__item au-task__item--success">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Create new task for Dashboard</a>
                            </h5>
                            <span class="time">03:30 PM</span>
                        </div>
                    </div>
                    <div class="au-task__item au-task__item--danger js-load-item">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Meeting about plan for Admin Template 2018</a>
                            </h5>
                            <span class="time">10:00 AM</span>
                        </div>
                    </div>
                    <div class="au-task__item au-task__item--warning js-load-item">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#">Create new task for Dashboard</a>
                            </h5>
                            <span class="time">11:00 AM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright ©  <?=date("Y") ?> | TimeTracker.com</ p>
    </footer>