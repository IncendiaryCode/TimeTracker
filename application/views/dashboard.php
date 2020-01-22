<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
?>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#" onclick = "window.location.reload()" ><img src="<?=base_url().UPLOAD_PATH?>logo-white.png" height="40px;"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#" class="text-white"><img src="<?=base_url().UPLOAD_PATH.$profile;?>" height="50px" width="50px;" class="rounded-circle"></a>
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
    <div class="container">
        <div class="row mt-5 mb-5" id="dashboard-stats">
            <div class="col-sm-4 col-12">
                <div class="card card-theme-a">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-right">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="col-6">
                                <h1><?php echo $total_users; ?></h1>
                                <p class="card-text">Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/add_users" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> User</a>
                            </div>
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/load_snapshot?type=user" class="btn btn-primary btn-block">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card card-theme-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-right">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="col-6">
                                <h1>
                                    <?php echo $total_projects; ?>
                                </h1>
                                <p class="card-text">Total projects</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/add_projects" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Project</a>
                            </div>
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/load_snapshot?type=project" class="btn btn-primary btn-block">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card card-theme-c">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-right">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="col-6">
                                <h1><?php echo $total_tasks; ?></h1>
                                <p class="card-text">Total tasks</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/load_add_task" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Task</a>
                            </div>
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/load_task_snapshot" class="btn btn-primary btn-block">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div>
            <p class="text-center text-primary pt-3 display-heading">Project chart</p><!-- TODO.. -->
            <div class="form-group">
                <div class="text-right form">
                    <input type="month" class="border p-1" id="cur-month" name="cur_month">
                </div>
            </div>
            <canvas id="main-chart" height="80px;"></canvas>
        </div>
        <div class="row pt-5 text-dark">
            <!-- TODO.. -->
            <div class="col-md-6">
                <?php 
                $count = 0;
                    if($top_projects[0] < 5)
                    {
                        $count = $top_projects[0];
                    }
                    else
                    {
                     $count = 5;   
                    }
                 ?>
                <h4 class="text-center">Top
                    <?= $count; ?> expensive projects</h4>
                <ul class="list-group mt-4">
                    <?php foreach($top_projects[1] as $project)
                    { ?>
                    <!--  -->
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?=base_url();?>index.php/admin/load_project_detail?project_id=<?=$project['project_id'] ?>">
                                    <?php
                                    if($project['image_name'] != ''){
                                        
                                        ?>
                                    <img src="<?=base_url().UPLOAD_PATH.$project['image_name'];?>" width="30px;">
                                    <?php
                                    } ?>
                                    <?php echo $project["project_name"]; ?>
                                </a>
                            </div>
                            <div class="col-6">
                                <?php echo round($project["t_minutes"]/60,2); ?> hrs</div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-md-6">
                <?php 
                $cnt = 0;
                    if($top_users[0] < 5)
                    {
                        $cnt = $top_users[0];
                    }
                    else
                    {
                     $cnt = 5;   
                    }
                 ?>
                <h4 class="text-center">Top
                    <?= $cnt;?> expensive users</h4>
                <ul class="list-group mt-4">
                    <?php foreach($top_users[1] as $user)
                    { ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-6"><a href="<?=base_url();?>index.php/admin/load_userdetails_page?user_id=<?= $user['user_id']; ?>">
                                    <?php echo $user["user_name"]; ?>
                                </a></div>
                            <div class="col-6">
                                <?php echo round($user["t_minutes"]/60,2); ?> hrs</div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <hr>
    <footer class="pb-5">
        <p class="text-center">Copyright © 2019 Printgreener.com</p>
    </footer>