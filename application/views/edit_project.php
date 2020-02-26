<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script type="text/javascript">
var usr_arr = [];
var usr_id = {};
var usr_profile = {};
</script>
<?php foreach($all_users as $users)
{ 
?>
<script type="text/javascript">
usr_arr.push("<?=$users['name']; ?>");
usr_id["<?=$users['name']; ?>"] = "<?=$users['id']; ?>";
usr_profile["<?=$users['name']; ?>"] = "<?=$users['profile']; ?>";

</script>
<?php } ?>

<div class="container">
    <h1 class="text-center display-heading mt-3">Edit project</h1>
    <form method="post" action="<?= base_url(); ?>index.php/admin/edit_project">
        <div class="row">
            <div class="col-12">
                <div class="pb-4">
                    Name:<input type="text" class="form-control" name = "project-name" class="form-control" placeholder="Project name" value = <?=$project_data['project']['project_name'] ?>>
                </div>
            </div>
            <input type = "hidden" id= "edit_project_id" name = "edit_project_id" value = <?=$project_data['project']['project_id'] ?> >
            <!-- <div class="col-12">
                <div class="pb-4">
                    Description:<input type="text" class="form-control" name = "meta-data" class="form-control" placeholder="Description" value = <?=$project_data['meta_data'] ?>>
                </div>
            </div> -->
            <div class="col-5">
                <div class="pb-4">
                    Logo: <input type="file" name="project-icon" class="form-control" placeholder="Project logo" value = <?=base_url().UPLOAD_PATH.$project_data['project']['project_image'];?>>
                </div>
            </div> 
            <div class="col-5">
                <div class="pb-4">
                    Color: <input type="color" class="form-control" placeholder="Project color" value = <?=$project_data['project']['project_color'] ?>>
                </div>
            </div>
            <div class="col-2 pt-4 text-right">
                    <button type="submit" class="btn btn-primary">Save</button>
            </div>                
        </div>
    </form>
    <hr class="mt-5">
    <div class="row scroll-module">
        <div class="col-6 module-append">
            <h3 class="text-center">Modules</h3>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="module name" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn" id="append-module" type="button"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <p class="text-danger" id="module-error"></p>
            <ul class="list-group module-lists pt-5">
            <?php if(!empty($project_data['module'])) { ?>
                <?php foreach(($project_data['module']) as $module) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?=$module['module_name']; ?>
                        <span><a href="#module-edit" data-toggle="modal" class = "module-edit">
                        <i class="fas fa-pencil-alt">
                            <input type = "hidden" value = <?=$module['module_id'] ?> ></i></a>
                            <a href="#module-delete" data-toggle="modal" data=<?=$module['module_id'] ?> class = "module-delete"><i class="fas fa-trash pl-3"></i></a>
                        </span>
                    </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
        <div class="col-6 user-append">
            <h3 class="text-center">Users</h3>
                <div class="input-group">
                    <input type="text" class="form-control" id="user-assigned" placeholder="user name" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn" id="append-user" type="button"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            <p class="text-danger" id="user-error"></p>
            <p id="append-list"></p>
            <ul class="list-group user-lists pt-5">
            <?php if(!empty($project_data['users'])) { ?>
                <?php foreach(($project_data['users']) as $user) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php if(!empty($user['profile_photo'])) { ?>
                        <img src="<?= base_url() . UPLOAD_PATH . $user['profile_photo']; ?>" width="30px;"> <?php  } ?><span class ="user-name"><?=$user['user_name']; ?></span>
                        <span><a href="#user-delete" data-toggle="modal" data=<?=$user['user_id']; ?> class = "user-delete"><i class="fas fa-trash pl-3">
                        <input type = "hidden" value = <?=$user['user_id'] ?> >
                        </i></a></span>
                    </li>
                <?php } ?>
            <?php } ?>
            </ul>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="module-edit" tabindex="-1" role="dialog" aria-labelledby="module-editLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form method="post" action="<?= base_url(); ?>index.php/admin/edit_module">
            <div class="modal-header">
                <h5 class="modal-title" id="module-editLabel">Edit module name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type = "hidden" id= "proj_id" name = "project_id" value = <?=$project_data['project']['project_id'] ?> >
                <input class="form-control" type = "text" id = "module-name" name = "edit_module">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
  </div>
</div>
<div class="modal fade" id="module-delete" tabindex="-1" role="dialog" aria-labelledby="module-deleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="module-deleteLabel">Do you want to delete this module?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary" id = "delete-module">Yes</button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="user-delete" tabindex="-1" role="dialog" aria-labelledby="user-deleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="user-deleteLabel">Do you want to delete this user?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary" id= "delete-user">Yes</button>
        </div>
    </div>
  </div>
</div>