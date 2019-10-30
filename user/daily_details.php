<?php
$GLOBALS['page_title'] = 'Daily activities';
include("header.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="daily-chart">
                <div class="form-group">
                    <label for="end-date">Select date</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="daily-chart">
                        <button class="btn btn-primary" onclick="dailyChart()">view chart</button>
                    </div>
                </div>
                <canvas id="daily"></canvas>
            </div>
        </div>
    <hr class="mt-5">
    <footer>
        <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
    </footer>
    </div>
</main>

<?php include("footer.php"); ?>