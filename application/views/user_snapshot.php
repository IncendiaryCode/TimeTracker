<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="container">    
    <div class="row mt-5">
        <div class="col-9">
            <h1 class="display-heading">User Snapshot</h1>
        </div>
        <div class="col-3">
            <select class="project-names form-control" id="project-list">
                <option>All projects</option>
                <?php if(!empty($projects)){
                    foreach($projects AS $proj){
                ?>
                <option><?=$proj['project_name'];?></option>
            <?php } } ?>
            </select>
        </div>
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
        //print_r($data); exit;
        foreach ($data as $k) {
        ?>
            <div class="row pt-3">
                <div class="col-2">
                    <a href="<?= base_url(); ?>index.php/admin/load_userdetails_page?user_id=<?= $k['user_id'] ?>" class=" " id="<?= $k['user_name'] ?>">
                        <?= $k['user_name']; ?>
                    </a>
                </div>
                <div class="col-6">
                    <?php
                    if (is_array($k['project'])) {
                        foreach ($k['project'] as $d) {
                        ?>
                        <a href="<?= base_url(); ?>index.php/admin/load_project_detail?project_id=<?= $d['project_id'] ?>" class="badge badge-light p-2 user_project_details mr-2">
                            <?php
                                if ($d['image_name'] != '') {
                                ?>
                                    <img src="<?= base_url() . UPLOAD_PATH_PROJECT . $d['image_name'] ?>" height="15px;" width="18px;">
                                <?php
                                }
                                echo $d['project_name']; ?>
                            <span class="badge badge-pill badge-dark"><?=$d['project_time'] ?></span>
                        </a>
                    <?php } 
                        }  ?>
                </div>
                <div class="col-2">
                    <p><?= $k['total_minutes']; ?></p>
                </div>
                <div class="col-2">
                    <div class="remove-user">
                        <i class="fas fa-trash-alt icon-plus icon-remove text-white" data-toggle="modal" data-target="#delete-entry"><input type="hidden" name="" class="user_id" value="<?= $k['user_id']; ?>"></i>
                    </div>
                </div>
            </div>
            <hr>
        <?php } ?>
    </div>
</div>
<div class="modal fade" id="delete-entry" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete confirmation</h3>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <h6 class="text-muted font-weight-light">This action can't be undone.</h6>
             </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-secondary col-6" id="cancel-delete" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary col-6" id="delete-user">Yes</button>
            </div>
        </div>
    </div>
</div>