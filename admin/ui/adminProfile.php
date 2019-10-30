<?php include("header.php"); ?>
   
    <h1 class="text-center text-white pb-4">My Profile</h1>
    <main class="container-fluid-main">
        <div class="  md main-container-employee text-center">
            <img id="new_img" src="../images/<?=$_SESSION['user_image'];?>" width="10%;" class="rounded-circle figure mt-4" data-toggle="modal" data-target="#changeImage" data-toggle="tooltip" data-placement="top" title="Upload profile picture">
            <div class="container">
                <form method="post" action="//localhost/time_tracker/php/change_pwd.php" id="changePsw" class="offset-md-2 col-md-8">
                    <h5 class="text-center mt-4">Change Password</h5>
                    <div class="form-group">
                        <div class="input-group mt-3 ">
                            <input type="password" class="mb-4 form-control font-weight-light border-top-0 border-left-0 border-right-0" id="oldPsw" name="psw1" placeholder="Enter Old Password">
                        </div>
                        <div class="input-group mt-3 ">
                            <input type="password" class="mb-4 form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter New Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mt-3">
                            <input type="password" class="form-control top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm Password">
                        </div>
                        <p class="text-danger"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-danger" id="alertMsg"></p>
                        <button type="submit" class="btn btn-primary save-task  text-white">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <div class="modal" id="changeImage">
        <div class="modal-dialog animated fadeInDown">
            <div class="modal-content ">
                <div class="modal-header ">Upload image
                    <button type="button" class="close text-danger" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="uploadImage" method="post" enctype="multipart/form-data" action="../php/upload_profile.php">
                        <p><input type="file" name="change_image" placeholder="Upload image" id="image"></p>
                        <p class="text-danger" id="imageErr"></p>
                        <button type="submit" class="btn btn-primary save-task submitImage" >Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
<?php include("footer.php"); ?>