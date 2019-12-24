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
    <div class="row mt-5 shadow-sm">
        <?php foreach($data as $details) { ?>
        <div class="col-md-4 ">
            <div class="card user-card">
                <div class="card-body">
                            <div class="mx-auto d-block">
                               <div class="text-center" ><span class="display-4"><?=$details['users_count'] ?></span><p class="display-5">Total users</p><input type="hidden" id="project_id" name="" value="<?=$details['project_id'] ?>"></div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="vl"></div>
        <div class="col-md-3 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="text-center"><span class="display-4 "><?=$details['tasks_count'] ?></span></div>
                    <p class="text-center">Total tasks</p>
                    
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-2 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <div class="text-center"><span class="display-4"><?=round($details['t_minutes']/60,2) ?></span>h
                            <p class="text-center">Total time spent</p></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div><hr>
    <div class="row mt-5">
        <canvas id="project_time_chart" height="80px;"></canvas>
    </div><hr>
    
    <div class="row">
        <div class="col-12">
            <p class="efficiency text-center mt-4">Project table</p>
            <table id="project-list-datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>User name</th>
                        <th>Task count</th>
                        <th>Time spent</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <p class="efficiency text-center mt-4">Task table</p>
            <table id="task-list-datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Task name</th>
                        <th>user count</th>
                        <th>Time spent</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    
</div>

<footer class="">
    <hr>
    <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>