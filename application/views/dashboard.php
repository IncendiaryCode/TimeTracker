 <?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#" class="text-white previous"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;"></a>
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
    <div class="container text-white-">

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
                                <h1><?php echo $total_projects; ?></h1>
                                <p class="card-text">Total Users</p>
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
                                <p class="card-text">Total Users</p>
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



        <!-- <div class="row mt-5 mb-5">
            <div class="col-sm-4 col-12  mt-1">
                <div class="card-body card1 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-sm"><?php echo $total_users; ?></span>
                            <span class="text-display pt-3">Total Users</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6 text-right">
                                <button class="btn btn-primary card11" onclick="window.location.href='<?=base_url();?>index.php/admin/add_users'"><i class="fas fa-plus"></i> Users</button>
                            </div>
                            <div class="col-6 text-left">
                                <button class="btn btn-primary card11" onclick="window.location.href='<?=base_url();?>index.php/admin/load_snapshot?type=user'">Vew details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12  mt-1">
                <div class="card-body card2 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-sm"><?php echo $total_projects; ?></span>
                            <span class="text-display pt-3">Total Projects</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6 text-right">
                                <button class="btn btn-primary card22" onclick="window.location.href='<?=base_url();?>index.php/admin/add_projects'"><i class="fas fa-plus"></i> Projects</button>
                            </div>
                            <div class="col-6 text-left">
                                <button class="btn btn-primary card22" onclick="window.location.href='<?=base_url();?>index.php/admin/load_snapshot?type=project'">Vew details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12  mt-1">
                <div class="card-body card3 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class=" fa fa-tasks"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-sm"><?php echo $total_tasks; ?></span>
                            <span class="text-display pt-3">Total Tasks</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6 text-right">
                                <button class="btn btn-primary card33" onclick="window.location.href='<?=base_url();?>index.php/admin/load_add_task'"><i class="fas fa-plus"></i> Tasks</button>
                            </div>
                            <div class="col-6 text-left">
                                <button class="btn btn-primary card33" onclick="window.location.href='<?=base_url();?>index.php/admin/load_task_snapshot'">Vew details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <hr>

        <div>
            <p class="text-center text-dark pt-3 display-heading">Project chart</p><!-- TODO.. -->
            <div class="form-group">
                <div class="text-right form">
                    <input type="month" class="border p-1" id="cur-month" name="cur_month"><span><button class="btn btn-primary" id="view-dashboard-chart"> view chart</button></span>
                </div>
            </div>
            <canvas id="main-chart" height="80px;"></canvas>
        </div>
        <div class="row mt-5 text-dark"><!-- TODO.. -->
            <div class="col-md-6 mt-md-3 mt-0">
                <h3 class="text-center">Top 5 expensive projects</h3>
                <ul class="list-group">
                  <li class="list-group-item">Sphere</li>
                  <li class="list-group-item">Time tracker</li>
                  <li class="list-group-item">Buck</li>
                  <li class="list-group-item">Latli</li>
                  <li class="list-group-item">xyz</li>
                </ul>
            </div>
            <div class="col-md-6 mt-md-3 mt-0">
                <h3 class="text-center">Top 5 expensive users</h3>
                <ul class="list-group">
                  <li class="list-group-item">aaa</li>
                  <li class="list-group-item">bbb</li>
                  <li class="list-group-item">ccc</li>
                  <li class="list-group-item">ddd</li>
                  <li class="list-group-item">eee</li>
                </ul>
            </div>
        </div>
    </div>
    <footer>
     <hr>
      <p class="text-center">Copyright © 2019 Printgreener.com</p>
    </footer>