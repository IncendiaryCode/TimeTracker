<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$profile = $this->session->userdata('user_profile');
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#"><img src="<?=base_url().UPLOAD_PATH?>logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <img src="<?=base_url().UPLOAD_PATH.$profile;?>" height="40px" class="rounded-circle">
                            <div class="dropdown-menu dropdown-menu-right">
                                <div>
                                    <p class="items"> <a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display pl-3"> Profile</a></p>
                                    <p class="items" ><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display pl-3"> <i class="fas fa-power-off"></i> Logout</a></p>
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
                <div class="row mt-2">
                    <div class="col-md-6 offset-md-3">
                        <?php 
                            if(validation_errors()) { ?>
                                <div class="alert alert-danger">
                                    <?php 
                                        echo validation_errors();
                                    ?>    
                                </div>
                            <?php } 
                            if($this->session->flashdata('true')){ ?>
                                <div class="alert-success">
                                    <?php  
                                        echo $this->session->flashdata('true'); 
                                    ?>    
                                </div>
                            <?php } 
                            else if($this->session->flashdata('err')){ ?>
                                <div class = "alert alert-danger">
                                    <?php echo $this->session->flashdata('err'); ?>
                                </div>
                            <?php } ?> 
                        <p class="text-center display-heading text-primary">Add user</p>
                        <form action="<?php echo base_url();?>index.php/admin/add_users" id="addUser" method="post">
                            <div class="form-group mt-3">
                                <label for="task-name ">Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="task_name" id="newUser" value="">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Email:<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="user_email" id="user_email">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Password:<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="task_pass" id="task_pass" value="">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Contact number:</label>
                                <input type="tel" minlength="10" maxlength="10" class="form-control" name="contact" id="contact" value="">
                            </div>
                            <p id="user-error" class="text-danger"></p>
                            <div class="text-right"><button type="submit" class="btn btn-primary" value="submit">Add User</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="admin-footer">
    <hr>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>