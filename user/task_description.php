<?php 
$GLOBALS['page_title'] = 'Task Description';
include("header.php");
include("../php/login_activities.php");
?>
<body>
   
    <main class="container-fluid-main">
        <div class="  md main-container-employee">
            <div class="container mt-4 ">
                <div class="au-card au-card--no-shadow au-card--no-pad mb-2">
                    <div class="au-card-title pt-5">
                        <div class="bg-overlay bg-overlay--blue"></div>
                        <h3>
                            <i class="zmdi zmdi-account-calendar"></i>
                        </h3>
                    </div>
                    <div class="au-task js-list-load">
                        <div class="au-task-list js-scrollbar3">
                            <div class="au-task__item au-task__item--danger">
                                <!-- retrieve from dayabase and pass it. -->
                                <div class=' ml-lg-5 m-3 mt-5 p-5 shadow card-style' onclick = 'clearTime()' data-toggle='modal' data-target='#newModal' data-toggle='tooltip' data-placement='top'>
                                    <div class='card-header bg-white text-left text-black-50 '>
                                        <div class='row pt-2'><span class='vertical-line'></span>
                                            <div class='col-3 text-left'>"start_time"
                                            </div>
                                            <div class='col-8 text-right'>
                                                <span class='text-right timer'>
                                                    <i class=' far fa-clock'></i>
                                                     <label id='hr'>00</label>:<label id='min'>00</label>:<label id='sec'>00</label>
                                                 </span>
                                                     <span><span id = 'end_time'>"end_time"</span>
                                                 </span>
                                                     <button class='text-danger btn btn-link' id='stop' onclick='clearTime()'>Stop</button>
                                                 </div>
                                                <div class='col-3 text-left mt-3'>"task_name"
                                                </div>
                                                 </div>
                                             </div>
                                             <div class='card-body text-body ml-4 mt-3'><p>"task_desription"</p></div>
                                                 <div class='card-footer text-black-50 bg-white pl-4 pb-3'><i class='fas fa-user-circle mt-3'></i>Project : "project_name"</div>
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
    <?php include("footer.php"); ?>