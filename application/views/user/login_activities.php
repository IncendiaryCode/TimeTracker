<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'My profile';
$this->load->helper('url_helper');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">

            <!-- <div class="row profile-chart">
                <div class="col-md-6 offset-md-1 col-12 text-left">
                    <span class="text-right input-group col-md-3 offset-md-9" id = "year-picker">
                        <div class="input-group date">Time spent for the month
                            <input readonly="" type="text" class="ml-2 mb-2 p-0 datepicker year-chart border-0 text-primary" id="year-chart" max =<?=date('YYYY');?> >
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i type="button" class="btn p-0 fas fa-angle-down text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </span>
                </div>
                <div class="col-md-10 offset-md-1">
                    <canvas id="user_prof_chart" height="100px;" class="mb-5"></canvas>
                    <div class="col-12  text-center profile-chart-nodata">
                        <img src="http://www.timetracker.com//assets/images/no-data.png?raw=true" class="img-fluid animated fadeIn">
                    </div> 
                    <h5  class="text-center font-weight-normal" id="profile-chart-error"></h5>
                </div>
            </div> -->
            <div class="row pt-5">
                <div class = "col-12">
                    <div class="row" id="login-date-filter">
                        <div class="col-6 text-left">
                            <h4>List of login activities</h4>
                        </div>
                        <div col-6 text-right>
                            <div class="row">
                            <div class="col-5  offset-md-1">
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" id="dateStart" name="dateStart" data-date-format="yyyy-mm-dd" >
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <button type="button" class="btn fa fa-calendar p-0"></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span>To</span>
                        <div class="col-5">
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" id="dateEnd" name="dateEnd" data-date-format="yyyy-mm-dd" >
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <button type="button" class="btn fa fa-calendar p-0"></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <p class="text-danger" id="start-time-error"></p>
                <table id="login-lists-datatable" class="table table-striped table-bordered">
                    <thead style="width:100%">
                        <tr>
                            <th>Date</th>
                            <th>Logged in at</th>
                            <th>Logged out at</th>
                            <th>Time spent</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <p class="text-center" id = "login-tabel-error"></p>
        </div>
    </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <?php
        if(!empty($GLOBALS['dark_mode'])){
    if ($GLOBALS['dark_mode'] == 1) { ?>
        <script type="text/javascript">
        $('#dark-mode-checkbox').attr("checked", "checked");
        </script>
        <?php }  else { ?>
        <script type="text/javascript">
        $('#dark-mode-checkbox').removeAttr("checked");
        </script>
        <?php }} ?>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright ©  <?=date("Y") ?> | TimeTracker.com</p>
        </footer>
    </div>


</main>