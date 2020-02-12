
<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
?>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <div class="row mt-2">
                    <div class="col-md-6 offset-md-3">
                        <?php 
                            if(validation_errors()) { ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo validation_errors();  ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button> 
                                </div>
                            <?php } 
                            if($this->session->flashdata('true')){ ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php
                                        echo $this->session->flashdata('true'); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                </div>
                            <?php } 
                            else if($this->session->flashdata('err')){ ?>
                                <div class = "alert alert-danger">
                                    <?php echo $this->session->flashdata('err'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php } ?>
                            <h1 class="text-center display-heading">Add Project</h1>
                        <form action="<?php echo base_url();?>index.php/admin/add_projects" id="add-project" method="post" enctype="multipart/form-data">
                            <div class="form-group mt-3" id="new-project-input">
                                <label for="task-name ">Project name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control " name="project-name" id="project-name" placeholder="">  
                            </div>
                            <div class="form-group mt-3 " id="append-new-module">

                                <label for="new-module0">Module name:</label>
                                <div class="row">
                                    <div class="col-10 assign-module0">
                                        <input class="form-control user"  id="new-module0" name="new-module[0][module]" placeholder="General">     
                                    </div>
                                    <div class="col-2 assign-module0">
                                        <a href="#" id="add-new-module" >
                                            <i class="fas fa-plus icon-plus" id="adding-module" ></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3 row " id="assign-new-user">
                                <div class="col-12">
                                    <label for="assign-name">Name:</label>
                                    <select class="form-control user"  id="assign-name0" multiple="" name="assign-name[0][name]">
                                        <?php $i=1;
                                            foreach($names as $name){ ?>
                                            <option name="assign-name[$i++][name]"><?php echo $name['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="task-name ">Choose logo:</label>
                                <input type="file" class="form-control" name="project-logo" id="Project-logo">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">Choose color:</label>
                                <input type="color"  value="#e384fb" class="form-control" name="project-color" id="Project-color">
                            </div>
                            <!-- <div class="form-group mt-3">
                                <label for="task-name ">Start date:</label>
                                <input type="text" class="form-control edit-date" name="start-date" id="start-date">
                            </div>

                            <div class="form-group mt-3">
                                <label for="task-name ">End date:</label>
                                <input type="text" class="form-control edit-date" name="end-date" id="end-date">
                            </div> -->
                            <p id="module-error" class="text-danger"></p>
                            <div class="text-right"><button type="submit" class="btn btn-primary text-right">Add Project</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
