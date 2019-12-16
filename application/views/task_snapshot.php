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
<!-- UI for task snapshot -->
<p class="display-4 text-primary text-center">Task details</p>
<div class="container">
            <div class="text-right">
            <select class="project-name-list" id="total-project">
            </select>
        </div>
    <div class="row mt-5">
        <div class="col-md-6 offset-md-3">
            <canvas id="task-chart"></canvas>
            <p id="task-chart-error" class="text-center"></p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-2"> 
            <p><strong>Task name</strong></p>
            </div>
        <div class="col-2">
            <p><strong>Project name</strong></p>
        </div>
        <div class="col-8 ">
            <p ><strong>Task details</strong></p>
        </div>
    </div>
    <hr>

        <div class="row">
                    <?php 
                    $task_name = " ";
                    $project = " ";
                    $username = " ";
                    $count = 1;
                        for($i=0;$i<sizeof($data);$i++){
                            $count++;
                        ?><div class="col-2">
                            <div id="display-name">
                            <div><?php
                            $user = $data[$i]['task'];

                            if($task_name == $user['task_name'])
                            {
                                ?><div class="col-2"></div><?php 
                            }
                            else
                            {
                            ?><p><?=$user['task_name']?></p>
                        <?php
                        $username = $data[$i]['user_name'];
                         } ?>
                </div>
            </div>
        </div>
        <div class="col-2">
            <div>
                <?php
                if($task_name == $user['task_name'])
                    {   
                        ?><div class="col-2"></div><?php 
                    }
                    else
                    {
                    $task_name = $user['task_name'];
                    $project = $data[$i]['project'];
                    $count= 1;
                    ?><p><?=$project;?></p>

                    <?php

                    }
                    
                    ?>
            </div>
        </div>
        <div class="col-8">
            <div class="row">
                <?php
                    $task = $data[$i]['task']; ?>
                <div class="col-6 pb-4"><span><?=$count ?>.)  </span><u>User name</u>:  <?php echo $data[$i]['user_name']; $username = $data[$i]['user_name'];?></div>
                <div class="col-6 pb-4"><u>Timer taken</u>:  <?=$task['total_minutes'];?></div>
                <div class="col-6 ">Start time</div>
                <div class="col-6 ">End time</div>
           
                <div class="col-6"><?=$task[0]['start_time'];?></div>
                <div class="col-6"><?=$task[0]['end_time'];?></div>
            </div>
            <hr>
        </div>
                <?php  } ?>
    </div>



</div>
<!-- end of task snapshot -->
<footer>
<hr>
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>