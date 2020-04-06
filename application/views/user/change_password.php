<?php
    $GLOBALS['page_title'] = 'Change password';
    defined('BASEPATH') OR exit('No direct script access allowed');
    $profile = $this->session->userdata('user_profile');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row mt-4">
                <div class="col-6 offset-3">
                    <div class="text-center mt-4">
                        <img src="<?=base_url().USER_UPLOAD_PATH.$profile;?>" class="rounded-circle text-center" width="150px;" height="150px;">
                    </div>
                    <?php 
                            $this->load->library('form_validation');
                            if(validation_errors()) { ?>
                    <div class="alert alert-danger">
                        <?php echo validation_errors(); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php } 
                            if($this->session->flashdata('err_msg'))
                            { ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('err_msg');?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php }?>
                    <form action="<?=base_url();?>index.php/user/change_password" class="mt-4" id="myProfile" method="post">
                        <div class="form-group">
                            <div class="input-group mb-3 ">
                                <input type="password" class="mb-4 form-control-file  font-weight-light border-top-0 border-left-0 border-right-0" id="oldPsw" name="psw1" placeholder="Enter current Password">
                            </div>
                            <div class="input-group mb-3 ">
                                <input type="password" class="mb-4 form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter New Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input type="password" class="form-control-file  top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm Password">
                            </div>
                            <p class="text-danger"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-danger text-left" id="alertMsg"></p>
                            <button type="submit" class="btn btn-primary mb-5 ">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright ©  <?=date("Y") ?> | TimeTracker.com</p>
        </footer>
    </div>
</main>