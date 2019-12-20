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
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#" class="text-white"><img src="<?=base_url().$picture?>" height="40px" class="rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display"> Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display"> <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title mb-3">Profile Card</strong>
                </div>
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <img class="rounded-circle mx-auto d-block" src="images/icon/avatar-01.jpg" alt="Card image cap">
                        <h5 class="text-sm-center mt-2 mb-1">Steven Lee</h5>
                        <div class="location text-sm-center">
                            <i class="fa fa-map-marker"></i> California, United States</div>
                    </div>
                    <hr>
                    <div class="card-text text-sm-center">
                        <a href="#">
                            <i class="fa fa-facebook pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-twitter pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-linkedin pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-pinterest pr-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <img class="rounded-circle mx-auto d-block" src="images/icon/avatar-01.jpg" alt="Card image cap">
                        <h5 class="text-sm-center mt-2 mb-1">Steven Lee</h5>
                        <div class="location text-sm-center">
                            <i class="fa fa-map-marker"></i> California, United States</div>
                    </div>
                    <hr>
                    <div class="card-text text-sm-center">
                        <a href="#">
                            <i class="fa fa-facebook pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-twitter pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-linkedin pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-pinterest pr-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-footer">
                    <strong class="card-title mb-3">Profile Card</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-user"></i>
                    <strong class="card-title pl-2">Profile Card</strong>
                </div>
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <img class="rounded-circle mx-auto d-block" src="images/icon/avatar-01.jpg" alt="Card image cap">
                        <h5 class="text-sm-center mt-2 mb-1">Steven Lee</h5>
                        <div class="location text-sm-center">
                            <i class="fa fa-map-marker"></i> California, United States</div>
                    </div>
                    <hr>
                    <div class="card-text text-sm-center">
                        <a href="#">
                            <i class="fa fa-facebook pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-twitter pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-linkedin pr-1"></i>
                        </a>
                        <a href="#">
                            <i class="fa fa-pinterest pr-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="">
    <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>