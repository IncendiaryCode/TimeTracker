<?php
$GLOBALS['page_title'] = 'My Profile';
include("header.php");
?>
    <main class="container-fluid container-fluid-main">
        <div class=" main-container">
            <div class="main-container-inner">
                <div class="row mt-5">
                    <div class="col-6 offset-3">
                        <div class="text-center mt-4">
<<<<<<< HEAD
                            <img src="<?=BASE_URL?>assets/images/user_profiles/<?=$_SESSION['user_image'];?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        </div>
                        <form action="<?=BASE_URL?>php/change_pwd.php" class="mt-4" id="myProfile" method="post">
=======
                            <img src="<?=BASE_URL?>assets/images/user_profiles/icons8-virtualbox-48.png" width="30%;" class="rounded-circle figure mt-4 text-center">
                        </div>
                        <form action="<?=BASE_URL?>/user/home.php" class="mt-4" id="myProfile">
>>>>>>> 8f08aeaa5781360dc38d4f4cb6e97c2997478051
                            <div class="form-group">
                                <div class="input-group mb-3 ">
                                    <input type="password" class="mb-4 form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="oldPsw" name="psw1" placeholder="Enter Old Password">
                                </div>
                                <div class="input-group mb-3 ">
                                    <input type="password" class="mb-4 form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter New Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control-file top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm Password">
                                </div>
                                <p class="text-danger"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-danger" id="alertMsg"></p>
                                <button type="submit" class="btn save-task  text-white">Submit</button>
                            </div>
                        </form>
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