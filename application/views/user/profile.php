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
                <form method="post" action="<?= base_url(); ?>user/edit_profile" id="edit-profile" enctype="multipart/form-data">
                    <div class="row pt-5 pb-5">
                        <div class="col-md-2 offset-md-2 col-6 offset-3 text-center">
                            <label class="cabinet center-block">
                                <figure>
                                    <img src="<?=base_url().USER_UPLOAD_PATH.$res['profile'];?>" class="rounded-circle text-center gambar img-thumbnail-" id="item-img-output" width="200px" height="200px"  />
                                    <figcaption><i class="fa fa-camera img-icon"></i></figcaption>
                                </figure>
                                <input type="file" class="item-img file center-block" name="change_img" id = "change-img" />
                                <input type="hidden" name="croped_pointed" id = "cropped-points" />
                            </label>                          
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
                                <div class="col-4 pt-3">
                                    <a class=" m-0" href="<?=base_url();?>index.php/user/change_password">Change password</a>
                                </div>
                                <div class="col-6 pt-3">
                                    <p id="profile-error" class="text-danger"></p>  
                                </div>
                                <div class="col-2 pt-3">
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
                            <h1 class="text-center mt-4"><?php echo round($res['t_minutes'], 0);?><span>h</span></h1>
                            <p class="text-center">Total working hours for this month</p>
                        </div>
                    </div>
                </div>
                <p class="vl"></p>
                <div class="col-5 col-md-4 ">
                    <div class="card user-card ">
                        <div class="card-body">
                            <h1 class="text-center mt-4"><?php echo round($res['total_time'], 0);?><span>h</span></h1>
                            <p class="text-center">Total working hours</p>
                        </div>
                    </div>
                </div>
            </div><hr>
            <div class="row profile-chart">
            <div class="col-md-6 offset-md-1 col-12 text-left">
                <span class="text-right input-group col-md-8-" id = "year-picker">
                    <div class="input-group date">Time spent for the year
                        <input readonly="" type="text" class="ml-2 mb-2 p-0 datepicker year-chart border-0 text-primary" id="year-chart" max =<?=date('YYYY');?> >
                        <div class="input-group-append">
                            <span>
                                <i type="button" class="btn p-0 fas fa-angle-down text-primary"></i>
                            </span>
                        </div>
                    </div>
                </span>
            </div>
            <div class="col-md-10 offset-md-1">
                <canvas id="user_prof_chart" height="100px;" class="mb-5"></canvas>
                <div class="col-12  text-center profile-chart-nodata">
                    <img src="http://www.timetracker.com//assets/images/no-data.png?raw=true" class="img-fluid animated fadeIn">
                </div> 
                    <h5  class="text-center font-weight-normal" id="profile-chart-error"></h5>
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
            <p class="text-center pt-2 ">Copyright ©  <?=date("Y") ?> | TimeTracker.com</p>
        </footer>
    </div>


</main>