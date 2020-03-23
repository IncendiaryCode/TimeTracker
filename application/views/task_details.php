<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    // print_r($task_data);
?>

<div class="container">
    <div class="row pt-3">
    	<div class="col-12 text-center">
    		<h1>Task Details</h1>
    	</div>
        <div class="col-12 col-md-10 offset-md-1">
    		<h4 class="mb-4">User name: <span><?= $task_data['user_name'] ?></span></h4>
    		<div class="pb-2">
           Task name: <input type="text" class="form-control" readonly="" name="task-name" value="<?=$task_data["task_name"] ?>">
    		</div>
    		<div class="pb-2">
           Project name: <input type="text" class="form-control" readonly="" name="project-name" value="<?=$task_data["project_name"] ?>">
    		</div>
    		<div class="pb-2">
           Module: <input type="text" class="form-control" readonly="" name="module-name" value="<?=$task_data["module_name"] ?>">
    		</div>
    		<div class="pb-2">
           Description: <input type="text" class="form-control" readonly="" name="task-description" value="<?=$task_data["description"] ?>">
    		</div>
        </div>
        <div class="col-12 col-md-10 offset-md-1">
             <div id="task-times">
                <div class="row pt-4">
                    <div class="col-6 text-left">
                        <p class="display-5"><strong>Timeline</strong></p>
                    </div>
                    <div class="col-12">
                        <p id="taskError" class="text-danger"></p>
                    </div>
                </div>
                <div id="show_list">
                    <div class="row">
                        <div class="col-4 col-md-5">
                            <p>Date</p>
                        </div>
                        <div class="col-4 col-md-3">
                            <p>Start time</p>
                        </div>
                        <div class="col-4 col-md-3">
                            <p>End time</p>
                        </div>
                    </div>
                </div>
            <!-- Add time: New Case -->
         	<?php foreach ($timeline_data as $task_timeline) { ?>
                    <div class="pb-5">
                        <div class="primary-wrap">
                            <div class="row remove-first-timeline">
                                <div class="col-4 col-md-5">
                                    <div class="input-group date mb-3">
                                        <input type="text" class="date-utc form-control datepicker pl-3" readonly="" name="time[0][date]" data-date-format="yyyy-mm-dd" id="date-picker-0" value="<?=$task_timeline["task_date"]; ?>" >
                                        <div class="input-group-append">
                                            <!-- <span class="input-group-text">
                                                <button type="button" class="btn fa fa-calendar datepicker-icon p-0"></button>
                                            </span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="input-group">
                                        <div role="wrapper" class="gj-timepicker gj-timepicker-bootstrap gj-unselectable input-group">
                                        	<input id="start-time-0"  readonly="" class="date-utc form-control timepicker-a border" name="time[0][start]" placeholder="hh:mm" data-type="timepicker" data-guid="7854e58d-85a9-1b4f-2825-6ed567aed58b" data-timepicker="true" role="input" value="<?=$task_timeline["start_time"]; ?>">
                                        	<!-- <span class="input-group-append" role="right-icon">
                                        		<button class="btn btn-outline-secondary border-left-0" type="button"><i class="gj-icon clock"></i>
                                        		</button>
                                        	</span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3">
                                    <div class="input-group">
                                        <div role="wrapper" class="gj-timepicker gj-timepicker-bootstrap gj-unselectable input-group">
                                        	<input id="end-time-0" readonly="" class="date-utc form-control timepicker-b border" name="time[0][end]" placeholder="hh:mm" data-type="timepicker" data-guid="dfd4c7fa-8792-be1b-6697-240e12210168" data-timepicker="true" role="input" value="<?=$task_timeline["end_time"]; ?>">
                                        	<!-- <span class="input-group-append" role="right-icon">
                                        		<button class="btn btn-outline-secondary border-left-0" type="button"><i class="gj-icon clock"></i>
                                        		</button>
                                        	</span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10 col-md-11 text-center">
                                    <input id="description-0"  readonly="" class="form-control" name="time[0][task_description]" placeholder="description" value="<?=$task_timeline["task_description"]; ?>"	>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
	        </div>
	    </div>
	</div>