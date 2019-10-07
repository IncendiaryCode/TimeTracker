<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        TimeTracker
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/theme.css">
</head>

<body>
    <header class="container-fluid main-header">
        <div class="row ">
            <div class="col-6 time-tracker">
                <img src="../images/logo-white.png" onclick="window.location.href='employeeInfo.html'" height="50px">
            </div>
        </div>
    </header>
    <main class="container-fluid-main">
        <div class="  md main-container-employee">
            <div class="container mt-4 ">
                <div class="au-card au-card--no-shadow au-card--no-pad mb-5">
                    <div class="au-card-title">
                        <div class="bg-overlay bg-overlay--blue"></div>
                        <h3>
                            <i class="zmdi zmdi-account-calendar"></i>All Login Activities
                        </h3>
                    </div>
                    <div class="au-task js-list-load">
                        <div class="au-task-list js-scrollbar3">
                            <div class="au-task__item au-task__item--danger">
                                <div class="au-task__item-inner">
                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <footer>
                <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
            </footer>
        </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="../javascript/employeeActivities.js"></script>
</body>

</html>