 <?php
defined('BASEPATH') OR exit('No direct script access allowed');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
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
                            <a href="#" class="text-white"><img src="<?=base_url().$picture?>" height="40px" class="rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display pl-2"> Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display pl-2"> <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
<!-- UI for task snapshot -->
<p class="display-heading text-primary text-center">Task snapshot</p>
<div class="container">
    <div class="form-group">
        <div class="text-right form">
            <input type="month" class="border p-1" id="curr-month" name="cur_month"><span><button class="btn btn-primary" id="view-chart"> view chart</button></span>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <canvas id="task-chart" height="80px" class=" mb-5"></canvas>
            <p id="task-chart-error" class="text-center"></p>
        </div>
    </div>
    <table id="task-lists-datatable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Task name</th>
                <th>Description</th>
                <th>Project</th>
                <th>Start date</th>
                <th>End date</th>
                <th>Time spent</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal" id="delete-task-modal" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header ">
                <span>Do you want to delete? </span></p>
                <button type="button" class="close text-danger" data-dismiss="modal">×</button>
            </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-secondary" id="cancel-delete" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="delete-task" ><input type="hidden" id="task" name=""> Yes</button>
                </div>
            </div>
        </div>
    </div>
    

<!-- end of task snapshot -->
<footer>
<hr>
  <p class="text-center">Copyright © 2019 Printgreener.com</p>
</footer>