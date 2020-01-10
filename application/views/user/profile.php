<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'My profile';
$this->load->helper('url_helper');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row mt-5">
                <div class="col-12 mt-5">
                    <form method="post" action="<?=base_url();?>index.php/user/dark" id="dark-mode">
                        <div class="dropdown text-right" id="dropdown-recent-acts">
                            <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                                <div class="dropdown-item checkbox"><input type="hidden" name="status" id="hidden-status"><input type="checkbox" id="dark-mode-checkbox" name="dark-mode"> Dark mode</div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-4 offset-md-2 text-center">
                    <div>
                        <img src="<?=base_url().USER_UPLOAD_PATH.$res['profile'];?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        <h4 class="employee-name mt-3">
                            <?php echo $res['name'];?>
                        </h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <table class="table mt-5">
                        <tbody id="table-body">
                            <table>
                                <tbody>
                                    <tr>
                                        <th width="50%" height="40px;">Email :</th>
                                        <td width="50%" height="40px;">
                                            <?=$res['email'];?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="50%" height="40px;">Phone number:</th>
                                        <td width="50%" height="40px;">
                                            <?php echo ($res['phone'] != 0)?$res['phone']:'';?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?=base_url();?>index.php/user/change_password">Change password</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </tbody>
                    </table>
                </div>
                
            </div><hr>
            <div class="row shadow-lg">
                <div class="col-6 col-md-4 offset-md-2">
                    <div class="card user-card ">
                        <div class="card-body">
                            <h6 class="text-center">Total working hours for this month</h6>
                            <p class="text-center mt-4"><?php echo $res['t_minutes'];?> hrs</p>
                        </div>
                    </div>
                </div>
                <p class="vl"></p>
                <div class="col-5 col-md-4 ">
                    <div class="card user-card ">
                        <div class="card-body">
                            <h6 class="text-center">Total working hours</h6>
                            <p class="text-center mt-4"><?php echo $res['total_time'];?> hrs</p>
                        </div>
                    </div>
                </div>
            </div><hr>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                    <div class="input-group col-md-3 offset-md-9 pt-4 text-right">
                        <!-- chart that shows monthly activities -->
                        <input type="number" class="" id="year-chart">
                    </div>
                </div>
                </div>
                    <canvas id="user_prof_chart" height="80px;"></canvas>
                    <p class="text-center" id="profile-chart-error"></p>
                </div>
            </div>
        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <?php
        if(!empty($GLOBALS['dark_mode'])){
    if ($GLOBALS['dark_mode'] == 1) { ?>
        <script type="text/javascript">
        $('#dark-mode-checkbox').attr("checked", "checked");
        </script>
        <?php }  else { ?>
        <script type="text/javascript">
        $('#dark-mode-checkbox').removeAttr("checked");
        </script>
        <?php }} ?>
        <footer class="footer">
            <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>