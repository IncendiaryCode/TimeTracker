<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="container">
    <div class="row mt-5 shadow-sm">
        <?php
        ?>
        <div class="col-md-4 ">
            <div class="card user-card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <div class="text-center"><span class="project-details"><?= $data['users'] ?></span>
                            <p class="display-5">Total users</p><input type="hidden" id="project_id" value="<?= $data['project_id'] ?>">
                        </div>
                        <div class="text-center "><i class="fas fa-plus icon-plus text-white" data-target="#add-user" data-toggle='modal'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-3 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div><span class="project-details">
                                    <?php
                                    if ($data['image_name'] != '') {
                                    ?>
                                        <img src="<?= base_url() . UPLOAD_PATH . $data['image_name']; ?>" width="40px;">
                                        <input type="hidden" id="project-id" name="" value="<?= $data['project_id'] ?>">
                                    <?php
                                    } ?>
                                    <strong><?= $data['project_name']; ?></strong>
                            </div>
                            <div><span class="project-details "><?= $data['tasks'] ?></span></div>
                            <p class="text-center">Total tasks</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-2 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <div class="text-center"><span class="project-details"><?= $data['total_minutes']; ?></span>
                            <p class="text-center">Total time spent</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <canvas id="project_time_chart" height="80px;"></canvas>
    </div>
    <hr>

    <div class="row">
        <div class="col-12">
            <p class="efficiency text-left separator-hr mt-5">Project table</p>
            <table id="project-list-datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>User name</th>
                        <th>Task count</th>
                        <th>Time spent</th>
                    </tr>
                </thead>
            </table>
            <p id="project-datatabel-error" class="text-center"></p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <p class="efficiency text-left separator-hr mt-5">Task table</p>
            <table id="task-list-datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Task name</th>
                        <th>User count</th>
                        <th>Time spent</th>
                    </tr>
                </thead>
            </table>
            <p id="task-datatabel-error" class="text-center"></p>
        </div>
    </div>

</div>

<div class="modal fade" id="add-user" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Add user</h4>
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url(); ?>index.php/admin/assign_user_to_project?project_id=<?= $data['project_id'] ?>" id="adding-user">
                    <?php
                    if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                    <?php } else if ($this->session->flashdata('success')) { ?>
                        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                    <?php } ?>
                    <select class="form-control user" id="assigning-user-name" name="assigning-user-name">
                        <option>select user</option>
                        <?php
                        foreach ($user_names as $name) { ?>
                            <option value="<?= $name['id'] ?>"><?php echo $name['name']; ?></option>

                        <?php } ?>
                    </select>
                    <input type="hidden" name="project-id" id="project-existing-id" value="<?= $data['project_id'] ?>">
                    <div class="text-right"><button type="submit" class="btn btn-primary mt-2">Add user</button></div>
                    <p class="text-danger" id="adding-user-error"></p>
                </form>
            </div>
        </div>
    </div>
</div>