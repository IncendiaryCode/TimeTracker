<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->helper('url_helper');
?>

<script type="text/javascript">
    var email = "<?=isset($email)?$email:''?>";
    console.log(email);
</script>


<div class="bg col-md-6" id="mySlider"></div>
<div class="col-md-6 positioning-logo text-center"><img src="<?= base_url(); ?>assets/images/logo.png"></div>
<div id="form2" class="animated fadeIn login-form">
            <h4 class="text-center">Forgot Password</h4>
    <?php
    if ($this->session->flashdata('error_msg')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
    <?php } ?>
    <form id="forgotPassword" method="post" action="<?= base_url(); ?>login/check_otp" novalidate>
        <div class="logo-space">
            <p class="text-danger" id="email-error"></p>
            <p class="error" id="Uname-error"></p>
            <p style="color: green" class="animated rotateIn" id="rotate-text"></p>
        </div>
        <?php
        if ($this->session->flashdata('err_msg')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg'); ?></div>
        <?php }  ?>
        <div class="form-group" id="enter-email">
            <div class="input-group top-space">
                <input type="email" class="form-control  has-email-validation has-empty-validation font-weight-light border-top-0 border-left-0 border-right-0" id="Uname" name="email" value="<?php set_value('email'); ?>" placeholder="Enter email"><div class="spinner-border send-otp-spinner" id="send-otp-spinner"></div>
            </div>
            <div class="row email-input">
                <div class="col-6">
                    <a href="<?= base_url(); ?>login" class="text-left forgot-color"><i class="fas fa-arrow-left"></i> Back to login</a>
                </div>
                <div class="col-6 text-right">
                    <button class="btn btn-primary login-color" onclick="sendOTP()" type="button" id="getOTP">Send OTP</button>
                </div>
            </div>
        </div>
        <div class="form-group otp email-input" id="enter-otp">
            <div class="input-group mb-3">
                <input type="text" class="form-control font-weight-light border-top-0 border-left-0 border-right-0" id="otp1" name="otp" placeholder="Enter OTP">
                <div class="spinner-border resend-otp-spinner" id="send-otp-spinner"></div>
            </div>
            <p class="text-success" id="resent-otp"></p>
            <div class="row email-input" style="width: 100%;">
                <div class="col-6">
                    <a href="<?= base_url(); ?>login" class="text-left forgot-color"><i class="fas fa-arrow-left"></i> Back to login</a>
                </div>
                <div class="col-6 text-right">
                    <button class="btn btn-primary login-color" onclick="resendOTP()" type="button" id="getOTP1">Resend OTP</button>
                    <button class="btn btn-primary login-color ml-3" id="fill-otp" type="submit" name="submit"> Submit </button>
                </div>
            </div>
        </div>
    </form>
</div>