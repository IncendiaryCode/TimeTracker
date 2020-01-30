<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="container">
    <div class="row mt-5 shadow-sm">
        <div class="col-md-4 ">
            <div class="card user-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="mx-auto d-block">
                                <?php
                                if ($data['profile'] != '') {
                                ?>
                                    <img src="<?= base_url() . USER_UPLOAD_PATH . $data['profile']; ?>" class="rounded-circle" width="50px;" height="50px;">
                                <?php } ?>
                            </div>
                        </div>
                        <input type="hidden" name="" id="user-id" value="<?= ($data['id']) ?>">
                        <div class="col-9">
                            <h3 class="text-left mt-2 mb-1"><?= ($data['user_name']) ?></h3>
                            <a href="#"><?= ($data['email']) ?></a>
                            <p><?php echo (($data['phone']) !== '0') ? $data['phone'] : ''; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-3 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <?php
                    if ($data['t_minutes']) { ?>
                        <div><span class="display-3"><?= round(($data['t_minutes'] / 60), 2); ?></span><span class="display-5">h</span></div>
                        <p class="text-center">Time spent</p>

                    <?php } else { ?>
                        <div><span class="display-heading"><?= round(($data['t_minutes'] / 60), 2); ?></span><span class="display-5">h</span></div> <?php } ?>
                </div>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-2 offset-md-1">
            <div class="card user-card">
                <div class="card-body">
                    <div class="mx-auto d-block">
                        <div class="text-center"><span class="display-3"><?= ($data['project_count']) ?></span>
                            <p class="text-center">Active projects</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <canvas id="user_time_chart" height="80px;"></canvas>
    </div>
    <hr>
    <p class="efficiency text-center mt-5">Task table</p>
    <table id="user-task-datatable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Task name</th>
                <th>Project name</th>
                <th>Time spent</th>
            </tr>
        </thead>
    </table>
    <p class="text-center" id="search-error"></p>
    <hr>
    <p class="efficiency text-center mt-5">Project table</p>
    <table id="user-project-datatable" class="table table-striped table-bordered ">
        <thead>
            <tr>
                <th>Project name</th>
                <th>Task count</th>
                <th>Time spent</th>
            </tr>
        </thead>
    </table>
    <p class="text-center" id="user-project-error"></p>
</div>