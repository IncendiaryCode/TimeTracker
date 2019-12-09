<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$this->load->library('session');
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
                            <img src="<?=base_url().$picture;?>" height="40px" class="rounded-circle">
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
                <div class="row pt-5">
                    <div class="container">
                          
                        <div class="alert-success"><?php echo isset($success)?$success:""; ?></div>
                        <div class="text-center">
                            <span>
                                <img id="profile-pic" src="<?=base_url().$picture;?>" class="rounded-circle img-fluid" width="200px;" height="200px;">
                                 <div class="edit">
                                    <a href="#" class="text-white"><i class="change-image fas fa-camera" data-toggle="modal" data-target="#change-profile-pic"></i></a>
                                </div> 
                            </span>
                        </div>
                        <div class="col-md-4 offset-md-4">   
                            <form action="<?=base_url();?>index.php/admin/change_password" id="changePsw" method="post">
                                <p class="text-center display-5 mt-4">Change password</p>
                                <?php 
                        if(validation_errors()) { ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                        <?php } 
                        else if($this->session->flashdata('err_msg')){ ?>
                                <div class = "alert alert-danger">
                                    <?php echo $this->session->flashdata('err_msg'); ?>
                                </div>
                            <?php } ?>
                                <div class="form-group mt-4">
                                    <input type="password" class="form-control-file border-top-0 border-left-0 border-right-0" name="old-pass" id="old-pass" placeholder="Enter old password">
                                </div>
                                <div class="form-group mt-5">
                                    <input type="password" class="form-control-file border-top-0 border-left-0 border-right-0" name="new-pass" id="new-pass" placeholder="Enter new password">
                                </div>
                                <div class="form-group mt-5">
                                    <input type="password" class="form-control-file border-top-0 border-left-0 border-right-0" name="confirm-pass" id="confirm-pass" placeholder="Confirm password">
                                </div>
                                <p class="text-danger pt-3" id="psw-error"></p>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal" id="change-profile-pic" data-backdrop="false">
    <!-- to change the profile picture of user-->
    <div class="modal-dialog animated">
        <div class="modal-content text-center">
            <div class="modal-header ">Upload image
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="uploadImage" method="post" action="<?=base_url();?>index.php/admin/upload_profile" enctype="multipart/form-data">
                    <p><input type="file" name="change_img" placeholder="Upload image" id="profile-image"></p>
                    <p class="text-danger pt-3" id="imageerror"></p>
                    <button type="submit" class="btn btn-primary" id="submit-profile">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <footer class="text-center admin-footer">
    <hr>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com </p>
    </footer>