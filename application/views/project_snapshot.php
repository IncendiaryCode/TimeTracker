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
    <form method="post" action="#">
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-8 offset-md-2">
                    <canvas id="project-chart"></canvas>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-3">
                    <p><strong>Project name</strong></p>
                </div>
                <div class="col-7">
                    <p><strong>Task details</strong></p>
                </div>
            </div>
            <hr>
                    <?php foreach($data as $proj){ ?>
            <div class="row">
                <div class="col-3">
                    <div class="min-height">
                        <p><u>
                            <?=$proj['project'];?></u>
                        </p>
                        <p>Total users: (<?=$proj['total_users'];?>)</p>
                        <p>Total time:(<?=round(($proj['time_used']/60),2);?> hours)</p>
                    </div>
                    
                </div>
                <div class="col-7">
                    <?php foreach($proj['task_details'] as $d){ 
                         ?>
                    <div class="row">
                        <div class="col-6 pb-4"><u>Task name</u>:
                            <?=$d['task_name'];?>
                        </div>
                        <div class="col-6 pb-4"><u>Time taken</u>:
                            <?=round(($d['t_minutes']/60),2);?> hours</div>
                        <div class="col-6 pb-4"><u>Working users</u>: <?=$d['user_count'];?></div>
                    </div>
                    <hr>
                    <?php  }?>
                </div>
                <div class="col-2"><i class="fas fa-trash-alt icon-plus project-remove text-danger"></i></div>
            </div>
                <?php  } ?>
        </div>
    </form>
    <hr>
    <footer>
        <p class="text-center">Copyright © 2019 Printgreener.com</p>
    </footer>
