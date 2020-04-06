<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller
{
    public $userid;
    //User panel constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->userid = $this->session->userdata('userid');
        $this->load->model('user_model');
        $this->load->helper('url');
        $this->load->helper('url_helper');
        //$this->load->driver('session');
        if($this->session->userdata('logged_in') == FALSE || $this->session->userdata('user_type') == 'admin'){ //login check
            redirect('login/index','refresh'); //if not logged in, move to login page
        }
        $this->load->helper(array(
            'form',
            'url'
        ));
        $this->lang->load('form_validation_lang');
        $this->load->library('form_validation');
        $this->load->helper('security');
        $this->load->library('upload');
        $this->load->library('image_lib');
    }
    
    public function index()
    {
        $task_details['task_info'] = $this->user_model->task_status();//get details about login time, running tasks
        $task_details['project_list'] = $this->user_model->get_project_name();
        $task_details['tasks_list'] = $this->user_model->get_tasks_name($this->userid);
        $this->load->template('user/user_dashboard',$task_details);
    }

    //start the login timer manually in user dashboard page
    public function save_login_time(){
        $res = array();
        $result = $this->user_model->start_login_timer(); //to start the login timer
        if($result == FALSE){
            //$res['flag'] = 0;
            $res['msg'] = 'Failed to start login timer.';
            $res['task_info'] = $this->user_model->task_status();
            $res['project_list'] = $this->user_model->get_project_name();
            $this->load->template('user/user_dashboard', $res);
        }else{
            redirect('user');
        }
    }
    
    public function dark(){
        $GLOBALS['dark_mode'] = 0;
        $this->form_validation->set_rules('dark-mode', 'Check box', 'required');
        if($this->input->post('status') == TRUE){
                $GLOBALS['dark_mode'] = 1;
            }else{
                $GLOBALS['dark_mode'] = 0;
            }
            /*$GLOBALS['page_title'] = 'My profile';
            $userid = $this->session->userdata('userid');
            $data['res']           = $this->user_model->my_profile($userid);
            $data['mode'] = $GLOBALS['dark-mode'];
            $this->load->view('user/header');
            $this->load->view('user/profile', $data);
            $this->load->view('user/footer');*/
            redirect('user/load_my_profile');
    }

    public function load_task_data() //Load task data to user dashboard
    {
        $today_filter = '';
        $filter = '';
        $search_string = '';
        $sort_type = '';
        $date = '';
        $offset = 0;
        if($this->input->post('offset')){
            $offset = $this->input->post('offset');
        }
        if($this->input->post('search_string')){
            $search_string = $this->input->post('search_string');
        }
        if($this->input->post('project_filter')){
            $filter = json_decode($this->input->post('project_filter'));
        }
        if ($this->input->post('type',TRUE)) {
            //load task data into user dashboard page
            $sort_type = $this->input->post('type', TRUE);
            if($this->input->post('filter')){
                $today_filter = 'today';
            }
            $task_details['data'] = $this->user_model->get_task_details($sort_type,$date,$filter,$today_filter,$search_string,$offset); //get task data
            if($task_details['data'] == NULL){ //if no data, send failure message
                $task_details['status'] = FALSE;
                $task_details['data'] = NULL;
                $task_details['msg'] = "No activity in this date.";
            }else{ //if data is present, send the data
                $task_details['status'] = TRUE;
                $task_details['count'] = $this->user_model->load_task_count($filter,$today_filter,$search_string);
                $task_details['offset'] = $offset;
            }
            echo json_encode($task_details);
        }else if(!empty($this->input->post('chart_type'))){
            //load task data into employee activities page
            $date = $this->input->post('date');
            $chart_type = $this->input->post('chart_type');
            $task_details['data'] = $this->user_model->get_task_details($chart_type,$date,$filter); //get task data
            if($task_details['data'] == NULL){ //if no data, send failure message
                    $task_details['status'] = FALSE;
                    $task_details['data'] = array();
                    $task_details['msg'] = "No activity in this date.";
            }else{ //if data is present, send the data
                $task_details['status'] = TRUE;
            }
            echo json_encode($task_details);
        }
    }

    //stop the old task by updating end time(in user dashboard page)
    public function get_running_task()
    {
        $result['data'] = $this->user_model->running_task_data();
        if($result['data']['login_data'] == NULL && $result['data']['task_data'] == NULL){
            $result['status'] = FALSE;
            $result['msg'] = 'No running tasks found.';
        }else{
            $result['status'] = TRUE;
        }
        echo json_encode($result);
    }

    //fetch project names to dashboard page
    public function get_projects(){
        $data['result'] = $this->user_model->get_project_name();
        if($data == NULL){
            $project_data['status'] = FALSE;
            $project_data['result'] = NULL;
            $project_data['msg'] = "No projects assigned to you.";
        }else{
            $project_data['status'] = TRUE;
            $project_data['result'] = $data;
        }
        echo json_encode($project_data);
    }

    //Start timer function
    public function start_timer()
    {
        // $task_type   = $this->input->post('id');
        $data['userid'] = $this->userid;

        if($this->input->post('id')){
            $data['task_id'] = $this->input->post('id'); //task id is sent through ajax call
        }else if($this->input->get('id')){
            $data['task_id'] = $this->input->get('id'); //task id is sent through url
        }
        if($data['task_id'] == 'undefined'){ //if task id is not sent properly, send failure message
            $output_result['status'] = FALSE;
            $output_result['msg']    = "task-id not sent.";
            echo json_encode($output_result); //send response to the ajax call
        }else{
            $data['task_type'] = 'task';
            if($this->input->post('time')){
                $data['start_time'] = $this->input->post('time');
            }
            else{
                $data['start_time'] = date('Y-m-d H:i:s');
            }
            $result['details'] = $this->user_model->start_timer($data); //start the timer for the requested task id 
            if ($result['details'] == FALSE) {
                $output_result['status'] = FALSE;
                $output_result['msg']    = "Timer not initiated.";
            } else {
                $output_result['status'] = TRUE;
                $output_result['msg']    = "Timer started.";
                $output_result['data'] = $result;
            }
            //@TDOD Handle UI attach event for sending all the time AJAX.
            if($this->input->get('id')){ //if the request is sent via url, then redirect to the dashboard page
                redirect('user/index','refresh');
            }else{
                echo json_encode($output_result); //send response to the ajax call
            }
        }

    }

    //Validate input time format(OR type of input time)
    public function validate_time(){
        if($this->user_model->validate_time($this->input->post('time')) == TRUE){
            return TRUE;
        }else{
            $this->form_validation->set_message('validate_time', 'End time cannot be less than Start time.');
            return FALSE;
        }   
    }

    //Stop Timer function
    public function stop_timer()
    {
        $post_data = $this->input->post();
        //$end_time = (!empty($post_data['stop-end-time'])) ? $post_data['stop-end-time'] : '';//set end time if end time is sent
        $data['userid'] = $this->userid;
        $data['end_time'] = (isset($post_data['time']))?$post_data['time']:date('Y:m:d H:i:s');
        $data['task_desc'] = isset($post_data['task-description']) ? $post_data['task-description'] : '';//get task description if sent
        $data['flag'] = isset($post_data['flag']) ? $post_data['flag'] : '';//set flag if flag is sent

        if ($this->input->post('id')) { //task id is sent through post request
            $task_id = $this->input->post('id', TRUE);
            $data['task_id'] = $task_id;
            //if id is sent through post request, go to stop timer function
            $result['details'] = $this->user_model->stop_timer($data);
            if ($result == FALSE) {
                $output_result['status'] = FALSE;
                $output_result['msg']    = "Something went wrong.";
            } else {
                $output_result['flag']   = $result;
                $output_result['status'] = TRUE;
                $output_result['msg']    = "Timer stopped.";
            }
            echo json_encode($output_result);
        } else {
            $task_id = $this->input->get('id', TRUE);
            $data['task_id'] = $task_id;
            if ($task_id == '') { //if task id is not sent through get request, send failure message
                $output_result['status'] = FALSE;
                $output_result['msg'] = "Bad request, parameter missing.";
                $output_result['task_info'] = $this->user_model->task_status();
                $output_result['project_list'] = $this->user_model->get_project_name();
                $this->load->template('user/user_dashboard', $output_result);
            } else { //if id is sent through get request, go to stop timer function
                $this->form_validation->set_rules('time','Time','required|trim|callback_validate_time');
                if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to the form
                    $output_result['task_info'] = $this->user_model->task_status();
                    $output_result['project_list'] = $this->user_model->get_project_name();
                    $this->load->template('user/user_dashboard', $output_result);
                }else{
                    $result = $this->user_model->stop_timer($data);
                    if ($result == FALSE) {
                        $output_result['status'] = $result;
                        $output_result['msg']    = "Something went wrong.";
                        $output_result['task_info'] = $this->user_model->task_status();
                        $output_result['project_list'] = $this->user_model->get_project_name();
                        $this->load->template('user/user_dashboard', $output_result);
                    } else if ($result == TRUE) {
                        redirect('user');
                    }
                }
            }
        }
    }

    /*Load employee activities page
        Contains :
            *Daily,weekly and monthly activities of the user
            *daily_chart,weekly_chart and monthly_chart representing the activities of the user*/
    public function load_employee_activities()
    {
        $GLOBALS['page_title'] = 'My Activities';
        $data['project_list'] = $this->user_model->get_project_name();
        $this->load->template('user/employee_activities',$data);
    }

    //Show daily,weekly,monthly activity chart of the user(employee activities page)
    public function activity_chart()
    {
        //ajax call
        if (isset($_POST['chart_type']) && isset($_POST['date'])) {
            $date = $_POST['date'];
            $chart_type = $_POST['chart_type'];
            $filter = array();
            if($this->input->post('project_filter')){
                $filter = json_decode($this->input->post('project_filter'));
            }
            $chart_data = $this->user_model->get_activity($chart_type, $date, $this->userid, $filter); //get activity of the user for given arguments
            echo json_encode($chart_data);
        }
        else
        {
            $chart_data['status'] = FALSE;
            $chart_data['data'] = NULL;
            $chart_data['msg'] = "Invalid input format.";
            echo json_encode($task_details);
        }
    }

    //fetch project module
    public function get_project_module()
    {
        $projectid      = $this->input->post('id');
        $data['result'] = $this->user_model->get_module_name($projectid); //get project modules for the requested project(project id)
        echo json_encode($data);
    }
    //check whether task exists in order to add the task
    public function task_exists()
    {
        if ($this->user_model->task_exists() == TRUE) {
            $this->form_validation->set_message('task_exists', 'Task name exists.');
            return false;
        } else {
            return true;
        }
    }
    //load add task page OR edit task page
    public function load_add_task()
    {
        if (isset($_GET['t_id'])) {//if task id is sent, load edit task page
            $GLOBALS['page_title'] = 'Edit Task';
            $t_id = $this->input->get('t_id', TRUE);
            $userid = $this->userid;
            $valid_task_id = $this->user_model->validate_task_id($t_id,$userid);
            if($valid_task_id == TRUE){
                $task_data = $this->user_model->get_task_info($t_id,$userid); //get task details for the requested task id
            }else{
                //show_error("You don't have a Task with the requested task-id.");
                $this->session->set_flashdata('failure', "You don't have a Task for this id.");
                $task_data = NULL;
            }
            $this->load->template('user/add_task', $task_data);

        } else {//if task id is not sent, load add task page
            $GLOBALS['page_title'] = 'Add Task';
            $data['result'] = $this->user_model->get_project_name();
            $this->load->template('user/add_task', $data);
        }
    }

    //Add task option to users
    public function add_tasks()
    {
        $std ='';
        $data['action'] = 'add_task';
        //form inputs validation
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|callback_task_exists|xss_clean');
        $this->form_validation->set_rules('project', 'Project name', 'required');
        if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to add task page
            $std = validation_errors();
            $this->session->set_flashdata('failure', $std);
            redirect('user/load_add_task');
        } else { //if inputs are valid, insert task information into db
            $data['userid'] = $this->userid;
            $data['project_module'] = $this->input->post('project_module');
            $data['project_id'] = $this->input->post('project');
            $data['task_name'] = $this->input->post('task_name');
            $data['task_desc'] = $this->input->post('task_desc');
            $data['time_range'] = $this->input->post('time');
            $result = $this->user_model->add_tasks($data);
            if (!$result) { //if not added, redirect to add task page with error message
                $this->session->set_flashdata('failure', 'Unable to add the Task.');
                redirect('user/load_add_task');
            } else { //if add method is successful, redirect with success message
                $this->session->set_flashdata('success', 'A new Task is added.');
                redirect('user/index');
            } 
        }
    }
    
    //Function to edit task
    public function edit_task()
    {
        //form inputs validation
        $std ='';
        $user_id = $this->userid;
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|callback_task_exists|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to edit task page
            $t_id = $this->input->post('task_id', TRUE);
            $std = validation_errors();
            $this->session->set_flashdata('failure', $std);
            redirect('user/load_add_task?t_id='.$t_id);
            //$this->load->template('user/add_task', $task_data);
        } else { //if inputs are valid, update and/or insert task information into db
            $data['action'] = 'edit';
            $data['userid'] = $this->session->userdata('userid');
            $data['project_module'] = $this->input->post('project_module');
            $data['project_id'] = $this->input->post('project');
            $data['task_name'] = $this->input->post('task_name');
            $data['task_id'] = $this->input->post('task_id');
            $data['task_desc'] = $this->input->post('task_desc');
            if(!empty($this->input->post('time'))){
                $data['timings'] = $this->input->post('time');                
            }
            /*$punch_in_time = $this->user_model->get_punch_in_time($user_id);
            $punch_time = strtotime($punch_in_time);
            $punch_date = date('Y-m-d',$punch_time);
            $punch_in_compare = strtotime($punch_date);
            $current_date = date('Y-m-d');*/
            /*** Validate Date, Start Time and End Time Inputs ***/
            /*foreach($data['timings'] AS $time){
                if ($time['date'] == '' || $time['date'] == 'Invalid da' || !(preg_match('/\d{4}-\d{2}-\d{2}/', $time['date']))) {
                    //input date validation
                    $t_id = $this->input->post('task_id', TRUE);
                    $this->session->set_flashdata('date_failure', 'Invalid Task Date.');
                    redirect('user/load_add_task?t_id=' . $t_id);
                } else if ($time['start'] == '' || !(preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $time['start']))) {
                    //check for start time format
                    $t_id = $this->input->post('task_id', TRUE);
                    $this->session->set_flashdata('start_time_error', 'Invalid Start Time.');
                    redirect('user/load_add_task?t_id=' . $t_id);
                } else if ($time['end'] == '' || !(preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $time['end']))) {
                    //check for end time input
                    $t_id = $this->input->post('task_id', TRUE);
                    $this->session->set_flashdata('end_time_error', 'Invalid End Time.');
                    redirect('user/load_add_task?t_id=' . $t_id);
                } else {
                    $task_date = strtotime($time['date']); //entered date
                    $start_date = date('Y-m-d',strtotime($time['start']));
                    $end_date = date('Y-m-d',strtotime($time['end']));
                    $start_time = strtotime($time['start']);
                    $end_time = strtotime($time['end']);
                    if (!empty($punch_in_time)) {
                        //if the user punched in and try to edit task

                        if($task_date > $punch_in_compare) {
                            //trying to edit next day timings

                            $t_id = $this->input->post('task_id', TRUE);
                            $this->session->set_flashdata('greater_date', "You cannot insert future task timings.");
                            redirect('user/load_add_task?t_id=' . $t_id);
                        } else if ($task_date == $punch_in_compare || $task_date < $punch_in_compare) {
                            //if entered time is same as punch in date
                            if ($start_date != $time['date'] || $end_date != $time['date']) {
                                //if start date and end date is not same as respective task date
                                $t_id = $this->input->post('task_id', TRUE);
                                $this->session->set_flashdata('start_time_error', 'Invalid StartDate/EndDate sent.');
                                redirect('user/load_add_task?t_id=' . $t_id);
                            } else if ($end_time < $start_time) {
                                    $t_id = $this->input->post('task_id', TRUE);
                                    $this->session->set_flashdata('less_end_than_start', 'End Time cannot be lesser than Start Time.');
                                    redirect('user/load_add_task?t_id=' . $t_id);
                            }
                        }
                        if ($time['date'] == $punch_date) {
                            if ($start_time < $punch_time) {
                                //If start time is less than punch-in time
                                $t_id = $this->input->post('task_id', TRUE);
                                $this->session->set_flashdata('less_start_than_punch_in', "Start time cannot be lesser than punch-in time.");
                                redirect('user/load_add_task?t_id=' . $t_id);
                            } else if ($end_time < $punch_time) {
                                $t_id = $this->input->post('task_id', TRUE);
                                $this->session->set_flashdata('less_end_than_punch_in', 'End Time cannot be lesser than punch-in time.');
                                redirect('user/load_add_task?t_id=' . $t_id);
                            }
                        }
                    } else {
                        //if the user did not punch-in and try to edit task timings
                        $now = strtotime(date('Y-m-d')); //current date
                        if(($task_date > $now) || ($task_date == $now)) {
                            //trying to edit next day timings

                            $t_id = $this->input->post('task_id', TRUE);
                            $this->session->set_flashdata('no_punchin_date_error', "Punch in to add timings for the day or the next day.");
                            redirect('user/load_add_task?t_id=' . $t_id);
                        } else {
                            if ($start_date != $time['date'] || $end_date != $time['date']) {
                                //if start date and end date is not same as respective task date
                                $t_id = $this->input->post('task_id', TRUE);
                                $this->session->set_flashdata('start_time_error', 'Invalid StartDate/EndDate sent.');
                                redirect('user/load_add_task?t_id=' . $t_id);
                            } else if ($end_time < $start_time) {
                                    $t_id = $this->input->post('task_id', TRUE);
                                    $this->session->set_flashdata('less_end_than_start', 'End Time cannot be lesser than Start Time.');
                                    redirect('user/load_add_task?t_id=' . $t_id);
                            }
                        }
                    }
                }
            }*/
            $result = $this->user_model->add_tasks($data);
            if (!$result) {
                //if edit is unsuccessful, redirect to edit task page with error message
                $t_id = $this->input->post('task_id', TRUE);
                $this->session->set_flashdata('failure', 'Unable to edit.');
                redirect('user/load_add_task?t_id=' . $t_id);
            } else {
                //if edit method is successful, redirect with success message
                $t_id = $this->input->post('task_id', TRUE);
                $this->session->set_flashdata('success', 'Edit task successful.');
                redirect('user/load_add_task?t_id=' . $t_id, 'refresh');
            }
        }
    }

    //Display User Profile Page
    public function load_my_profile()
    {
        $GLOBALS['page_title'] = 'My Profile';
        $data['res']           = $this->user_model->my_profile($this->userid); //get user profile details
        if ($data['res'] != NULL) {
            $this->load->template('user/profile', $data);
        } else {
            $data['res'] = 'No profile Data present.';
            $this->load->template('user/profile', $data);
        }
    }

    //Edit profile function to edit User name and phone number
    public function edit_profile()
    {
        $user_data['name'] = $this->input->post('profile-name');
        $user_data['phone'] = $this->input->post('profile-ph');
        if (!empty($_FILES['change_img']['name'])) { //if image file present, upload image file
            $config['upload_path']   = USER_UPLOAD_PATH;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['overwrite']     = FALSE;
            // $config['encrypt_name']  = TRUE;
            $config['remove_spaces'] = TRUE;
            $config['file_name']     = $_FILES['change_img']['name'];
            $this->load->library('upload', $config);
            /*if(!is_dir($config['upload_path'])){
                mkdir($config['upload_path'], 0755, TRUE);
            }*/
            $this->upload->initialize($config);
            if ($this->upload->do_upload('change_img')) {
                $uploadData = $this->upload->data();
                $picture    = $uploadData['file_name']; //to update profile in db
                $user_data['profile_photo'] = $picture;
                //print_r($uploadData);exit;
                $cropped_points = $this->input->post('cropped_points');
                $c_points = explode(',',$cropped_points);
                $x_axis = $c_points[0];
                $y_axis = $c_points[1];

                $config['image_library'] = 'gd2';
                $config['source_image'] = $uploadData['full_path'];
                $config['maintain_ratio'] = TRUE;
                $config['x_axis'] = $x_axis;
                $config['y_axis'] = $y_axis;
                $img_dim = getimagesize($uploadData['full_path']);
                $config['width'] = $img_dim[0];
                $config['height'] = $img_dim[1];
                $croped_image = base64_decode($_FILES['change_img']['name']);
                $this->load->library('image_lib',$config);
                $this->image_lib->initialize($config);
                $this->image_lib->crop();

                if(!($this->image_lib->crop())){
                    $this->session->set_flashdata('failure', $this->image_lib->display_errors());
                    redirect('user/load_my_profile','refresh');
                }else{
                    $user_data['profile_photo'] = $picture;
                }
            } else {
                //if image is not uploaded, print error message
                $this->session->set_flashdata('failure', $this->upload->display_errors());
                redirect('user/load_my_profile','refresh');
            }
            
        }
        $result = $this->user_model->edit_profile($user_data);
        if($result == TRUE){
            $this->session->set_flashdata('success', 'Profile data updated.');
            redirect('user/load_my_profile');
        } else {
            $this->session->set_flashdata('failure', 'Unable to update profile data.');
            redirect('user/load_my_profile');
        }
    }

    //fetch user activity graph data into user profile page
    public function user_chart()
    {
        //user activity chart (in user profile page)
        $year = $this->input->post('date');
        $data['res'] = $this->user_model->user_chart_data($year); //get user chart data into user user profile page
        if ($data['res'] == FALSE) { //if data is not present, send message
            $final_data['status'] = FALSE;
            $final_data['msg'] = "No chart Data.";
            $final_data['res'] = NULL;
        } else { //if data is present, send data
            $final_data['status'] = TRUE;
            $final_data['res'] = $data['res'];
        }
        echo json_encode($final_data);
    }

    public function password_exists()
    { //While changing password, check whether the password is correct or not
        if ($this->user_model->password_exists() == TRUE) {
            return true;
        } else {
            return false;
        }
    }
    //Change password..
    public function change_password()
    {
        $GLOBALS['page_title'] = 'Change Password';
        //form inputs validation
        $this->form_validation->set_rules('psw1', 'Old Password', 'trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
        $this->form_validation->set_rules('psw11', 'New Password', 'trim|required|min_length[3]|max_length[100]|trim|xss_clean');
        $this->form_validation->set_rules('psw22', 'Confirm Password', 'trim|required|matches[psw11]|min_length[3]|max_length[100]|trim|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are invalid,load change password page with validation error message
            $this->load->template('user/change_password');
        } else { //if inputs are valid, go to change_password method
            $result = $this->user_model->change_password();
            if (!$result) {
                //if change_password method is unsuccessful, redirect with failure message
                $this->session->set_flashdata('err_msg', 'Passwords do not match..');
                $this->load->template('user/change_password');
            } else {
                //if change_password is successful, redirect with success message
                $this->session->set_flashdata('success', 'Successfully Changed.');
                redirect('/login/index', 'refresh');
            }
        }
    }

    //update logout time
    public function update_end_time()
    {
        if($this->input->post('action')){
            $id = $this->input->post('id');
            $time = $this->input->post('time');

            $result = $this->user_model->punchout_previous($time,$id);
            if($result == TRUE){
                $res['status'] = TRUE;
                $res['msg'] = "Punchout of previous day-punch in is done.";
            }else{
                $res['status'] = FALSE;
                $res['msg'] = "Unable to punchout the previous day punch in.";
            }
        }else{
            $punchout_time = $this->input->post('punch_out_time');
            $running_tasks_result = $this->user_model->running_tasks_today();
            if($running_tasks_result == TRUE){
                $res['status'] = FALSE;
                $res['msg'] = "Please stop today's tasks before Punch-out.";
            }else{
                $result = $this->user_model->update_logout_time_web($this->userid,$punchout_time);
                if($result == TRUE){
                    $res['status'] = TRUE;
                    $res['msg'] = 'Punchout successful.';
                }else{
                    $res['status'] = FALSE;
                    $res['msg'] = 'Punchout unsuccessful.';
                }
            }
        }
        echo json_encode($res);    
    }

    public function delete_task_data()
    {
        //delete option
        $table_id = $this->input->post('id');
        $result = $this->user_model->delete_task_data($this->userid, $table_id);
        if($result == TRUE){
            $res['status'] = TRUE;
            $res['msg'] = 'Delete successful.';
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'Delete unsuccessful.';
        }
        echo json_encode($res);
    }

    public function login_activity(){
        $GLOBALS['page_title'] = 'Login Activities';
        $this->load->template('user/login_activities');
    }

    public function user_login_data(){
        $user_data = $this->input->post();
        $login_details = $this->user_model->login_data($user_data);
        if($login_details == NULL){
            $result['status'] = FALSE;
            $result['log_data'] = array();
        }else{
            $total_data = $this->user_model->total_data_count($user_data);
            $result = array("draw" => $user_data['draw'],
            "status" => TRUE,
            "recordsTotal" => $total_data,
            "recordsFiltered" => $total_data,
            "log_data" => $login_details);
        }
        echo json_encode($result);
    }
}
?>
