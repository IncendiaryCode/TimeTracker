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
                            <a class="nav-link active text-center" id="daily-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="true">Daily</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-center" id="weekly-tab" data-toggle="tab" href="#weekly-view" role="tab" aria-controls="weekly-view" aria-selected="false">Weekly</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-center" id="monthly-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="false">Monthly</a>
                        </li>
                    </ul>
                    <div class="row">                        
                        <div class="col-12">
                            <!-- Filters -->
                            <div class="filter-parent" aria-labelledby="project-filtering">
                                <span class="alert-filter"></span>
                                <button class="btn project-filtering animated" type="button" id="project-filtering" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                <div class="activity-filter" id="animate-right">
                                    <form action="#" id="activity-filter" class="display-filter">
                                        <div class="row">
                                            <div class="col-12" id="activity-filtering">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h5 class="pb-2">Projects</h5>
                                                        <i class="far fa-times-circle close-filters"></i>
                                                    </div>                                                    
                                                </div>
                                                <?php for($p=0; $p<sizeof($project_list); $p++) { ?>
                                                <div class="form-check custom-control custom-checkbox">
                                                    <input class="form-check-input " type="checkbox" id="activity-check-<?=$p?>">
                                                    <input type="hidden" class="custom-control-input" value=<?=$project_list[$p]['id'] ?>>
                                                    <label class="form-check-label" for="activity-check-<?=$p?>">
                                                        <?=$project_list[$p]['name'] ?>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-12 pt-4 text-left">
                                                <button type="submit" class="btn btn-primary">Apply </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- ./ Filters -->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="daily-view" role="tabpanel" aria-labelledby="daily-tab">
                                    <div class="daily-chart">
                                        <div class="row mt-5">
                                            <div class="col-3 text-right">
                                                <a href="#" class="arrow-style" id="previous-date"><i class="fas fa-angle-left"></i></a>
                                            </div>
                                            <div class="col-6 text-center pl-md-5">
                                                <h5 id="current-date"></h5>
                                                <h6 class="mt-3 mb-1">Duration</h6>
                                                <input type="hidden" class="edit-date" id="daily-chart" data-date-format="YYYY-mm-DD ">
                                                <h4 id="daily-duration">00:00</h4>
                                            </div>
                                            <div class="col-3 text-left">
                                                <a href="#" class="arrow-style" id="next-date"><i class="fas fa-angle-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <p id="daily-error" class="text-center"></p>
                                    <div id="daily">
                                        <div id="print-chart"></div>
                                        <p class="cust_daily_chart" id="cust_daily_chart">
                                            <span id="chart-labels"><span class="">12AM</span>
                                                <!-- <span class="cust_chart">2AM</span> -->
                                                <span class="cust_chart">4AM</span>
                                                <!-- <span class="cust_chart">6AM</span> -->
                                                <span class="cust_chart">8AM</span>
                                                <!-- span class="cust_chart">10AM</span> -->
                                                <span class="cust_chart">12PM</span>
                                                <!-- <span class="cust_chart">2PM</span> -->
                                                <span class="cust_chart">4PM</span>
                                                <!-- <span class="cust_chart">6PM</span> -->
                                                <span class="cust_chart">8PM</span>
                                                <!-- <span class="cust_chart">10PM</span> -->
                                                <span class="cust_chart">12AM</span>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="weekly-view" role="tabpanel" aria-labelledby="weekly-tab">
                                    <div class="daily-chart text-center">
                                        <div class="row mt-5">
                                            <div class="col-3 text-right">
                                                <a href="#" class="arrow-style" id="previous-week"><i class=" fas fa-angle-left"></i></a>
                                            </div>
                                            <div class="col-6 pl-md-5">
                                                <h5 id="current-week"></h5><span id="week_y"></span>
                                                <h6 class="mt-3 mb-1">Duration</h6>
                                                <h4 id="weekly-duration">00:00</h4>
                                                <input type="text" class="form-control " id="weekly-chart">
                                            </div>
                                            <div class="col-3 text-left">
                                                <a href="#" class="arrow-style" id="next-week"><i class="fas fa-angle-right"></i></a>
                                            </div>
                                        </div>
                                        <p id="week-error"></p>
                                        <canvas id="weekly" class="weekly offset-2 col-8"></canvas>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="monthly-view" role="tabpanel" aria-labelledby="monthly-tab">
                                    <div class="monthly-chart">
                                        <div class="row mt-5">
                                            <div class="col-3 text-right">
                                                <a href="javascript:previous()" class="arrow-style" id="previous-year"><i class="fas fa-angle-left"></i></a>
                                            </div>
                                            <div class="col-6 text-center pl-md-5">
                                                <h5 id="current-year"></h5>
                                                <h6 class="mt-3 mb-1">Duration</h6>
                                                <h4 id="monthly-duration">00:00</h4>
                                                <input type="hidden" class="form-control" id="monthly-chart">
                                            </div>
                                            <div class="col-3 text-left">
                                                <a href="javascript:next()" class="arrow-style" id="next-year"><i class="fas fa-angle-right"></i></a>
                                            </div>
                                            <div class="col-md-8 offset-md-2 mt-4">
                                                <div class="card">
                                                    <table class="table text-center" id="calendar">
                                                        <thead>
                                                            <tr>
                                                                <th>Sun</th>
                                                                <th>Mon</th>
                                                                <th>Tue</th>
                                                                <th>Wed</th>
                                                                <th>Thu</th>
                                                                <th>Fri</th>
                                                                <th>Sat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="calendar-body">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <p class="text-center" id="monthly-chart-error"></p>
                                            </div>
                                        </div>
                                    </div>
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
                                <div class="row" id="no-activities">
                                    <div class="col-12 text-center no-activities">
                                        <img src="<?=base_url()?>/assets/images/no-data.png?raw=true" class="img-fluid animated fadeIn">
                                        <h3 class="pb-3">No task available.</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright ©
                <?=date("Y") ?> Printgreener.com</p>
        </footer>
    </div>
</main>