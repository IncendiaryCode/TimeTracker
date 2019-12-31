<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');   
    $this->load->helper('url_helper');
    $this->load->library('form_validation');
    $this->load->helper('form');
    $this->load->library('session');
?>

<body>
	<div class="bg  col-md-6" id="mySlider"></div>
    <div class="col-md-6 positioning-logo text-center"><img src="<?=base_url();?>assets/images/logo.png"></div>
    <div id="formPsw" class="">
            <form id="reEnterPsw" method="post" class="login-form" action="<?=base_url();?>index.php/login/change_pass" novalidate>
                <div class="text-center">
                    <div class=" logo-space">
                        <h4 class="text-center">Change Password</h4>
                    </div>
                </div>
               <!--  <?php 
                    
                    if($this->session->flashdata('err_msg'))
                    { ?>
                        <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg');?></div>
                    <?php }?> -->
                <div class="form-group">
                    <div class="input-group">
                        <input type="hidden" class="form-control-file logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light"  id="user-email" name="mail" placeholder="username" value="<?php echo (isset($result))?$result:"";?>">
                    </div>
                </div>
                <div class="form-group">
                      <div class="input-group mb-3 top-space">
                        <input type="password" class="form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter new password">
                      </div>
                </div>
                <div class="form-group otp">
                      <div class="input-group mb-3  top-space">
                        <input type="password" class="form-control-file top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm password">
                    </div>
                </div>
                <p class="error" id="cnfrmPsw"></p>
            
                <div class="row top-space" style="width: 100%;">
                    <a href="<?=base_url();?>index.php/login" class="col-6 forgot-color"><i class="fas fa-arrow-left"> back to login</i></a>
                    <button type="submit" class="col-3 offset-3 btn btn-primary login-color" id="count">Submit</button>
                </div>
            </form>
        </div>