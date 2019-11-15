<!DOCTYPE html>
<?php
include("../configurations/constants.php");
session_start();
if(!isset($_SESSION['user'])){
    header("location:".BASE_URL."index.php");
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Time Tracker</title>
    <!-- //User Dashboard assets -->
    <link href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.11.0/css/all.css">
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
    <link href="//unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>assets/css/animate.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>assets/css/style.css?v=<?=VERSION?>">
    <script type="text/javascript">
        var timeTrackerBaseURL = "<?=BASE_URL?>";
    </script>
</head>

<body>
    <header class="container main-header">
        <div class="row">
            <div class="col-6 time-tracker">
                <a href="<?=BASE_URL?>user/home.php">
                    <img src="<?=BASE_URL?>assets/images/logo-white.png" height="40px">
                </a>
            </div>

            <?php if(empty($GLOBALS['page_title'])) { ?>        <!-- refers to home page -->
            <div class="col-5 text-right ">
                <a href="<?=BASE_URL?>user/add_task.php?add=add_t" class="btn btn-primary" id="new-task"><i class="fas fa-plus icon-White "></i> Task</a>
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