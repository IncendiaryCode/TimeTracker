<?php
    session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TimeTracker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header class="container-fluid main-header">
        <div class="row">
            <div class="col-6 time-tracker">
                <img src="images/logo-white.png" height="40px" onclick="window.location.href='employeeInfo.php'">
            </div>
            <div class="col-12">
                <p class="display-4 mt-5  text-center">My Profile</p>
            </div>
        </div>
    </header>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row mt-5">
                    <div class="col-6 offset-3">
                        <div class="text-center mt-4">
                            <img src="images/<?=$_SESSION['user_image'];?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        </div>
                        <form action="change_pwd.php" method="post" class="mt-5" id="myProfile">
                            <div class="form-group">
                                <div class="input-group mb-3 ">
                                    <input type="password" class="mb-4 form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="oldPsw" name="psw1" placeholder="Enter Old Password">
                                </div>
                                <div class="input-group mb-3 ">
                                    <input type="password" class="mb-4 form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter New Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control-file top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm Password">
                                </div>
                                <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
                                <p class="text-danger"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-danger" id="alertMsg"></p>
                                <button type="submit" class="btn save-task  text-white">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <hr class="mt-5">
            <footer>
                <p class="text-center pt-2 ">© 2019 Printgreener.com</p>
            </footer>
        </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="javascript/employeeProfile.js"></script>
    <script src="javascript/addtask.js"></script>
</body>

</html>