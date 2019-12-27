<?php
    $GLOBALS['page_title'] = 'Login Activities';
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->library('session');
    $profile = $this->session->userdata('user_profile');
?>
<main class="container-fluid container-fluid-main"><p class="display-4 text-white  text-center">My activities</p>
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
                                        <input type="text" class="form-control edit-date" id="daily-chart" data-date-format="YYYY-mm-DD ">
                                        <button class="btn btn-primary" onclick="loadDailyChart()">view chart</button>
                                    </div>
                                </div>
                            </div>
                            <p id="daily-error"class="text-center"></p>
                            <div id="daily">
                                <p class="cust_daily_chart" id="cust_daily_chart">
                                    <span class="">8AM</span>
                                    <span class="cust_chart">9AM</span>
                                    <span class="cust_chart">10AM</span>
                                    <span class="cust_chart">11AM</span>            
                                    <span class="cust_chart">12AM</span>
                                    <span class="cust_chart">1PM</span>
                                    <span class="cust_chart">2PM</span>
                                    <span class="cust_chart">3PM</span>
                                    <span class="cust_chart">4PM</span>
                                    <span class="cust_chart">5PM</span>
                                    <span class="cust_chart">6PM</span>
                                    <span class="cust_chart">7PM</span>
                                    <span class="cust_chart">8PM</span>
                                </p>
                                <p id="print-chart"></p>
                            </div>
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
                                <p id="week-error"class="text-center"></p>
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
                            <div class="pl-2" id="calendar_basic"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="au-task js-list-load">
                <div class="au-task-list js-scrollbar3">
                    <div class="au-task__item au-task__item--danger">
                        <div class="row au-task__item-inner attach  m-1 pt-4" id="attachPanels">
                            <!-- Loading in js-->                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div>
        <footer class="text-center">
            <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>