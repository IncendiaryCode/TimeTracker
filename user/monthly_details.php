<?php
$GLOBALS['page_title'] = 'Monthly activities';
include("header.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div>
                <!-- Green theme -->
               <div class="auto-jsCalendar material-theme green" id="calendar" ></div>
            </div>
            <div class="clear"></div>
        </div>
        <hr class="mt-5">
        <footer>
            <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
<?php include("footer.php"); ?>
