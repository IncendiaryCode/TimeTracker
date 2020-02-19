 <?php
    defined('BASEPATH') or exit('No direct script access allowed');
?>

 <!-- UI for task snapshot -->
 
 <div class="container">
     <!-- <div class="input-group">
         <div class="text-right form">
             <input type="month" class="border p-1" id="curr-month" name="cur_month">
         </div>
     </div> -->
     <div class="row  pt-3">
        <div class="col-6">
            <h1 class="display-heading text-left">Task snapshot</h1>
        </div>
        <div class="col-3 offset-3">
            <div class="input-group">
                <input type="text" class="form-control" id="curr-month" name="cur_month" value="<?= date('F Y'); ?>">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
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
                <div class="col-3">
                    <div id="baseDateControl">
                        <div class="dateControlBlock pb-3">
                            Between <input type="text" name="dateStart" id="dateStart" class="datepicker p-0 border border-secondary" size="8" /> and
                            <input type="text" name="dateEnd" id="dateEnd" class="datepicker p-0 border border-secondary" size="8"/>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <select class="custom-select" id="select-prt">
                        <option selected>Select project</option>
                    <?php 
                    if(!empty($projects))
                    {
                        foreach($projects as $prj) { ?>
                            <option value=<?=$prj['id']?> ><?=$prj['project_name']?></option>
                        <?php }
                    }   ?> 
                    </select>
                </div>
                <div class="col-3">
                    <select class="custom-select" id ="select-user">
                        <option selected>Select user</option>
                        <?php 
                        if(!empty($users)) {
                            foreach($users as $usr) { ?>
                                <option value=<?=$usr['id']?> ><?=$usr['name']?></option>
                            <?php }
                        }   ?> 
                    </select>
                </div>
                <div class="col-2 text-right">
                    <a class="clear-filter" href="#"><i class="far fa-times-circle"></i> Clear filter</a>
                </div>
                <div class="col-1 text-right">
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
 <div class="modal" id="delete-task-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content text-center">
             <div class="modal-header ">
                 <span>Do you want to delete? </span></p>
                 <button type="button" class="close text-danger" data-dismiss="modal">×</button>
             </div>
             <div class="modal-footer text-center">
                 <button type="button" class="btn btn-secondary" id="cancel-delete" data-dismiss="modal">No</button>
                 <button type="button" class="btn btn-primary" id="delete-task"><input type="hidden" id="task" name=""> Yes</button>
             </div>
         </div>
     </div>
 </div>