<!DOCTYPE html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$theme_mode = "theme-light";
if(!empty($GLOBALS['dark_mode']) && $GLOBALS['dark_mode'] == 1){
    $theme_mode = "theme-dark";
}
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title class="title">TimeTracker</title>
    <link rel="icon" href="<?=base_url();?>assets/images/logo.png" type="image/icon type">
    <!-- //User Dashboard assets -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap">
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.11.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/plugins/bxslider/css/jquery.bxslider.min.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="//www.jqueryscript.net/css/jquerysctipttop.css">    
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="//unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/animation.css?v=<?=VERSION?>">
    <?php
    if(!empty($GLOBALS['dark_mode'])){
     if ($GLOBALS['dark_mode'] == 1) { ?>    
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/theme_dark.css?v=<?=VERSION?>">
    <?php }  else { ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/style.css?v=<?=VERSION?>">
      <?php }
  }else{ ?>
        <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/style.css?v=<?=VERSION?>">
      <?php } ?> 
      <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/theme.css?v=<?=VERSION?>"> 
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/bootstrap-datetimepicker.min.css?v=<?=VERSION?>">
    <script type="text/javascript">
    var timeTrackerBaseURL = "<?=base_url();?>";
    </script>
</head>
<body class="<?=$theme_mode?>">
    <div class="container sticky-top"> 
        <div class="row">
            <header class="main-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand d-none d-lg-block" href="<?=base_url();?>"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px" ></a>
                    <a class="navbar-brand d-md-block d-lg-none" href="<?=base_url();?>"><img src="<?=base_url();?>assets/images/logo_small.png" height="40px" ></a>                
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item d-none d-md-block d-lg-block">
                            <a class="nav-link" href="<?= base_url(); ?>user/load_employee_activities">My activities</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link btn btn-primary" href="<?= base_url(); ?>user/load_add_task" onclick="return check_for_punchIn()"><i class="fas fa-plus icon-White "></i> Task</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link h4 mb-0" href="#change-profile" data-toggle="modal" data-placement="top" title="User Profile"><i class="fas fa-bars figure"></i></a>                                          
                        </li>
                    </ul>
                </nav>
            </header>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php if(!empty($GLOBALS['page_title'])) { ?>
            <div class="col-12">
                <p class="display-heading pt-3 text-center text-white">
                    <?php  echo $GLOBALS['page_title'];  ?>
                </p>
            </div>
        <?php } ?>
        </div>
    </div>