<!DOCTYPE html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title class="title">TimeTracker</title>
    <link rel="icon" href="<?=base_url();?>assets/images/logo.png" type="image/icon type">
    <!-- //User Dashboard assets -->
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.11.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="//www.jqueryscript.net/css/jquerysctipttop.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap">
    <link rel="stylesheet" type="text/css" href="//unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/plugins/bxslider/css/jquery.bxslider.min.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/animation.css?v=<?=VERSION?>">
    <?php
    if(!empty($GLOBALS['dark_mode'])){

     if ($GLOBALS['dark_mode'] == 1) { ?>
    
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/dark_mode_style.css?v=<?=VERSION?>">
    
    <?php }  else { ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/style.css?v=<?=VERSION?>">
      <?php }
  }else{ ?>
        <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/style.css?v=<?=VERSION?>">
      <?php } ?>  
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/bootstrap-datetimepicker.min.css?v=<?=VERSION?>">
    <script type="text/javascript">
    var timeTrackerBaseURL = "<?=base_url();?>";
    </script>
</head>

<body>
    <header class="container main-header">
        <div class="row">
            <?php if(empty($GLOBALS['page_title'])) { ?>
                <div class="col-md-6 col-6 time-tracker">
                    <img src="<?=base_url();?>assets/images/logo-white.png" height="40px" >
                </div>
            <?php } else { ?>
            <div class="col-md-6 col-6 time-tracker">
                <a href="<?=site_url();?>/user" class="link-to-back">
                    <img src="<?=base_url();?>assets/images/logo-white.png" height="40px" >
                </a>
            </div>
        <?php } ?>
            <!-- refers to home page -->
            <div class=" col-6 text-right">
            <?php
             if(empty($GLOBALS['page_title'])) {
                ?>
                <div class="row">
                    <div class="col-9 col-md-11 text-right btn-task">
                <a href="<?=site_url();?>/user/add_tasks" class="btn btn-primary" id="new-task"><i class="fas fa-plus icon-White "></i> Task</a>
            </div>
                    <!-- to chage image -->
                    <div class="col-3 col-md-1 text-right">
                    <a href="#" class="text-white profile-icon"><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#change-profile" data-toggle="tooltip" data-placement="top" title="User Profile"></i></a>
                </div>
            </div>
            <?php } else { ?>
           
            <!-- refers to all other pages  -->
                <a href="#" class="text-white profile-icon"><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#change-profile" data-placement="top" title="User Profile"></i></a>
            </div>
            <div class="col-12">
                <p class="display-heading pt-3 text-center">
                    <?php echo $GLOBALS['page_title'] ?>
                </p>
            <?php } ?>
            </div>
        </div>
    </header>