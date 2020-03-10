<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url_helper');
$this->load->library('session');
$this->load->library('form_validation');
?>
    
    <main class="container-fluid container-fluid-main">
        <div class="container-fluid">
            <div class="main-container-inner">
                <div class="container">
                    <div class="row mt-2">
                        <div class="col-md-6 offset-md-3">
                            <?php 
                            if($this->session->flashdata('true')){ ?>
                                <div class="alert alert-success">
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
                            <h1 class="text-center display-heading">Add Task</h1>
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
                                        <label for="user-name ">Choose users </label>
                                        <select class="form-control user" id="select-users" multiple="" name="user-name[0][name]">
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
                                <p id="taskError" class="text-danger"></p>
                                <div class="text-right"><button type="submit" class="btn btn-primary">Assign Task</button></div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
