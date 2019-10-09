<!DOCTYPE html>
<?php
include("configurations/constants.php");
session_start();
// $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
if(isset($_SESSION['user_id'])){
    //header("location:user/home.php");
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Time Tracker</title>
    <?php if(!isset($_SESSION['user'])){ ?>
        <!-- //Login assets -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
        <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/assets/css/login.css">
        <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/assets/css/new.css">
    <?php } else { ?>
        <!-- //User Dashboard assets -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.7.0/css/all.css">
        <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/assets/css/login.css">
        <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/assets/css/new.css">
    <?php } ?>
</head>