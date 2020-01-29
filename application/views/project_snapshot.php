<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="container">
    <div class="row mt-5">
        <div class="col">
            <h1 class="display-heading">Project Snapshot</h1>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div id="chart_div" style=" height: 500px;"></div>
            <p id="project-snap-error"></p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-5">
            <p><strong>Project name</strong></p>
        </div>
        <div class="col-5">
            <p><strong>Users</strong></p>
        </div>
        <div class="col-2">
            <p><strong>Time spent</strong></p>
        </div>
    </div>
    <hr>
    <?php foreach ($data as $proj) {
    ?>
        <div class="row" style="min-height: 50px;">
            <div class="col-5">
                <a href="<?= base_url(); ?>index.php/admin/load_project_detail?project_id=<?= $proj['project_id'] ?>">
                    <div class="mr-2">
                        <?php
                        if ($proj['project_icon'] != '') {
                        ?>
                            <img src="<?= base_url() . UPLOAD_PATH . $proj['project_icon']; ?>" width="30px;">
                            <input type="hidden" id="project-id" name="" value="<?= $proj['project_id'] ?>">
                        <?php
                        } ?>
                        <?= $proj['project_name']; ?>
                    </div>
                </a>
            </div>
            <div class="col-5">
                <p>total users: <?= $proj['total_users']; ?></p>
                <?php foreach ($proj['user_details'] as $user) {  ?>
                    <!-- redirect to user detail page -->
                    <a href="<?= base_url(); ?>index.php/admin/load_userdetails_page?user_id=<?= $user['user_id']; ?>" class="pt-2 mr-3 mt-2">
                        <?= $user['user_name']; ?>
                    </a>
                <?php  } ?>
            </div>
            <div class="col-2"><?= round($proj['time_used'] / 60, 2) ?> hrs</div>

        </div>
        <hr>
    <?php  } ?>
</div>