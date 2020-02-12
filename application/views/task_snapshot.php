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
         <div class="col-md-8 offset-md-2">
             <table id="task-lists-datatable" class="table table-striped table-bordered">
                 <thead>
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
 <div class="modal" id="delete-task-modal" data-backdrop="false">
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