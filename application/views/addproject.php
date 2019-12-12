<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
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
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/admin/load_profile"' class="text-display">1. Profile</a></p>
                                    <p class="items"><a href="#" onclick='window.location.href="<?=base_url();?>index.php/login/logout"' class="text-display">2. <i class="fas fa-power-off"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row mt-2 pt-4">
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
                            <p class="text-center display-4 m-3 text-primary">Add project</p>
                        <form action="<?php echo base_url();?>index.php/admin/add_projects" id="add-project" method="post">
                            <div class="form-group mt-3">
                            <p><input type="radio" name="type" id="old-project" checked>Existing project
                            <input type="radio" name="type" id="new-project" class="ml-5" >New project</p>
                            <select class="project-list form-control mt-3" id="old-project-input" name="project_name">
                            <option>Select project</option></select>
                            </div>
                            <div class="form-group mt-3" id="new-project-input">
                                <label for="task-name ">Enter the Project name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control " name="project-name" id="project-name" placeholder="">  
                            </div>
                            <div class="form-group mt-3 row " id="append-new-module">
                                <div class="col-10 ">
                                    <label for="new-module0">Enter module name</label>
                                    <input class="form-control user"  id="new-module0" name="new-module[0][module]" placeholder="General">     
                                </div>
                                <div class="col-2 pt-4">
                                    <a href="#" id="add-new-module" title="Add">
                                        <i class="fas fa-plus icon-plus text-success" ></i>
                                    </a>
                                </div>
                            </div>
                            <div class="form-group mt-3 row " id="assign-new-user">
                                <div class="col-10 ">
                                    <label for="assign-name">Choose the name of user to assign project</label>
                                    <select class="form-control user"  id="assign-name0" name="assign-name[0][name]">
                                        <option>Select User</option>
                                        <?php
                                            foreach($names as $name){ ?>
                                            <option ><?php echo $name['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-2 pt-4">
                                    <a href="javascript:void(0);" id="assign-new-user" title="Add">
                                        <i class="fas fa-plus icon-plus text-success" ></i>
                                    </a>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Choose logo for project</label>
                                <input type="file" class="form-control" name="project-logo" id="Project-logo">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Choose color for project</label>
                                <input type="color"  value="#e384fb" class="form-control" name="project-color" id="Project-color">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Enter start date</label>
                                <input type="text" class="form-control edit-date" name="start-date" id="start-date">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Enter end date</label>
                                <input type="text" class="form-control edit-date" name="end-date" id="end-date">
                            </div>
                            <p id="module-error" class="text-danger"></p>
                            <button type="submit" class="btn btn-primary">Add Project</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer class="">
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>