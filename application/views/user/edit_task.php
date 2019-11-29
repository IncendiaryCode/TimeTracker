<?php
$GLOBALS['page_title'] = 'Edit Task';
$this->load->library('session');
$profile = $this->session->userdata('user_profile');
print_r($task_data[0]['id']);
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row ">
                <div class="col-6 offset-3">
                    <?php 
                        $this->load->library('form_validation');
                        if(validation_errors()) { ?>
                    <div class="alert alert-danger">
                        <?php echo validation_errors();
                        echo isset($failure)?$failure:"";
                         ?>
                    </div>
                    <?php } ?>
                    <div class="alert-success">
                        <?php echo isset($success)?$success:""; ?>
                    </div>
                    <form action="<?=base_url();?>index.php/user/edit_task?type=edit" method="post" id="addTask" class="mt-5 ">
                        <input type="hidden" name="task_id" value="<?=$task_data[0]['task_id'];?>">
                        <div class="form-group  ">
                            <label for="task-name ">Write the task name</label>
                            <input type="text" class="form-control" name="task_name" id="Taskname" value="<?=$task_data[0]['task_name'];?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Write a small description</label>
                            <textarea class="form-control" id="description" name="task_desc" rows="4"><?=$task_data[0]['description'];?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="choose-project">Choose a project</label>
                            <select readonly="" type="number" class="form-control" id="choose-project" name="project_name">
                                <option selected value=<?php echo $task_data[0]['project_id']?>>
                                    <?=$task_data[0]['name'];?>
                                </option>
                            </select>
                        </div>
                        <h4 class="mt-4 text-center">Task activities</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Start time</th>
                                    <th scope="col">End time</th>
                                </tr>
                            </thead>
                            <?php $num = 1;
                          foreach($task_data as $task){ 
                            
                            ?>
                            <tbody id="task_history">
                                <tr>
                                    <th scope="row">
                                        <?=$num;?>
                                    </th>
                                    <td>
                                    <input type="text" id="start-date<?=$num?>"  name="start-date<?=$num?>" value="<?=$task['task_date'];?>">
                                    </td>
                                    <td>
                                        <input type="text" id="start<?=$num?>"  name="start<?=$num?>" value="<?=$task['start_time'];?>">
                                    </td>
                                    <td>
                                        
                                        <input type="text" id="start<?=$num?>"  name="end<?=$num?>" value="<?=$task['end_time'];?>">
                                    </td>
                                </tr>
                            </tbody>
                            <?php $num=$num+1;
                        } ?>
                        </table>
                        <p id="taskError" class=" text-danger"></p>
                        <p>&nbsp;</p>
                        <hr />
                        <button type="submit" class="btn btn-primary">Save Task</button>
                    </form>
                </div>
            </div>
        </div>
        </script>
        <footer class="footer">
            <hr>
            <p class="text-center ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>