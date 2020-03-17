<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');

?>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row pt-5">
                    <div class="container">
                        <?php if(!empty($this->session->flashdata('success'))){ ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo (!empty($this->session->flashdata('success')))?$this->session->flashdata('success'):''; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } if(!empty($this->session->flashdata('failure'))){?>
                        <div class="alert-danger">
                        <?php echo (!empty($this->session->flashdata('failure')))?$this->session->flashdata('failure'):''; ?>
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                        <div class="text-center">
                                <img id="profile-pic" src="<?=base_url().UPLOAD_PATH.$res['profile'];?>" class="rounded-circle" width="150px;" height="150px;">
                                 <div class="edit">
                                    <div class="img-icon">
                                    <a href="#" class="text-white "><i class="change-image fas fa-camera" data-toggle="modal" data-target="#change-profile-pic"></i></a>
                                    </div>
                                </div> 
                        </div>
                        <div class="col-md-6 offset-md-3">
                            <form action="<?=base_url();?>index.php/admin/change_password" id="changePsw" method="post">
                                <p class="text-center display-5 mt-4">Change password</p>
                                <div class="alert-success"><?php echo isset($success)?$success:""; ?></div>
                                <?php 
                            if($this->session->flashdata('err_msg')){ ?>
                                <div class = "alert alert-danger">
                                    <?php echo $this->session->flashdata('err_msg'); ?>
                                </div>
                            <?php } ?>
                                <div class="form-group mt-4">
                                    <input type="password" class="form-control border-top-0 border-left-0 border-right-0" name="old-pass" id="old-pass" placeholder="Enter old password">
                                </div>
                                <div class="form-group mt-5">
                                    <input type="password" class="form-control border-top-0 border-left-0 border-right-0" name="new-pass" id="new-pass" placeholder="Enter new password">
                                </div>
                                <div class="form-group mt-5">
                                    <input type="password" class="form-control border-top-0 border-left-0 border-right-0" name="confirm-pass" id="confirm-pass" placeholder="Confirm password">
                                </div>
                                <p class="text-danger pt-3" id="psw-error"></p>
                                <p class="text-success pt-3" id="psw-success"></p>
                                <div class="text-right">
                                    <button class="btn btn-primary " type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal" id="change-profile-pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <!-- to change the profile picture of user-->
    <div class="modal-dialog animated">
        <div class="modal-content text-center">
            <div class="modal-header ">Upload image
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body upload-image">
                <form id="uploadImage" method="post" action="<?=base_url();?>index.php/admin/upload_profile" enctype="multipart/form-data">
                    <p class=""><input type="file" name="change_img" placeholder="Upload image"  class="form-control" id="profile-image"></p>
                    <p class="text-danger pt-3" id="imageerror"></p>
                    <button type="submit" class="btn btn-primary" id="submit-profile">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
