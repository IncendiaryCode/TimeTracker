 <?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#" class="text-white previous"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    
                    <div class="nav-item nav-link pr-4">
                        <div class="dropdown" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#"><i class="far fa-bell"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <table class="table table-hover">
                                        <thead>You have 3 notificatoins</thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_notification"'>See all notifications</a></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#" class="text-white"><img src="<?=base_url().$picture?>" height="40px" class="rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <p><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display">Profile</a></p>
                                    <p><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display"><i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
<div class="container">
    <div class="row mt-5">
        <div class="col-4">
            <p><strong>User name</strong></p>
            </div>
        <div class="col-4">
            <p><strong>Project name</strong></p>
        </div>
        <div class="col-4 text-center">
            <p><strong>Task details</strong></p>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-4">
            <div id="display-name">
                <div class="mt-5">
                    <p>Vinay</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="mt-5">
                <p>Time Tracker</p>
            </div>
        </div>
        <div class="col-4 text-center">
            <div class="row">
                <div class="col-6 pb-4"><strong><u>Task name</u>:  Login page</strong></div>
                <div class="col-6 pb-4"><strong><u>Timer taken</u>:  8:00 hrs</strong></div>
                <div class="col-6 "><strong>Start time</strong></div>
                <div class="col-6 "><strong>End time</strong></div>
                <div class="col-6">04:30 AM</div>
                <div class="col-6">04:30 PM</div>
                <div class="col-6">04:30 AM</div>
                <div class="col-6">04:30 PM</div>
            </div>
        </div>
    </div>
</div>


<hr>
<footer class="admin-footer">
<hr>
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>