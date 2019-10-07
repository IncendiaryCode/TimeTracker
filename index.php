<?php include("header.php");?>
<body >
        <div class="bg" id="mySlider">
        </div>
        <div id="form1">
            <form id="loginForm"  method="post" action="<?=BASE_URL?>php/login.php" novalidate>
                <div class="text-center"> <img src="<?=BASE_URL?>assets/images/logo.png"></div>
                <div class=" logo-space">
                        <h5 class="text-center">Login</h5>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="Username" name="Username" placeholder="Username">
                    </div>
                    <p class="error" id="Username-error"></p>
                </div>
                <div class="form-group">
                    <div class="input-group ">
                        <input type="password" class="form-control top-space has-empty-validation border-top-0 border-left-0 border-right-0 font-weight-light" id="Password" name="password" placeholder="password" >
                    </div>
                    <p class="error" id="Password-error" ></p>
                </div>
                <div class="row top-space" style="width: 100%;">
                    <a href="#" class="col-6" id="forgot">forgot password?</a>
                    <button type="submit" class=" col-3 offset-3 btn btn-primary" id="submit">Login</button>
                    <div class="error"><?php echo $error;?></div>
                </div>
            </form> 
            
        </div>

        <div id="form2" class="animated fadeInRightBig">
            <form id="forgotPassword" novalidate method="post" action="<?=BASE_URL?>php/check_otp.php" onsubmit="validateOtp()">
                <div class="text-center"> <img src="<?=BASE_URL?>images/logo.png"></div>
                    <div class="logo-space">
                        <h5 class="text-center">Forgot Password</h5>
                </div>
                <div class="form-group">
                      <div class="input-group mb-3 top-space">
                        <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
                        <input type="email" class="form-control  has-email-validation has-empty-validation font-weight-light border-top-0 border-left-0 border-right-0" id="Uname" name="Email" placeholder="Enter email">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="getOTP" onclick="sendOTP()">Send OTP</button>  
                         </div>
                      </div>
                      <p class="error" id="Uname-error"></p>
                      <p style="color: green" class="animated rotateIn" id="rotate-text"></p>
                </div>
                <div class="form-group otp">
                      <div class="input-group mb-3  top-space">
                        <input type="text" class="form-control top-space font-weight-light border-top-0 border-left-0 border-right-0" id="otp1" name="otp" placeholder="Enter OTP">
                       
                      </div>
                      <p class="error" id="here"></p>
                </div>
                  <div class="row top-space" style="width: 100%;">
                    <a href="<?=BASE_URL?>index.php" class="col-6">back to login</a>
                    <button class="btn btn-primary col-3 offset-3" id="count" type="submit">  Submit  </button>    
                </div>
                
            </form> 
        </div>

     <!--   <div id="formPsw" class="animated fadeInRightBig">
            <form id="reEnterPsw" novalidate method="post" action="">
                <div class="text-center"> <img src="images/logo.png"></div>
                    <div class=" logo-space">
                        <h5 class="text-center">Change Password</h5>
                </div>
                <div class="form-group">
                      <div class="input-group mb-3 top-space">
                        <input type="password" class="form-control font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter new password">
                      </div>
                </div>
                <div class="form-group otp">
                      <div class="input-group mb-3  top-space">
                        <input type="password" class="form-control top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm password">
                </div>
                <p class="error" id="cnfrmPsw"></p>
            </div>
                <div class="row top-space" style="width: 100%;">
                    <a href="index.php" class="col-6">back to login</a>
                    <button type="submit" class="col-3 offset-3 btn btn-primary" id="count">Submit</button>
                </div>
            </form> </div>
            <div id="success" class="animated bounce">
            
               <p class="text-center">success</p>
                
        </div>  --> 
        
<?php include("footer.php");?>
