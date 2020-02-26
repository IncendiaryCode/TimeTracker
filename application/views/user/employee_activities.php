<?php
    $GLOBALS['page_title'] = 'My activities';
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner mt-3">
            <div class="au-card au-card--no-shadow au-card--no-pad mb-2">
            <div class="row pt-4">
                <div class="col-12 text-right">
                    <ul class="nav float-right">
                        <li class="nav-item " id="activity-clear-filter">
                            <a class="nav-link pt-0" href="#"><i class="far fa-times-circle"></i> Clear filter</a>
                        </li>                       
                        <!-- <li class="nav-item">
                            <a class="nav-link pt-0" id="activity-action-filter" href="#navBarActivityFilter" data-toggle="collapse" aria-controls="navBarActivityFilter" aria-expanded="false" aria-label="Toggle Filter">
                                <i class="fas fa-sliders-h"></i>
                            </a>
                        </li> -->
                    </ul>                    
                </div>
                <div class="col-12">
                        <div class="collapse" id="navBarActivityFilter">
                            <div class="bg-light- p-4">
                                <form action="#" id="activity-filter">
                                    <div class="row">
                                        <div class="col-4" id="activity-sorting">
                                            <h5 class="pb-2">Sort by</h5>
                                            <div class="form-check">
                                                <input class="form-check-input" data-type="name" type="radio" name="exampleRadios" id="activity-date-radio" value="date" checked>
                                                <label class="form-check-label" for="date-radio">
                                                    Date
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" data-type="name" type="radio" name="exampleRadios" id="activity-task-radio" value="task">
                                                <label class="form-check-label" for="task-radio">
                                                    Task
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" data-type="project" type="radio" name="exampleRadios" id="activity-project-radio" value="project">
                                                <label class="form-check-label" for="project-radio">
                                                    Project
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" data-type="duration" type="radio" name="exampleRadios" id="activity-duration-radio" value="duration">
                                                <label class="form-check-label" for="duration-radio">
                                                    Duration
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4" id="activity-filtering">
                                            <h5 class="pb-2">Filter by</h5>
                                            <?php
                                                for($p=0; $p<sizeof($project_list); $p++) { ?>                                        
                                                <div class="form-check custom-control custom-checkbox">
                                                    <input class="form-check-input " type="checkbox" id="activity-check-<?=$p?>">
                                                        <input type="hidden" class="custom-control-input" value=<?=$project_list[$p]['id'] ?>>
                                                        <label class="form-check-label" for="activity-check-<?=$p?>">
                                                        <?=$project_list[$p]['name'] ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-4 text-right">
                                            <button type="submit" class="btn btn-primary">Apply </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <p id="alarmmsg" class="text-center"></p>
                </div>
                </div>
                <div class="au-card-title pt-5">

                    <ul class="nav nav-tabs" id="chart-navigation" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-center" id="daily-view-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="true">Daily</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-center" id="weekly-view-tab" data-toggle="tab" href="#weekly-view" role="tab" aria-controls="weekly-view" aria-selected="false">Weekly</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-center" id="monthly-view-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="false">Monthly</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="daily-view" role="tabpanel" aria-labelledby="daily-view-tab">
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
                                    <div class="col-12">
                                        <div class="task-detail" id="task-detail"></div>
                                    </div>
                                </div>
                            </div>
                            <p id="daily-error"class="text-center"></p>
                            <div id="daily">
                                <div id="print-chart"></div>
                                <p class="cust_daily_chart" id="cust_daily_chart">
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
                                <canvas id="weekly" class="offset-2 col-8"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="monthly-view" role="tabpanel" aria-labelledby="monthly-view-tab">
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
                    </div>
                </div>
            </div>
        </div>
        <div>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright © <?=date("Y") ?> Printgreener.com</p>
        </footer>
    </div>
</main>