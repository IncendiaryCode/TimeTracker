<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$this->load->library('session');
$profile = $this->session->userdata('user_profile');
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
                <div class="container">
                    <div class="row">
                        <div class="col-8 offset-2">
                        <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                        <?php } ?>
                        <div class="alert-success"><?php echo isset($success)?$success:""; ?></div>  
                        <form action="<?php echo base_url();?>index.php/admin/add_tasks" id="addTask" method="post">
                            <div class="form-group mt-5" id="append-new-user">
                                <label for="user-name ">Choose the name of user to assign task</label>
                                <select class="form-control"  id="user-name0" name="user_name0">
                                    <option>Select User</option>
                                    <?php
                                        foreach($names as $name){ ?>
                                        <option><?php echo $name['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div><i class="fas fa-plus icon-plus text-success"></i></div>
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
                            <div class="form-group">
                                <label for="module">Choose module</label>
                                <select class="form-control" id="module" name="module">
                                    
                                </select>
                            </div>
                            <p id="user-name-error" class="text-danger"></p>
                            <button type="submit" class="btn btn-primary">Assign Task</button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
