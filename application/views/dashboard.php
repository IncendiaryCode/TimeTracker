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
    <div class="container text-white">
        <div class="row mt-5 mb-5">
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
        </div>
        <hr>
        <div>
            <h2 class="text-center pt-3">Project Chart</h2><!-- TODO.. -->
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
    <!--<div class="container table">
        <h3 class="text-center mt-5 pb-3">Activities</h3>
        <p class="text-right"><i class="fas fa-list-ul"></i></p>
        <div class="col-8 text-right">
        </div>
        <div class="table-responsive  table-responsive-data2">
            <table class="table table-hover">
                <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Task</th>
                        <th scope="col">Number of Users</th>
                        <th scope="col">Status</th>
                        <th scope="col">Modify</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sphere</td>
                        <td>abc</td>
                        <td>4</td>
                        <td>50%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                    <tr>
                        <td>Latli</td>
                        <td>pqr</td>
                        <td>5</td>
                        <td>55%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                    <tr>
                        <td>Buck</td>
                        <td>xyz</td>
                        <td>4</td>
                        <td>75%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> -->

    <footer>
     <hr>
      <p class="text-center">Copyright © 2019 Printgreener.com</p>
    </footer>