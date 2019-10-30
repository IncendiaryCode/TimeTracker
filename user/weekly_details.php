<?php
$GLOBALS['page_title'] = 'Weekly activities';
include("header.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
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
    <hr class="mt-5">
    <footer>
        <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
    </footer>
    </div>
</main>

<?php include("footer.php"); ?>