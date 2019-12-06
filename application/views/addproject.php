<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link pr-4">
                        <div class="dropdown" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <i class="far fa fa-qrcode"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <table class="table table-hover">
                                        <thead>Options</thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row"><a href="#">Assign Task</a></th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row"><a href="#" onclick=window.location.href="adminOptions.html">See all </a></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item nav-link pr-4">
                        <div class="dropdown" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <i class="far fa-bell"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <table class="table table-hover">
                                        <thead>You have 3 notificatoins</thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row"><a href="#" onclick=window.location.href="adminNotifications">See all notifications</a></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <img src="<?=base_url().$picture?>" height="40px" class="rounded-circle">
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <p><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display">Profile</a></p>
                                    <p><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display"><i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row mt-2 pt-4">
                    <div class="col-6 offset-3">
                        <!-- <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                        <?php } ?> 
                        <div class="alert-success"><?php echo isset($success)?$success:""; ?></div> -->
                        <form action="<?php echo base_url();?>index.php/admin/add_projects" id="add-project" method="post">
                            <div class="form-group mt-3">
                                <label for="task-name ">Enter the Project name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="project-name" id="project-name">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Choose logo for project</label>
                                <input type="file" class="form-control" name="project-logo" id="Project-logo">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Choose color for project</label>
                                <input type="color"  value="#e384fb" class="form-control" name="project-color" id="Project-color">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Enter start date</label>
                                <input type="text" class="form-control edit-date" name="start-date" id="start-date">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Enter end date</label>
                                <input type="text" class="form-control edit-date" name="end-date" id="end-date">
                            </div>
                            <p id="project-error" class="text-danger"></p>
                            <button type="submit" class="btn btn-primary">Add Project</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer class="admin-footer">
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>