<?php
include("header.php");
    if(!isset($_SESSION['user'])){
        header('location:http://localhost/time_tracker/index.php');
        die();
    }
    include("../php/get_number.php");
?>
<body>
    <div class="container-fluid text-white">
        <div class="row mt-5 mb-5">
            <div class="col-xl-2 col-sm-4 col-12 offset-xl-2 mt-1">
                <div class="card-body card1 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-lg"><?php echo $row; ?></span>
                            <span class="text-display pt-3">Total Users</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-link text-white" onclick="window.location.href='addUser.php'"><i class="fas fa-plus"></i> Users</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4 col-12 offset-xl-1 mt-1">
                <div class="card-body card2 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-lg"><?php echo $row_proj; ?></span>
                            <span class="text-display pt-3">Total Projects</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-link text-white" onclick="window.location.href='addProject.php'"><i class="fas fa-plus"></i> Projects</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-sm-4 col-12 offset-xl-1 mt-1">
                <div class="card-body card3 shadow-lg pb-4">
                    <div class="row pt-5">
                        <div class="col-lg-5 col-4 text-right">
                            <i class=" fa fa-tasks"></i>
                        </div>
                        <div class="col-lg-7 col-8 text-center">
                            <span class="font-weight-bold text-display number-font shadow-lg"><?php echo $row_task; ?></span>
                            <span class="text-display pt-3">Total Tasks</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-link text-white" onclick="window.location.href='addTask.php'"><i class="fas fa-plus"></i> Tasks</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="container ">
        <h2 class="text-center pt-3">Project Chart</h2>
        <p class="text-right"><i class="fas fa-list-ul"></i></p>  
        <canvas id="canvas" width="300" height="100"></canvas>
    </div>
    <hr>
    <div class="container table ">
        <h3 class="text-center mt-5 pb-3">Activities</h3>
        <p class="text-right"><i class="fas fa-list-ul"></i></p>
        <div class="col-8 text-right ">
        </div>
        <div class="table-responsive  table-responsive-data2">
            <table class="table table-hover">
                <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Task</th>
                        <th scope="col">Number of Users</th>
                        <th scope="col">Status</th>
                        <th scope="col">Modify</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sphere</td>
                        <td>abc</td>
                        <td>4</td>
                        <td>50%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                    <tr>
                        <td>Latli</td>
                        <td>pqr</td>
                        <td>5</td>
                        <td>55%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                    <tr>
                        <td>Buck</td>
                        <td>xyz</td>
                        <td>4</td>
                        <td>75%</td>
                        <td><i class="far fa-edit" data-toggle='tooltip' data-placement="top" title="Edit "></i>
                            <i class="fas fa-trash-alt pl-4" data-toggle='tooltip' data-placement="top" title="Delete "></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <footer>
      <p class="text-center m-5">Copyright © 2019 Printgreener.com</p>
    </footer>
<?php include("footer.php"); ?>