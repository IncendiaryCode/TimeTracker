<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= (isset($title)) ? 'TimeTracker - ' . $title : 'TimeTracker'; ?>
    </title>
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
    </script>
</head>

<body id="body-<?= $this->router->class ?>-<?= $this->router->method ?>">
    <div class="row" style="height: 100%; width:100%">
        <div class="bg col-md-6">
            <div id="mySlider"></div>
        </div>
        <div class="col-md-6">
            <div class="text-center positioning-logo">
                <img src="<?= base_url(); ?>assets/images/logo.png"></div>
            <div>
                <div id="form1">
                    <div class="row">
                        <div class="col">
                            <?php if (validation_errors()) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo validation_errors(); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php }
                    if ($this->session->flashdata('err_message')) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('err_message'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php }
                    if ($this->session->flashdata('success')) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php } ?>
                            <form id="loginForm" class="login-form" method="post" action="<?= base_url(); ?>login/login_process?" novalidate>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="email" class="form-control logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="username" name="username" placeholder="Email" value="<?=set_value('username')?>">
                                    </div>
                                </div>
                                <p class="text-danger" id="username-error"></p>
                                <div class="form-group">
                                    <div class="input-group ">
                                        <input type="password" class="form-control top-space has-empty-validation border-top-0 border-left-0 border-right-0 font-weight-light" id="password" name="password" placeholder="Password">
                                        <input type="hidden" id="time-zone" name="time-zone">
                                    </div>
                                </div>
                                <script src="<?=base_url();?>assets/user/javascript/momet_copy.js?v=<?=VERSION?>"></script>
                                <script src="<?=base_url();?>assets/user/javascript/moment_zone.js?v=<?=VERSION?>"></script>
                                <script type="text/javascript">
                                var timeZone = moment.tz.guess();
                                document.getElementById('time-zone').value = timeZone;
                                </script>
                                <p class="text-danger" id="password-error"></p>
                                <div class="row top-space" style="width: 100%; padding-top: 30px">
                                    <a href="<?= base_url(); ?>login/forgot_pwd" class="col-6 forgot-color" id="forgot-pwd">Forgot password?</a>
                                    <button type="submit" class=" col-3 offset-3 login-color" id="submit">Login</button>
                                    <div class="error">
                                        <?php echo isset($error) ? $error : '' ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/javascript/script.js?v=<?= VERSION ?>"></script>
</body>

</html>