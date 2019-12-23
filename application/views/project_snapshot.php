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
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display pl-2"> Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display pl-2"> <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- for Project snap_shot -->
    <p class="display-4 text-primary text-center">Project details</p>
        <div class="container">
            <div class="row mt-5">
                <div class="col-12">
                    <div id="chart_div" style=" height: 500px;"></div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-5">
                    <p><strong>Project name</strong></p>
                </div>
                <div class="col-5">
                    <p><strong>Users</strong></p>
                </div>
                <div class="col-2">
                    <p><strong>Time spent</strong></p>
                </div>
            </div>
            <hr>
                <?php foreach($data as $proj){ 
                    ?>
            <div class="row" style="min-height: 50px;">
                <div class="col-5">                
                    <a href="" class="">
                        <div class="mr-2">
                        <?php
                        if($proj['project_icon'] != ''){
                            $image = substr($proj['project_icon'],29);
                            ?>
                            <img src="<?=base_url().$image?>" width="30px;">
                            <?php
                        } ?>
                        <?=$proj['project_name']; ?>
                    </div>
                        
                    </a>
            </div>
            <div class="col-5">
                <p>Total users: <?=$proj['total_users'];?></p>
                 <?php foreach($proj['user_details'] as $user){  ?>     <!-- redirect to user detail page -->
                     <a href="<?=base_url();?>index.php/admin/load_userdetails_page?user_id=3" class="pt-2 mr-3 mt-2">
                        <?= $user['user_name']; ?>
                    </a>
                <?php  }?>
            </div>
            <div class="col-2"><?=round($proj['time_used']/60,2)?> hrs</div>
            
        </div><hr>
            <?php  } ?>
        </div>

    <hr>
    <footer>
        <p class="text-center">Copyright © 2019 Printgreener.com</p>
    </footer>
