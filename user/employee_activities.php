<?php 
$GLOBALS['page_title'] = 'Login Activities';
include("header.php");
#include("../php/login_activities.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner mt-3">
            <div class="au-card au-card--no-shadow au-card--no-pad mb-2">
                <div class="au-card-title pt-5">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Daily activitie</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Weekly activities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Monthly activities</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="daily-chart">
                                <div class="form-group">
                                    <label for="end-date">Select date</label>
                                    <div class="input-group">
                                        <!-- chart that shows daily activities -->
                                        <input type="date" class="form-control" id="daily-chart">
                                        <button class="btn btn-primary" onclick="dailyChart()">view chart</button>
                                    </div>
                                </div>
                                <canvas id="daily"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="daily-chart">
                                <div class="form-group">
                                    <label for="end-date">Select week</label>
                                    <div class="input-group">
                                        <input type="week" class="form-control " id="weekly-chart">
                                        <button class="btn btn-primary" onclick="weeklyChart()">view chart</button>
                                    </div>
                                </div>
                                <canvas id="weekly"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="p-5" id="calendar_basic">
                            </div>
                            <div class="clear">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="au-task js-list-load">
                <div class="au-task-list js-scrollbar3">
                    <div class="au-task__item au-task__item--danger">
                        <div class="row au-task__item-inner attach  m-1" id="attachPanels">
                            <!-- Loading in js-->
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
