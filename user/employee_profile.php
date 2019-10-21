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
                            <h4 class="text-center employee-name mt-3"><?php echo $_SESSION['user_name'];?></h4>
                        </div>
                        <div class="m-5">
                            <table class="table">
                                  <tbody>
                                    <tr>
                                      <th scope="row">Email</th>
                                      <td><?php echo $_SESSION['user'];?></td>
                                    </tr>
                                    <tr>
                                      <th scope="row"><div id="phone_no">Phone number</div></th>
                                      <td></td>
                                    </tr>
                                    <tr>
                                      <th scope="row"><div id="emp_type">Employee type</div></th>
                                      <td><?php?></td>
                                    </tr>
                                  </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-5">
            <footer>
                <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
            </footer>
        </div>
    </main>
<?php include("footer.php"); ?>