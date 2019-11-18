<!DOCTYPE html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Time Tracker</title>   
    <!-- //User Dashboard assets -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/style.css?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/new.css?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/css/add_task_style.css?>">


    <script type="text/javascript">
        var timeTrackerBaseURL = "<?=base_url();?>";
    </script>
</head>

<body>
    <header class="container main-header">
        <div class="row">
            <div class="col-6 time-tracker">
                <a href="<?=site_url();?>/user">
                    <img src="<?=base_url();?>assets/images/logo-white.png" height="40px">
                </a>
            </div>

            <?php if(empty($GLOBALS['page_title'])) { ?>        <!-- refers to home page -->
            <div class="col-5 text-right ">
                <a href="<?=site_url();?>/user/add_tasks" class="btn btn-primary" id="new-task"><i class="fas fa-plus icon-White "></i> Task</a>
            </div>
            <div class="col-1 text-left" id="append">
                <!-- to chage image -->
                <h2><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#change-profile" data-toggle="tooltip" data-placement="top" title="User Profile"></i></h2>
           </div>
       		<?php } else { ?>  <!-- refers to all other pages  -->
           <div class="col-6 text-right">
                <h2><i class="fas fa-bars figure" id="append-hide" data-toggle="modal" data-target="#change-profile" data-placement="top" title="User Profile"></i></h2>
            </div>
            <div class="col-12">
                <p class="display-4 pt-3 text-center"><?php echo $GLOBALS['page_title'] ?></p>
            </div>
       		<?php } ?>

        </div>
    </header>