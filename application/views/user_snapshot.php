<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#" class="text-white previous"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <a href="#" class="text-white"><img src="<?=base_url().UPLOAD_PATH.$profile;?>" height="40px" class="rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display"> Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display"> <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
<p class="display-heading text-primary text-center">User snapshot</p>
<div class="container">
    <div class="text-right mt-5">
        <select class="project-names" id="project-list">
            <option>All projects</option>
        </select>
    </div> 
    <div class="row mt-5">
        <div class="col-12">
            <canvas id="user-chart" height="80px;"></canvas>
            <p id="user-chart-error" class="text-center"></p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-2">
            <p><strong>User name</strong></p>
        </div>
        <div class="col-6 ">
            <p><strong>Project name</strong></p>
        </div>
        <div class="col-2">
            <p><strong>Time spent</strong></p>
        </div>
        <div class="col-2">
            <p><strong>Action</strong></p>
        </div>
    </div>
    <hr>
    <div>
        <?php
            foreach ($data as $k) {
            ?>
            <div class="row pt-3">
                <div class="col-2">                    
                    <a href="<?=base_url();?>index.php/admin/load_userdetails_page?user_id=<?=$k['user_id'] ?>" class=" " id="<?=$k['user_name']?>">
                        <?=$k['user_name'];?>
                    </a>
                </div>
                <div class="col-6">
                    <?php
                    foreach ($k['project'] as $d) { 
                        ?>                
                    <a href="<?=base_url();?>index.php/admin/load_project_detail?project_id=<?=$d['project_id'] ?>" class=""><div class="mr-2">
                        <?php
                        if($d['image_name'] != ''){
                            ?>
                            <img src="<?=base_url().UPLOAD_PATH.$d['image_name']?>" height="15px;" width="18px;">
                            <?php
                        } echo $d['project_name']; ?></div>
                        
                    </a>
                <?php }  ?>
            </div>
            
            <div  class="col-2">
                <p><?=round(($k['total_minutes']/60),2);?> hrs</p>
            </div>
        <div class="col-2">
            <div class="row remove-user">
                <i class="fas fa-trash-alt icon-plus icon-remove text-danger" data-toggle="modal" data-target="#delete-entry"><input type="hidden" name=""  class="user_id" value="<?=$k['user_id'];?>"></i>
            </div>
        </div>
    </div>
    <hr>
        <?php } ?>
    </div>
</div>

<div class="modal" id="delete-entry" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header ">
                <span>Do you want to delete? </span>
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-secondary" id="cancel-delete" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="delete-user" >Yes</button>
            </div>
        </div>
    </div>
</div>

<footer class="">
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>
