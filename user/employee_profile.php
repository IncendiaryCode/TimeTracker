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
                        <div class="dropdown">
                            <!-- calander view of all activites. -->
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My activities
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a href="../user/daily_details.php" class="nav-link"  role="tab" aria-controls="pills-home" aria-selected="true">Daily activities</a>
                    <a href="../user/weekly_details.php" class="nav-link" role="tab" aria-controls="pills-profile" aria-selected="false">Weekly activities</a>
                    <a href="../user/monthly_details.php" class="nav-link" role="tab" aria-controls="pills-contact" aria-selected="false">Monthly activities</a>
                  </div>
                </div>
                    </div>
                </div>
            </div>
            <div class="offset-4 col-4 pl-5">
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