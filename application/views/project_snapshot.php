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
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display">1. Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display">2. <i class="fas fa-power-off"></i> Logout</a></p>
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
            <div class="col-2"> 
                <p><strong>Project name</strong></p>
                </div>
            <div class="col-2">
                <p><strong>Module name</strong></p>
            </div>
            <div class="col-8">
                <p><strong>Task details</strong></p>
            </div>
        </div>
        <hr>
        <div class="row">
               <?php
               $project = " ";
               $module = " ";
                for($i=0; $i<sizeof($data); $i++){
                    ?> <div class="col-2">
                        <div id="display-uname">
                            <div>
                    <?php 
                    if($project == $data[$i]['project'])
                    {   

                        ?><div class="col-2"></div><?php 
                    }
                    else
                    {
                $project = $data[$i]['project'];
                ?><div class="min-height"><p><?=$project;?></p>
                <p>Total users: (total_users)</p>
                <p>Project started:(start_time) </p>
                <p>Total time:(total_time) </p></div>
            <?php }
            ?>
            </div>
        </div>
    </div>
        <div class="col-2">
            <div>
                <?php 
                    if(($module == $data[$i]['module']) && ($project == $data[$i-1]['project']))
                        {
                            ?><div class="col-2"></div><?php 
                        }
                        else
                        {
                    $module = $data[$i]['module'];
                    ?><p class="min-height"><?=$module;?></p><?php
                    }
                    ?>
                
            </div>
        </div>
        <div class="col-8">
                    <?php $task = $data[$i]['task']; ?>
            <div class="row">

                <div class="col-6 pb-4"><u>Task name</u>: <?=$task['task_name'];?></div>
                <div class="col-6 pb-4"><u>Time taken</u>: <?=$task['total_minutes'];?> minutes</div>
                <div class="col-6 pb-4"><u>Working users</u></div>
                <div class="col-6 pb-4"><u>Time taken</u></div>
                
            </div>
            <hr>
        </div>
        <?php }  ?>
    <hr>
    </div>
    
</div>
</form>
<hr>
<footer class="">
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>
