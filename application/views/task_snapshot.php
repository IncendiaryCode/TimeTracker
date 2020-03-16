 <?php
    defined('BASEPATH') or exit('No direct script access allowed');
?>

 <!-- UI for task snapshot -->
 
 <div class="container">
     <div class="row  pt-3">
        <div class="col-6">
            <h1 class="display-heading text-left">Task snapshot</h1>
        </div>
        <div class="col-3 offset-3" id="select-month">
            <div class="input-group date">
                <input type="text" class="form-control datepicker" id="curr-month" name="cur_month" data-date-format="yyyy-mm-dd" placeholder="form" value="<?= date('F Y'); ?>" >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <button type="button" class="btn fa fa-calendar p-0"></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
     <div class="row mt-5">
         <div class="col-12">
             <canvas id="task-chart" height="80px" class=" mb-5"></canvas>
             <p id="task-chart-error" class="text-center"></p>
         </div>
         <div class="col-md-12 mt-4">
            <div class="row pb-3 task-filters">
                <div class="col-md-3 col-6">
                    <div id="baseDateControl">
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker" id="dateStart" name="dateStart" data-date-format="yyyy-mm-dd" placeholder="form" >
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <button type="button" class="btn fa fa-calendar p-0"></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                            <div class="input-group date">
                                    <input type="text" class="form-control datepicker" id="dateEnd" name="dateEnd" data-date-format="yyyy-mm-dd" placeholder="to">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <button type="button" class="btn fa fa-calendar p-0"></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <select class="custom-select" id="select-prt">
                        <option selected>All projects</option>
                    <?php 
                    if(!empty($projects))
                    {
                        foreach($projects as $prj) { ?>
                            <option value=<?=$prj['id']?> ><?=$prj['project_name']?></option>
                        <?php }
                    }   ?> 
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <select class="custom-select" id ="select-user">
                        <option selected>All users</option>
                        <?php 
                        if(!empty($users)) {
                            foreach($users as $usr) { ?>
                                <option value=<?=$usr['id']?> ><?=$usr['name']?></option>
                            <?php }
                        }   ?>
                    </select>
                </div>
                <div class="col-md-2 col-3 text-right">
                    <a class="clear-filter" href="#"><i class="far fa-times-circle"></i> Clear filter</a>
                </div>
                <div class="col-md-1 col-3 text-right">
                    <button type="button" class="btn btn-primary" id="task-snapshot-filter">Apply</button>
                </div>
                <div class="col-12 text-center mt-2">
                    <p class="text-danger pl-3" id="task-filter-error"></p>
                </div>
            </div>
             <table id="task-lists-datatable" class="table table-striped table-bordered">
                 <thead style="'width':'100%'">
                     <tr>
                         <th>Task name</th>
                         <th>Description</th>
                         <th>Project</th>
                         <th>User</th>
                         <th>Start date</th>
                         <th>End date</th>
                         <th>Time spent</th>
                         <th>&nbsp;</th>
                     </tr>
                 </thead>
             </table>
             <p id="task-tabel-error" class="text-center"></p>
         </div>
     </div>
 </div>
 <div class="modal fade" id="delete-task-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered-">
         <div class="modal-content">
             <div class="modal-header">
                 <h3>Delete confirmation</h3>
                 <button type="button" class="close text-danger" data-dismiss="modal">×</button>
             </div>
             <div class="modal-body">
                <p>Are you sure you want to delete this task?</p>
                <h6 class="text-muted font-weight-light">This action can't be undone.</h6>
             </div>
             <div class="modal-footer text-center">
                 <button type="button" class="btn btn-secondary col-6" id="cancel-delete" data-dismiss="modal">No</button>
                 <button type="button" class="btn btn-primary col-6" id="delete-task"><input type="hidden" id="task" name=""> Yes</button>
             </div>
         </div>
     </div>
 </div>