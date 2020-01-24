<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="bg col-md-6" id="mySlider"></div>
<div class="col-md-6 positioning-logo text-center"><img src="<?= base_url(); ?>assets/images/logo.png"></div>
<div id="form1">
    <?php if (validation_errors()) { ?>
        <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
    <?php }
    if ($this->session->flashdata('err_message')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('err_message'); ?></div>
    <?php }
    if ($this->session->flashdata('success')) { ?>
        <div class="alert-success"><?php echo $this->session->flashdata('success'); ?></div>
    <?php } ?>
    <form id="loginForm" class="login-form" method="post" action="<?= base_url(); ?>login/login_process" novalidate>
        <p class="error" id="password-error"></p>
        <p class="error" id="username-error"></p>
        <div class="form-group">
            <div class="input-group">
                <input type="email" class="form-control logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="username" name="username" placeholder="Email">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group ">
                <input type="password" class="form-control top-space has-empty-validation border-top-0 border-left-0 border-right-0 font-weight-light" id="password" name="password" placeholder="Password">
            </div>
        </div>
        <div class="row top-space" style="width: 100%; padding-top: 30px">
            
            <a href="<?= base_url(); ?>login/forgot_pwd" class="col-6 forgot-color" id="forgot-pwd">Forgot password?</a>
            <button type="submit" class=" col-3 offset-3 login-color" id="submit">Login</button>
            <div class="error"><?php echo isset($error) ? $error : '' ?></div>
        </div>
    </form>
</div>