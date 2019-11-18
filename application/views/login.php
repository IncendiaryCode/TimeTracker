<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');   
    $this->load->helper('url_helper');
?>
<body>
        <div class="bg  col-md-6" id="mySlider"></div>
        <div class="col-md-6 positioning-logo text-center"><img src="<?=base_url();?>assets/images/logo.png"></div>
        <div id="form1">  
            <?php 
                $this->load->library('form_validation');
                $this->load->helper('form');

                if(validation_errors()) { ?>
                    <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
            <?php } 
            if( $this->session->flashdata('err_message') )
            { ?>
               <div class="alert alert-danger"><?php echo $this->session->flashdata('err_message');?></div>
            <?php } ?>
             <div class="alert-success"><?php echo $this->session->flashdata('success');?></div>
                <form id="loginForm" class="login-form"  method="post" action="<?=base_url();?>index.php/login/login_process" novalidate>
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control-file logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="username" name="username" placeholder="User name">
                    </div>
                    <p class="error" id="username-error"></p>
                </div>
                <div class="form-group">
                    <div class="input-group ">
                        <input type="password" class="form-control-file top-space has-empty-validation border-top-0 border-left-0 border-right-0 font-weight-light" id="password" name="password" placeholder="Password" >
                    </div>
                    <p class="error" id="Password-error" ></p>
                </div>
                <div class="row top-space" style="width: 100%;">
                    <a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/load_forgot_pwd"' class="col-6 forgot-color" id="forgot">Forgot password?</a>

                    <button type="submit" class=" col-3 offset-3 login-color btn btn-primary" id="submit">Login</button>
                    <div class="error"><?php echo isset($error) ? $error : ''?></div>
                </div>
            </form>
        </div>
   

        