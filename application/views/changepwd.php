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
    <div id="formPsw" class="">
        <form id="reEnterPsw" method="post" class="login-form" action="<?= base_url(); ?>index.php/login/change_pass" novalidate>
            <div class="text-center">
                <div class=" logo-space">
                    <h4 class="text-center">Change Password</h4>
                </div>
            </div>
            <?php

            if ($this->session->flashdata('err_msg')) { ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('err_msg'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            <div class="form-group">
                <div class="input-group">
                    <input type="hidden" id="user-email" name="mail" placeholder="username" value="<?php echo (isset($result)) ? $result : ""; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group  email-input">
                    <input type="password" class="form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter new password">
                </div>
            </div>
            <div class="form-group otp">
                <div class="input-group email-input">
                    <input type="password" class="form-control  font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm password">
                </div>
            </div>
            <p class="error" id="cnfrmPsw"></p>

            <div class="row email-input" style="width: 100%;">
                <a href="<?= base_url(); ?>index.php/login" class="col-6 forgot-color"><i class="fas fa-arrow-left"> </i>back to login</a>
                <button type="submit" class="col-3 offset-3 btn btn-primary login-color" id="count">Submit</button>
            </div>
        </form>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/javascript/script.js?v=<?= VERSION ?>"></script>
</body>

</html>