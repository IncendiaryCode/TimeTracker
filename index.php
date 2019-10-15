<?php include("header.php");?>
<body>
        <div class="bg  col-md-6" id="mySlider"></div>
        <div class="col-md-6 positioning-logo text-center"><img src="<?=BASE_URL?>assets/images/logo.png"></div>
        <div id="form1">
            <form id="loginForm" class="login-form"  method="post" action="<?=BASE_URL?>php/login.php" novalidate>
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control-file logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="Username" name="Username" placeholder="Username">
                    </div>
                    <p class="error" id="Username-error"></p>
                </div>
                <div class="form-group">
                    <div class="input-group ">
                        <input type="password" class="form-control-file top-space has-empty-validation border-top-0 border-left-0 border-right-0 font-weight-light" id="Password" name="password" placeholder="password" >
                    </div>
                    <p class="error" id="Password-error" ></p>
                </div>
                <div class="row top-space" style="width: 100%;">
                    <a href="#" class="col-6 forgot-color" id="forgot">forgot password?</a>

                    <button type="submit" class=" col-3 offset-3 login-color btn btn-primary" id="submit">Login</button>
                    <div class="error"><?php echo isset($error) ? $error : ''?></div>
                </div>
            </form>
        </div>

        <div id="form2" class="animated fadeInRightBig">
            <form id="forgotPassword" method="post" class="login-form" action="<?=BASE_URL?>php/check_otp.php" onsubmit="validateOtp()" novalidate>
                
                    <div class="logo-space">
                        <h5 class="text-center">Forgot Password</h5>
                        <p class="error" id="here"></p>
                          <p class="error" id="Uname-error"></p>
                          <p style="color: green" class="animated rotateIn" id="rotate-text"></p>
                    </div>

                    <div class="form-group">
                          <div class="input-group mb-3 top-space">
                            <input id="user_id" name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'];?>">
                            <input type="email" class="form-control-file  has-email-validation has-empty-validation font-weight-light border-top-0 border-left-0 border-right-0" id="Uname" name="email" placeholder="Enter email">
                            
                            
                          </div>
                              <a href="#"  class="text-left btn btn-link forgot-color p-0" id="getOTP" onclick="sendOTP()">Send OTP</a>  
                    </div>
                    <div class="form-group otp">
                          <div class="input-group mb-3  top-space">
                            <input type="text" class="form-control-file top-space font-weight-light border-top-0 border-left-0 border-right-0" id="otp1" name="otp" placeholder="Enter OTP">
                          </div>
                          
                    </div>
                      <div class="row top-space" style="width: 100%;">
                        <a href="<?=BASE_URL?>index.php" class="col-6 text-left forgot-color"><i class="fas fa-arrow-left"> back to login</i></a>
                        <button class="btn btn-primary col-3 offset-3 login-color" id="count" type="submit">  Submit  </button>    
                    </div>              
            </form> 
        </div>

        <div id="formPsw" class="animated fadeInRightBig">
            <form id="reEnterPsw" method="post" class="login-form" action="<?=BASE_URL?>php/change_pwd.php" novalidate>
                <div class="text-center">
                    <div class=" logo-space">
                        <h5 class="text-center">Change Password</h5>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control-file logo-space has-email-validation has-empty-validation  border-top-0 border-left-0 border-right-0 space-top font-weight-light" id="Username" name="Username" placeholder="Username">
                    </div>
                    <p class="error" id="Username-error"></p>
                </div>
                <div class="form-group">
                      <div class="input-group mb-3 top-space">
                        <input type="password" class="form-control-file font-weight-light border-top-0 border-left-0 border-right-0" id="psw1" name="psw11" placeholder="Enter new password">
                      </div>
                </div>
                <div class="form-group otp">
                      <div class="input-group mb-3  top-space">
                        <input type="password" class="form-control-file top-space font-weight-light border-top-0 border-left-0 border-right-0" id="psw2" name="psw22" placeholder="Confirm password">
                    </div>
                </div>
                <p class="error" id="cnfrmPsw"></p>
            
                <div class="row top-space" style="width: 100%;">
                    <a href="index.php" class="col-6 forgot-color"><i class="fas fa-arrow-left"> back to login</i></a>
                    <button type="submit" class="col-3 offset-3 btn btn-primary login-color" id="count">Submit</button>
                </div>
            </form>
        </div>
        
<?php include("footer.php");?>
