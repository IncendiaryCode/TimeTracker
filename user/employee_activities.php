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
                        <div class="bg-overlay bg-overlay--blue"></div>
                        <h3>
                            <i class="zmdi zmdi-account-calendar"></i>
                        </h3>
                    </div>
                    <div class="au-task js-list-load">
                        <div class="au-task-list js-scrollbar3">
                            <div class="au-task__item au-task__item--danger">
                                <div class="row au-task__item-inner attach  m-1" id="attachPanels" >
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
