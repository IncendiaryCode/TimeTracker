<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'My profile';
$this->load->helper('url_helper');
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row pt-5">
                <div class="col-12">
            <?php if (!empty($this->session->flashdata('failure'))) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    echo (!empty($this->session->flashdata('failure'))) ? $this->session->flashdata('failure') : '';
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                <?php if (!empty($this->session->flashdata('success'))) { ?>
                    <div class="alert alert-success mb-5">
                        <?php echo (!empty($this->session->flashdata('success'))) ? $this->session->flashdata('success') : ''; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>
            </div>
            </div>

                <!-- <div class="col-12 mt-5">
                    <form method="post" action="<?=base_url();?>index.php/user/dark" id="dark-mode">
                        <div class="dropdown text-right" id="dropdown-recent-acts">
                            <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                                <div class="dropdown-item checkbox"><input type="hidden" name="status" id="hidden-status"><input type="checkbox" id="dark-mode-checkbox" name="dark-mode"> Dark mode</div>
                            </div>
                        </div>
                    </form>
                </div> -->
                <form method="post" action="<?= base_url(); ?>user/edit_profile" id="edit-profile">
                    <div class="row pt-5">
                        <div class="col-md-2 offset-md-2 col-6 offset-3 text-center">
                            <img src="<?=base_url().USER_UPLOAD_PATH.$res['profile'];?>"  class="rounded-circle text-center" width="200px;" height="200px;">
                            <div class="edit">
                                <div class="img-icon">
                                    <a href="#" class="text-white"><i class="change-image fas fa-camera" data-toggle="modal" data-target="#changeimage"></i></a>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-6 offset-md-2">
                            <div class="row mr-5">
                                <div class="col-4">
                                    <p class="profile-entry">Name</p>
                                </div>
                                <div class="col-8">
                                    <h4 class="employee-name">
                                        <input type="text" class="form-control-file border-top-0 border-left-0 border-right-0" name="profile-name" id="profile-name" value = "<?php echo $res['name'];?>">
                                    </h4>  
                                </div>
                                <div class="col-4 pt-3">
                                    <p class="profile-entry">Email</p>
                                </div>
                                <div class="col-8 pt-3">
                                    <?=$res['email'];?>
                                </div>
                                <div class="col-4 pt-3">
                                    <p class="profile-entry">Mobile</p>
                                </div>
                                <div class="col-8 pt-3">
                                        <input type="text" class="form-control-file  border-top-0 border-left-0 border-right-0" name="profile-ph" id="profile-ph" value = "<?php echo ($res['phone'] != 0)?$res['phone']:'';?>">
                                </div>
                                <div class="col-4">
                                    <a href="<?=base_url();?>index.php/user/change_password">Change password</a>
                                </div>
                                <div class="col-6">
                                    <p id="profile-error" class="text-danger"></p>  
                                </div>
                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary login-color" id="save-profile">Save</button>
                                </div>
                            </div>                               
                            </div>
                    </div>
                </form>
            
            <hr>
            <div class="row shadow-lg">
                <div class="col-6 col-md-4 offset-md-2">
                    <div class="card user-card ">
                        <div class="card-body">
                            <h1 class="text-center mt-4"><?php echo round($res['t_minutes'], 0);?></h1>
                            <p class="text-center">Total working hours for this month</p>
                        </div>
                    </div>
                </div>
                <p class="vl"></p>
                <div class="col-5 col-md-4 ">
                    <div class="card user-card ">
                        <div class="card-body">
                            <h1 class="text-center mt-4"><?php echo round($res['total_time'], 0);?></h1>
                            <p class="text-center">Total working hours</p>
                        </div>
                    </div>
                </div>
            </div><hr>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                    <div class="text-right input-group col-md-3 offset-md-9 pt-4 ">
                        <!-- chart that shows monthly activities -->
                        <input type="number" class="" id="year-chart">
                    </div>
                </div>
                </div>
                    <div class="col-md-10 offset-md-1">
                        <canvas id="user_prof_chart" height="100px;" class="mb-5"></canvas>
                        <p class="text-center" id="profile-chart-error"></p>
                    </div>
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
            <p class="text-center pt-2 ">Copyright © 2020 Printgreener.com</p>
        </footer>
    </div>
</main>