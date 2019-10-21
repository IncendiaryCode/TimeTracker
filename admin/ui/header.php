<!DOCTYPE html>
<?php
include("../configurations/constants.php");
session_start();
if(!isset($_SESSION['user'])){
    header("location:../time_tracker/index.php");
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> TimeTracker </title>
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/new.css">
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script type="text/javascript">
        var timeTrackerBaseURL = "<?=BASE_URL?>";
    </script>
</head>