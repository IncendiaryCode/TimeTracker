<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
print_r($data)
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
    <div class="row mt-5">
        <div class="col-md-3 ">
            <div class="card user-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2 text-right">
                            <div class="mx-auto d-block">
                                <?php
                                if($data['profile'] != ''){
                                    $image = substr($data['profile'],29);
                                    ?>
                                    <img src="<?=base_url().$image?>" class="img-fluid">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-10 text-left">
                            <h3 class="text-sm-center mt-2 mb-1"><?=($data['user_name']) ?></h3>
                            <a href=""><?=($data['email']) ?></a>
                            <p><?=($data['phone']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="vl"></div>
        <div class="col-md-3 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div ><span class="display-3"><?=round($data['t_minutes']/60) ?></span><span class="display-5">h</span></div>
                    <p class="text-center">Time spent</p>
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-3 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <div class="text-center"><span class="display-3"><?=($data['project_count']) ?></span>
                            <p class="text-center">Active projects</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="">
    <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>