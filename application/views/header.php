<!DOCTYPE html>
<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= (isset($title)) ? 'TimeTracker - ' . $title : 'TimeTracker' ?></title>
    <link rel="icon" href="<?= base_url(); ?>assets/images/logo.png" type="image/icon type">
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="//unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/user/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?v=<?= VERSION ?>">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css?v=<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/login.css?v=<?= VERSION ?>">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/theme.css?v=<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/new.css?v=<?= VERSION ?>">
    <script type="text/javascript">
        var timeTrackerBaseURL = "<?= base_url(); ?>";
    </script>
</head>

<body id="body-<?= $this->router->class ?>-<?= $this->router->method ?>">

    <header id="main-navbar">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="<?=base_url()?>index.php/admin"><img src="<?= base_url() . UPLOAD_PATH ?>logo-white.png" height="40px;"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#" class="text-white"><img src="<?= base_url() . UPLOAD_PATH . $profile; ?>" height="40px" class="rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div>
                                    <p class="items"><a href="#" onclick='window.location.href="<?= base_url(); ?>index.php/admin/load_profile"' class="text-display pl-2"> Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?= base_url(); ?>index.php/login/logout"' class="text-display pl-2"> <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>