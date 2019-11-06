<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');   
    $this->load->helper('url_helper');
?>

<body>
	<div class="bg  col-md-6" id="mySlider"></div>
    <div class="col-md-6 positioning-logo text-center"><img src="<?=base_url();?>assets/images/logo.png"></div>
	<div id="form2" class="animated fadeInRightBig login-form">
								
				                <?php 
		                          $this->load->library('form_validation');
			                	  $this->load->helper('form');
			                	  $this->load->library('session');
		                          if($this->session->flashdata('error_msg'))
		                            { ?>
		                            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg');?></div>
		                        <?php }?>
    <form id="forgotPassword" method="post" action="<?=base_url();?>index.php/forgot_pwd/check_otp" onsubmit="validateOtp()" novalidate>
                    <div class="logo-space">
                        <h5 class="text-center">Forgot Password</h5>
                        <p class="error" id="here"></p>
                          <p class="error" id="Uname-error"></p>
                          <p style="color: green" class="animated rotateIn" id="rotate-text"></p>
                    </div>
                    <?php 
					                $this->load->library('form_validation');
					                $this->load->helper('form');
					                $this->load->library('session');
						        if($this->session->flashdata('err_msg'))
				                { ?>
				                    <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg');?></div>
				                <?php }?>
                <div class="form-group">
                         <div class="input-group mb-3 top-space">
                            	<input type="email" class="form-control-file  has-email-validation has-empty-validation font-weight-light border-top-0 border-left-0 border-right-0" id="Uname" name="email" value="<?php set_value('email');?>" placeholder="Enter email">
                            	
				                </div>
                             	<a onclick="sendOTP()" id="getOTP" href="#" class="text-left btn btn-link forgot-color p-0">Send OTP</a>   
                    </div>
                    <div class="form-group otp">
                          <div class="input-group mb-3">
                            <input type="text" class="form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="otp1" name="otp" placeholder="Enter OTP">
                          </div>
                          
                    </div>
                      <div class="row top-space" style="width: 100%;">
                        <a href="<?=base_url();?>index.php/login" class="col-6 text-left forgot-color"><i class="fas fa-arrow-left"> back to login</i></a>
                        <button onclick="window.location.href='<?=base_url();?>index.php/forgot_pwd/check_otp'" class="btn btn-primary col-3 offset-3 login-color" id="count" type="submit" name="submit">  Submit  </button>
                    </div>              
            </form> 
        </div>