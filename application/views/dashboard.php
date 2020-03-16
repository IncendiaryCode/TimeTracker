<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="container">
    <div class="row mt-5" id="dashboard-stats">
        <div class="col-md-4 col-12">
            <div class="card card-theme-a">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-right">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="col-6">
                            <h1><?php echo $total_users; ?></h1>
                            <p class="card-text">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/add_users" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> User</a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/load_snapshot?type=user" class="btn btn-primary btn-block details">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="card card-theme-b">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-right">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div class="col-6">
                            <h1>
                                <?php echo $total_projects; ?>
                            </h1>
                            <p class="card-text">Total projects</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/add_projects" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Project</a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/load_snapshot?type=project" class="btn btn-primary btn-block details">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="card card-theme-c">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-right">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="col-6">
                            <h1><?php echo $total_tasks; ?></h1>
                            <p class="card-text">Total tasks</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/load_add_task" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Task</a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url(); ?>index.php/admin/load_task_snapshot" class="btn btn-primary btn-block details">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="separator-hr" />
    <div class="row">
        <div class="col-6">
            <h1 class="display-heading">Project Chart</h1>
        </div>
        <div class="col-6 col-md-3 offset-md-3" id="dash-prj-dtpicker">
            <div class="input-group date">
                <input type="text" class="form-control datepicker" name="cur_month" id="update-prj-dtpicker" value="<?= date('F Y'); ?>" >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <button type="button" class="btn fa fa-calendar p-0"></button>
                    </span>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col">
            <canvas id="main-chart" height="80px;"></canvas>
        </div>
    </div>
    <div class="row pt-5 text-dark">
        <!-- TODO.. -->
        <div class="col-md-6">
            <?php
            if(!empty($top_projects[0])){
            if($top_projects[0] != 0)
                {
                $count = 0;
                if ($top_projects[0] < 5) {
                    $count = $top_projects[0];
                } else {
                    $count = 5;
                }
            ?>
            <h4>Top <?= $count; ?> projects</h4> <?php } ?>
            <ul class="list-group mt-4">
                <?php foreach ($top_projects[1] as $project) { ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= base_url(); ?>index.php/admin/load_project_detail?project_id=<?= $project['project_id'] ?>">
                                    <?php
                                    if ($project['image_name'] != '') {

                                    ?>
                                        <img src="<?= base_url() . UPLOAD_PATH_PROJECT . $project['image_name']; ?>" width="30px;">
                                    <?php
                                    } ?>
                                    <?php echo $project["project_name"]; ?>
                                </a>
                            </div>
                            <div class="col-6">
                                <?php
                                    $hours = floor($project['t_minutes'] / 60);
                                    $minutes = ($project['t_minutes'] % 60);
                                    if($hours < 1)
                                        echo sprintf('%02dm', $minutes);
                                    else if($minutes < 1)
                                        echo sprintf('%02dh', $hours);
                                    else if(($hours<1) && ($minutes <1))
                                        echo "--";
                                    else
                                        echo sprintf('%02dh %02dm', $hours, $minutes);
                                ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        </div>
        <div class="col-md-6" id = "top-users">
            <?php
            if(!empty($top_users[0])){
            $cnt = 0;
            if ($top_users[0] < 5) {
                $cnt = $top_users[0];
            } else {
                $cnt = 5;
            }
            ?>
            <h4>Top <?= $cnt; ?> users</h4>
            <ul class="list-group mt-4">
                <?php foreach ($top_users[1] as $user) { ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= base_url(); ?>index.php/admin/load_userdetails_page?user_id=<?= $user['user_id']; ?>">
                                    <?php if ($user['profile'] != ''){ ?>
                                        <img class="rounded-circle" src="<?= base_url() . USER_UPLOAD_PATH . $user['profile']; ?>" width="30px;" height="30px;">
                                  <?php  } ?>
                                    <?php echo $user["user_name"]; ?>
                                </a>
                            </div>
                            <div class="col-6">
                                <?php
                                    $hours = floor($user['t_minutes'] / 60);
                                    $minutes = ($user['t_minutes'] % 60);
                                    if($hours < 1)
                                        echo sprintf('%02dm', $minutes);
                                    else if($minutes < 1)
                                        echo sprintf('%02dh', $hours);
                                    else if(($hours<1) && ($minutes <1))
                                        echo "--";
                                    else
                                        echo sprintf('%02dh %02dm', $hours, $minutes);
                                ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        </div>
    </div>
</div>
