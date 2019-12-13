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
<p class="display-4 m-3 text-primary text-center">User details</p>
<div class="container">
    <div class="text-right mt-5">
        <select class="project-names" id="project-list">
        </select>
    </div> 
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <canvas id="user-chart"></canvas>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-2">
            <p><strong>User name</strong></p>
            </div>
        <div class="col-2">
            <p><strong>Project name</strong></p>
        </div>
        <div class="col-8">
            <p><strong>Task details</strong></p>
        </div>
    </div>
    <hr>
    <div class="row">
                <?php 
                $user = " ";
                $project = " ";
                    for($i=0; $i<sizeof($data); $i++){
                    ?><div class="col-2">
                        <div id="display-username">
                        <div><?php
                        if($user == $data[$i]['user_name'])
                        {
                            ?><div class="col-2"></div><?php 
                        }
                        else
                        {
                        $user = $data[$i]['user_name'];
                        ?><p><?=$user?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-2">
            <div>
            <?php
            if(($project == $data[$i]['project']) && ($user == $data[$i-1]['user_name']))
                {   
                    ?><div class="col-2"></div><?php 
                }
                else
                {
                $project = $data[$i]['project'];
                ?><p><?=$project;?></p><?php
                }?>
            </div>
        </div>
        <div class="col-8">
            <div class="row">
                <?php
                    $task = $data[$i]['task'];
                     ?>
                <div class="col-6 pb-4"><strong><u>Task Name</u>:  <?=$task['task_name'];?></strong></div>
                <div class="col-6 pb-4"><strong><u>Timer taken</u>:  <?=$task['total_minutes'];?> minutes</strong></div>
                <div class="col-6 "><strong>Start time</strong></div>
                <div class="col-6 "><strong>End time</strong></div>
                
                <div class="col-6"><?=$task[0]['start_time'];?></div>
                <div class="col-6"><?=$task[0]['end_time'];?></div>
            </div>
            <hr>
        </div>
        <?php } ?>
    </div>
    
</div>
<hr>
<footer class="">
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>
