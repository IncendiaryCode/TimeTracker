<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$this->load->library('session');
$profile = $this->session->userdata('user__profile');
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'">
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
                                                <th scope="row"><a href="#" onclick=window.location.href="adminNotifications.php">See all notifications</a></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <img src="<?=base_url();?>assets/images/<?=$profile?>" height="40px" class="rounded-circle">
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class=" text-center">
                                    <p><a href="#" onclick='window.location.href="<?=base_url();?>index.php/profile"' class="text-display">Profile</a></p>
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
        <div class="container-fluid">
            <div class="main-container-inner">
                <div class="row mt-5">
                    <div class="col-6 offset-3">
                        <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                        <?php } ?>  
                        <form action="<?php echo base_url();?>index.php/add_task/add_tasks" id="addTask" method="post">
                            <div class="form-group mt-3">
                                <label for="user-name ">Enter the name of User to assign task</label>
                                <input type="text" class="form-control" name="user-name" id="Username">
                            </div>
                            <div class="form-group">
                                <label for="task-name">Enter the Task name</label>
                                <input type="text" class="form-control" id="task_name" name="task_name">
                            </div>
                            <div class="form-group">
                                <label for="description">Write a small description</label>
                                <textarea class="form-control" id="description" name="description"rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="choose-project">Choose a project</label>
                                <select class="form-control" id="chooseProject" name="chooseProject">
                                <option>Select Project</option>
                                <?php 
                                    foreach($result as $p){ ?>
                                    <option><?=$p['name']; ?></option>
                                <?php } ?> 
                            </select>
                            </div>
                            <p id="taskError" class=" text-danger"></p>
                            <button type="submit" class="save-task">Assign Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
