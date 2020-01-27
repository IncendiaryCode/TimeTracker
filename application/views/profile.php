<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->helper('url_helper');
    $profile = $this->session->userdata('user_profile');
?>
     <header class="container-fluid">
        <div class="row">
            <div class="col-6 m-3">
                <img src="<?=base_url().UPLOAD_PATH?>logo-white.png" height="40px" onclick="window.location.href='<?=base_url();?>index.php/admin'">
            </div>
        </div>
    </header> 

   <!--  <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="dropdown text-right" id="dropdown-recent-acts">
                    <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                        
                        <div class="dropdown-item checkbox"><input type="checkbox"  id="admin-dark-mode" name="dark-mode"> Dark mode</div>
                        <div class="dropdown-item checkbox"><input type="checkbox"  id="admin-normal-mode" name="normal-mode"> Normal mode</div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <main class="container-fluid-main">
        <div class="main-container-inner mt-3">
        <div class="md main-container-employee text-center">
            <img id="new_img" src="<?=base_url();?>assets/images/<?=$profile;?>" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeImage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
            <div class="container ">
                <?php 
                    $this->load->library('form_validation');
                    if(validation_errors()) { ?>
                        <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                   <?php } 
                    if( $this->session->flashdata('err_msg') )
                 { ?>
               <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg');?></div>
               <?php } ?>

                <form method="post" action="<?=base_url();?>index.php/admin/change_password" id="changePsw">
                    <h5 class="text-center mt-4">Change Password</h5>
                    <div class="form-group">
                        <div class="input-group mb-3 ">
                            <input type="password" class="mb-4 form-control font-weight-light border-top-0 border-left-0 border-right-0" id="oldPsw" name="psw1" placeholder="Enter Old Password">
                        </div>
                        <div class="input-group mb-3 ">
                            <input type="password" class="mb-4 form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter New Password">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="mb-4 form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm Password">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn save-task  text-white">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">Copyright © <?=Date("Y"); ?> Printgreener.com</ p>
            </footer>
        </div>
    </main>
     <div class="modal" id="changeImage">
        <div class="modal-dialog animated">
            <div class="modal-content ">
                <div class="modal-header ">Upload image
                    <button type="button" class="close text-danger" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="uploadImage" method="post" enctype="multipart/form-data" action="<?=base_url();?>index.php/admin/upload_profile">
                        <p><input type="file" name="change_image" placeholder="Upload image" id="image"></p>
                        <p class="text-danger" id="imageErr"></p>
                        <button type="submit" class="btn save-task submitImage" >Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
