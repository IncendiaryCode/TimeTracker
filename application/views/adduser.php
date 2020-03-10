<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$profile = $this->session->userdata('user_profile');
?>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row mt-2">
                    <div class="col-md-6 offset-md-3">
                        <?php 
                            if($this->session->flashdata('true')){ ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                   <span><?= $this->session->flashdata('true'); ?> </span>
                                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php } 
                            else if($this->session->flashdata('err')){ ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('err'); ?>
                                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php } ?> 
                        <h1 class="text-center display-heading">Add User</h1>
                        <form action="<?php echo base_url();?>index.php/admin/add_users" id="addUser" method="post" novalidate>
                            <div class="form-group mt-3">
                                <label for="task-name ">Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="task_name" id="newUser" value="">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Email:<span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="user_email" id="user_email" >
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Password:<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="task_pass" id="task_pass" value="">
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Contact number:</label>
                                <input type="tel" minlength="10" maxlength="10" class="form-control" name="contact" id="contact" value="">
                            </div>
                            <p id="user-error" class="text-danger"></p>
                            <div class="text-right"><button type="submit" class="btn btn-primary" value="submit">Add User</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
