<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= (isset($title)) ? 'TimeTracker - ' . $title : 'TimeTracker'; ?></title>
    <link rel="icon" href="<?= base_url(); ?>assets/images/logo.png" type="image/icon type">
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/login.css?v=<?= VERSION ?>">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/theme.css?v=<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/new.css?v=<?= VERSION ?>">
    <script type="text/javascript">
        var timeTrackerBaseURL = "<?= base_url(); ?>";
        var email = "<?= isset($email) ? $email : '' ?>";
    </script>
</head>

<body id="body-<?= $this->router->class ?>-<?= $this->router->method ?>">

    <div class="bg col-md-6" id="mySlider"></div>
    <div class="col-md-6 positioning-logo text-center"><img src="<?= base_url(); ?>assets/images/logo.png"></div>
    <div id="form2" class="animated fadeIn login-form">
        <h4 class="text-center mt-5 mt-md-0">Forgot Password</h4>
        <?php
        if ($this->session->flashdata('error_msg')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
        <form id="forgotPassword" method="post" action="<?= base_url(); ?>login/check_otp" novalidate>
            <div class="logo-space">
                <p class="text-danger" id="email-error"></p>
                <p class="error" id="Uname-error"></p>
                <p style="color: green" class="animated rotateIn" id="rotate-text"></p>
            </div>
            <?php
            if ($this->session->flashdata('err_msg')) { ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                            
                </div>
            <?php }  ?>
            <div class="form-group" id="enter-email">
                <div class="input-group top-space">
                    <input type="email" class="form-control  has-email-validation has-empty-validation font-weight-light border-top-0 border-left-0 border-right-0" id="Uname" name="email" value="<?php set_value('email'); ?>" placeholder="Enter email">
                    <div class="spinner-border send-otp-spinner" id="send-otp-spinner"></div>
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
                <p>Check your email for OTP. &nbsp;&nbsp; <a href="javascript:resendOTP()" id="getOTP1">Resend</a></p>
                <div class="row email-input" style="width: 100%;">
                    <div class="col-8">
                        <a href="<?= base_url(); ?>login" class="text-left forgot-color"><i class="fas fa-arrow-left"></i> Back to login</a>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-primary btn-block login-color ml-4" id="fill-otp" type="submit" name="submit"> Submit </button>
                        <!-- <button class="btn btn-primary login-color" onclick="resendOTP()" type="button" id="getOTP1">Resend OTP</button> -->
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/javascript/script.js?v=<?= VERSION ?>"></script>
</body>

</html>