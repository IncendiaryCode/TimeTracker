<?php
$GLOBALS['page_title'] = 'My profile';
include("header.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row mt-5">
                <div class="col-6 offset-3">
                    <div class="text-center mt-4">
                        <img src="<?=BASE_URL?>assets/images/user_profiles/<?=$_SESSION['user_image'];?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        <h4 class="text-center employee-name mt-3">
                            <?php echo $_SESSION['user_name'];?>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="offset-4 col-4 pl-5">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a href="../user/daily_details.php" class="nav-link active"  role="tab" aria-controls="pills-home" aria-selected="true">Daily</a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/weekly_details.php" class="nav-link" role="tab" aria-controls="pills-profile" aria-selected="false">Weekly</a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/monthly_details.php" class="nav-link" role="tab" aria-controls="pills-contact" aria-selected="false">Monthly</a>
                    </li>
                </ul>
                <table class="table mt-5">
                    <tbody id="table-body">
                    </tbody>
                </table>
            </div>
          </div>

            <hr class="mt-5">
            <footer>
                <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
            </footer>
        </div>
</main>
<?php include("footer.php"); ?>