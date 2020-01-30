<?php
    $GLOBALS['page_title'] = 'My activities';
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner mt-3">
            <div class="au-card au-card--no-shadow au-card--no-pad mb-2">
                <div class="au-card-title pt-5">
                    <ul class="nav nav-tabs" id="chart-navigation" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="daily-view-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="true">Daily</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="weekly-view-tab" data-toggle="tab" href="#weekly-view" role="tab" aria-controls="weekly-view" aria-selected="false">Weekly</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="monthly-view-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="false">Monthly</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="daily-view" role="tabpanel" aria-labelledby="daily-view-tab">
                            <div class="daily-chart">
                                <div class="row mt-5">
                                    <div class="col-3 text-center">
                                        <a href="#" id="previous-date"><h1 ><i class="fas fa-angle-left"></i></h1></a>


                                    </div>
                                    <div class="col-6 text-center">
                                        <h5 id="current-date"></h5>
                                        <h6 class="mt-3 mb-1">Duration</h6>
                                        <input type="hidden" class="form-control- edit-date" id="daily-chart" data-date-format="YYYY-mm-DD ">
                                        <h4 id="daily-duration">10:15</h4>
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="#" id="next-date"><h1><i class="fas fa-angle-right"></i></h1></a>
                                        <!-- <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div> -->
                                        
                                    </div>
                                    <!-- <div class="col-6 text-right">
                                        <div class="input-group">
                                            <label for="end-date">Select date</label>
                                            <div class="input-group ">
                                            <div class="input-group mb-3">
                                                  <input type="hidden-" class="form-control- edit-date" id="daily-chart" data-date-format="YYYY-mm-DD ">
                                                  <div class="input-group-append">
                                                    <button class="btn fa fa-calendar edit-date" type="button"></button>
                                                  </div>
                                            </div>

                                            </div>


                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <p id="daily-error"class="text-center"></p>
                            <div id="daily">
                                <div><p id="print-chart"></p></div>
                                <p class="cust_daily_chart" id="cust_daily_chart"><hr>
                                <span id="chart-labels"><span class="">8AM</span>
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
                                </span>
                                </p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="weekly-view" role="tabpanel" aria-labelledby="weekly-view-tab">
                            <div class="daily-chart">
                                <div class="row mt-5">
                                    <div class="col-3 text-center">
                                        <a href="#" id="previous-week"><h1 ><i class="fas fa-angle-left"></i></h1></a>
                                    </div>
                                    <div class="col-6 text-center">
                                        <h5 id="current-week"></h5>
                                        <h6 class="mt-3 mb-1">Duration</h6>
                                        <h4 id="weekly-duration">35:12</h4>
                                        <input type="week" class="form-control " id="weekly-chart">
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="#" id="next-week"><h1><i class="fas fa-angle-right"></i></h1></a>
                                    </div>
                                </div>
                                <p id="week-error"class="text-center"></p>
                                <canvas id="weekly" class="offset-2 col-8" style="width:1200px; height:350px;"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="monthly-view" role="tabpanel" aria-labelledby="monthly-view-tab">
                            <div class="monthly-chart">
                                <div class="row mt-5">
                                    <div class="col-3 text-center">
                                        <a href="#" id="previous-year"><h1 ><i class="fas fa-angle-left"></i></h1></a>
                                    </div>
                                    <div class="col-6 text-center">
                                        <h5 id="current-year"></h5>
                                        <h6 class="mt-3 mb-1">Duration</h6>
                                        <h4 id="daily-duration">135:12</h4>
                                        <input type="number" class="form-control" id="monthly-chart">
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="#" id="next-year"><h1><i class="fas fa-angle-right"></i></h1></a>
                                    </div>
                                </div>
                            </div>
                            <div class="pl-2" id="calendar_basic"></div><p class="text-center" id="monthly-chart-error"></p>
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
        <div>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright © 2020 Printgreener.com</p>
        </footer>
    </div>
</main>