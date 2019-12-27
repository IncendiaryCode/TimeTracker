<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$this->load->library('session');
$this->load->library('form_validation');
$profile = $this->session->userdata('user_profile');
$picture = substr($profile,29);
?>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="#"><img src="<?=base_url();?>assets/images/logo-white.png" height="40px;" onclick="window.location.href='<?=base_url();?>index.php/admin'"></a>
            <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon "></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto flex-column-reverse flex-lg-row">
                    <div class="nav-item nav-link">
                        <div class="dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false" x-placement="bottom-start">
                            <img src="<?=base_url().$picture?>" height="40px" class="rounded-circle">
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
    
    <main class="container-fluid container-fluid-main">
        <div class="container-fluid">
            <div class="main-container-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <?php 
                            if(validation_errors()) { ?>
                                <div class="alert alert-danger">
                                    <?php 
                                        echo validation_errors();
                                    ?>    
                                </div>
                            <?php } 
                            if($this->session->flashdata('true')){ ?>
                                <div class="alert-success">
                                    <?php  
                                        echo $this->session->flashdata('true'); 
                                    ?>    
                                </div>
                            <?php } 
                            else if($this->session->flashdata('err')){ ?>
                                <div class = "alert alert-danger">
                                    <?php echo $this->session->flashdata('err'); ?>
                                </div>
                            <?php } ?>
                            <p class="text-center display-heading m-3 text-primary">Add task</p>
                            <form action="<?php echo base_url();?>index.php/admin/add_tasks" id="addTask" method="post">
                                <div class="form-group">
                                    <label for="task-name">Task name</label>
                                    <input type="text" class="form-control" id="task_name" name="task_name">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description"rows="5"></textarea>
                                </div>
                                
                                <div class="form-group mt-5 row " id="append-new-user">
                                    <div class="col-12">
                                        <label for="user-name ">Choose the name </label>
                                        <select class="form-control user" multiple="" name="user-name[0][name]">
                                            <?php
                                                foreach($names as $name){ ?>
                                                <option ><?php echo $name['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <!-- <div class="col-2 pt-4">
                                        <a href="javascript:void(0);" id="add-new-user" title="Add">
                                            <i class="fas fa-plus icon-plus text-success" ></i>
                                        </a>
                                    </div> -->
                                </div>
                                <div class="row assign-user pb-3"></div>
                                <div>
                                <div class="form-group">
                                    <label for="choose-project">Choose project</label>
                                    <select class="form-control" id="chooseProject" name="chooseProject">
                                    <option>Select Project</option>
                                    <?php 
                                        foreach($result as $p){ 
                                            if($p['project_name'] != "")
                                                { ?>
                                            <option value="<?= $p['id']; ?>"><?=$p['project_name']; ?></option>
                                        <?php } } ?> 
                                </select>
                                </div>
                                <div class="form-group">
                                    <label for="module">Choose module</label>
                                    <select class="form-control" id="module" name="module">
                                        
                                    </select>
                                </div>
                                
                                
                                
                                <p id="user-name-error" class="text-danger"></p>
                                <button type="submit" class="btn btn-primary">Assign Task</button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="admin-footer">
    <hr>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
