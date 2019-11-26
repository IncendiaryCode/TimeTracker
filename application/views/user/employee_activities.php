<?php
    $GLOBALS['page_title'] = 'Login Activities';
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->library('session');
    $profile = $this->session->userdata('user_profile');
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner mt-3">
            <div class="au-card au-card--no-shadow au-card--no-pad mb-2">
                <div class="au-card-title pt-5">
                    <ul class="nav nav-tabs" id="chart-navigation" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="daily-view-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="true">Daily activities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="weekly-view-tab" data-toggle="tab" href="#weekly-view" role="tab" aria-controls="weekly-view" aria-selected="false">Weekly activities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="monthly-view-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="false">Monthly activities</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="daily-view" role="tabpanel" aria-labelledby="daily-view-tab">
                            <div class="daily-chart">
                                <div class="form-group m-5">
                                    <label for="end-date">Select date</label>
                                    <div class="input-group">
                                        <!-- chart that shows daily activities -->
                                        <input type="date" class="form-control" id="daily-chart">
                                        <button class="btn btn-primary" onclick="loadDailyChart()">view chart</button>
                                    </div>
                                </div>
                            </div>
                            <canvas id="daily" style="width:1000px; height:80px;"></canvas>
                        </div>
                        <div class="tab-pane fade" id="weekly-view" role="tabpanel" aria-labelledby="weekly-view-tab">
                            <div class="daily-chart">
                                <div class="form-group m-5">
                                    <label for="end-date">Select week</label>
                                    <div class="input-group">
                                        <input type="week" class="form-control " id="weekly-chart">
                                        <button class="btn btn-primary" onclick="loadWeeklyChart()">view chart</button>
                                    </div>
                                </div>
                                <canvas id="weekly" class="offset-2 col-8" style="width:1200px; height:350px;"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="monthly-view" role="tabpanel" aria-labelledby="monthly-view-tab">
                            <div class="daily-chart">
                                <div class="form-group m-5">
                                    <label for="end-date">Select date</label>
                                    <div class="input-group">
                                        <!-- chart that shows monthly activities -->
                                        <input type="number" class="form-control" id="monthly-chart">
                                        <button class="btn btn-primary" onclick="loadMonthlyChart()">view chart</button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5" id="calendar_basic"></div>
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
        <hr>
        <footer>
            <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>