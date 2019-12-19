<!DOCTYPE html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> TimeTracker </title>
    <link rel="icon" href="<?=base_url();?>assets/images/logo.png" type="image/icon type">
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="//unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/user/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?v=<?=VERSION?>">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/style.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/login.css?v=<?=VERSION?>">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/theme.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/new.css?v=<?=VERSION?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/admin.css?v=<?=VERSION?>">
    <script type="text/javascript">
    var timeTrackerBaseURL = "<?=base_url();?>";
    </script>
</head>