<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script type="text/javascript">
var usr_arr = [];
var usr_email_array = [];
var usr_email = {};
var usr_profile = {};
</script>
<?php foreach($all_users as $users)
{
?>
<script type="text/javascript">
usr_arr.push("<?=$users['name']; ?>");
usr_email_array.push("<?=$users['email']; ?>");
usr_email["<?=$users['email']; ?>"] = "<?=$users['id']; ?>";
usr_profile["<?=$users['name']; ?>"] = "<?=$users['profile']; ?>";
</script>
<?php } ?>

<div class="container">
    <h1 class="text-center display-heading mt-3">Edit Project</h1>
    <?php if (!empty($this->session->flashdata('error'))) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo (!empty($this->session->flashdata('error'))) ? $this->session->flashdata('error') : ''; ?>
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
    <!-- form of project details -->
    <form method="post" id="modify-project" action="<?= base_url(); ?>index.php/admin/edit_project" enctype="multipart/form-data">
        <div class="row">
            <div class="col-3">
                <img src="<?=base_url().UPLOAD_PATH_PROJECT.$project_data['project']['project_image'];?>" width="150px;" height="150px;" class="img-fluid">
            </div>
            <div class="col-9">
                <div class="row">
                    <div class="col-12">
                        <div class="pb-4">
                            Name:<input type="text" class="form-control" name = "project-name" class="form-control" placeholder="Project name" value = "<?=$project_data['project']['project_name'] ?>">
                        </div>
                    </div>
                    <input type = "hidden" id= "edit_project_id" name = "project_id" value = "<?=$project_data['project']['project_id'] ?>" >
                    <div class="col-5">
                        <div class="pb-4">
                            Logo: <input type="file" name="project_icon" class="form-control item-img" placeholder="Project logo" >
                            <input type="hidden" name="cropped_icon_points" id="cropped-icon-points">
                        </div>
                    </div> 
                    <div class="col-5">
                        <div class="pb-4">
                            Color: <input type="color" class="form-control" name="project-color" placeholder="Project color" value = "<?=$project_data['project']['project_color'] ?>">
                        </div>
                    </div>
                    <div class="col-2 pt-4 text-right">
                            <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>          
        </div>
        <p class="edit_project_error text-danger" id="edit-project-error"></p>
    </form>
    <hr class="mt-5">
    <!-- list of modules -->
    <div class="row scroll-module">
        <div class="col-md-6 module-append">
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
                <?php foreach($project_data['module'] as $module) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?=$module['module_name']; ?>
                        <span><a href="#module-edit" data-toggle="modal" id="append-module-id" class = "module-edit" data-item = "<?=$module['module_id']?>">
                        <i class="fas fa-pencil-alt">
                            <input type="hidden" name = "module_id" value ="<?=$module['module_id']?>" ></i></a>
                            <a href="#module-delete" data-toggle="modal" data="<?=$module['module_id'];?>" class ="module-delete"><i class="fas fa-trash pl-3"></i></a>
                        </span>
                    </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>

        <!-- list of users -->
        <div class="col-md-6 user-append">
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
                    <li class="list-group-item d-flex-">
                    <?php if(!empty($user['profile_photo'])) { ?>
                        <div class="row">
                            <div class="col-2 col-lg-1 text-left">
                                <img src="<?= base_url() . USER_UPLOAD_PATH . $user['profile_photo']; ?>" width="30px;"> <?php  } ?>
                            </div>
                            <div class="col-4 col-lg-5 text-left">
                                <?=$user['user_name']; ?>
                            </div>
                            <div class="col-6  text-right">
                                <span>
                                    <a href="#user-delete" data-toggle="modal" data="<?=$user['user_id']; ?>" class = "user-delete float-right"><i class="fas fa-trash pl-3">
                                        <input type = "hidden" value = <?=$user['user_id'] ?> >
                                        </i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            <?php } ?>
            </ul>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="module-edit" tabindex="-1" role="dialog" aria-labelledby="module-editLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form method="post" action="<?= base_url(); ?>index.php/admin/edit_project" id = "pre-edit-module">
            <div class="modal-header">
                <h5 class="modal-title" id="module-editLabel">Edit module name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type = "hidden" id= "proj_id" name = "project_id" value = <?=$project_data['project']['project_id'] ?> >
                <input type = "hidden" id='mdl_id' name = "module_id" >
                <input class="form-control" type = "text" id = "module-name" name = "module_name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary col-6">Save changes</button>
            </div>
        </form>
    </div>
  </div>
</div>
<div class="modal fade" id="module-delete" tabindex="-1" role="dialog" aria-labelledby="module-deleteLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="module-deleteLabel">Delete confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <p class="pl-2">On deleting this module,</p>
        <div class="ml-2 modal-body"><ul>
        <li>All tasks under this module will be deleted  and unassigned from the user.</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary col-6" id = "delete-module">Yes</button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="user-delete" tabindex="-1" role="dialog" aria-labelledby="user-deleteLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="user-deleteLabel">Delete confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <p class="pl-2">On deleting this user,</p>
        <div class="ml-2 modal-body"><ul><li>
            All timelines of tasks under this user will be deleted.</li></ul></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary col-6" id= "delete-user">Yes</button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="maintainAsectRatio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="upload-demo1" class="center-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary col-6" data-dismiss="modal">Close</button>
                <button type="button" id="cropImageBtn1" class="btn btn-primary col-6 ml-0">Done</button>
            </div>
        </div>
    </div>
</div>