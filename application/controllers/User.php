<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller
{
    //User panel constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('url');
        $this->load->helper('url_helper');
        //$this->load->driver('session');
        $this->load->library('session');

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
    }
    
    public function index()
    {
        $this->load->view('user/header');
        $task_details['task_info'] = $this->user_model->task_status(); //get details about login time, running tasks
        $task_details['project_list'] = $this->user_model->get_project_name();
        $this->load->view('user/user_dashboard', $task_details);
        $this->load->view('user/footer');
    }

    //start the login timer manually in user dashboard page
    public function save_login_time(){
        $result = $this->user_model->start_login_timer(); //to start the login timer
        $res = array();
        if($result == FALSE){
            //$res['flag'] = 0;
            $res['msg'] = 'Failed to start login timer.';
            $this->load->view('user/header');
            $res['task_info'] = $this->user_model->task_status();
            $res['project_list'] = $this->user_model->get_project_name();
            $this->load->view('user/user_dashboard', $res);
            $this->load->view('user/footer');
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
        if ($this->input->get('type',TRUE)) {
            //load task data into user dashboard page
            $sort_type                 = $this->input->get('type', TRUE);
            $date                 = '';
        if($this->input->get('project_filter')){
            $filter_type = 'proj_filter';
            $filter = json_decode($this->input->get('project_filter'));
        }
        else{
            $filter_type = '';
            $filter = '';
        }
        $task_details['data'] = $this->user_model->get_task_details($sort_type,$filter_type,$date,$filter); //get task data
            if($task_details['data'] == NULL){ //if no data, send failure message
                $task_details['status'] = FALSE;
                $task_details['data'] = NULL;
                $task_details['msg'] = "No activity in this date.";
            }else{ //if data is present, send the data
                $task_details['status'] = TRUE;
                echo json_encode($task_details);
            }
        }else if(!empty($this->input->get('chart_type'))){
            //load task data into employee activities page
            $date = $this->input->get('date');
            if($this->input->get('chart_type') == 'daily_chart'){
                //to display daily activities of the user
                $type = 'daily_chart';
                $task_details['data'] = $this->user_model->get_task_details($type,$filter_type = '',$date,$filter = ''); //get task data
                if($task_details['data'] == NULL){ //if no data, send failure message
                    $task_details['status'] = FALSE;
                    $task_details['data'] = NULL;
                    $task_details['msg'] = "No activity in this date.";
                }else{ //if data is present, send the data
                    $task_details['status'] = TRUE;
                }
                echo json_encode($task_details);
            }else if($this->input->get('chart_type') == 'weekly_chart'){
                //to display weekly activities of the user
                $type = 'weekly_chart';
                if(!preg_match('/^\d{1,4}-[W](\d|[0-4]\d|5[0123])$/',$date)){ //check input format for week number
                    $task_details['status'] = FALSE;
                    $task_details['data'] = NULL;
                    $task_details['msg'] = "Invalid input format.";
                    echo json_encode($task_details);
                }else{
                    $task_details['data'] = $this->user_model->get_task_details($type,$filter_type = '',$date,$filter = ''); //get task data
                    if($task_details['data'] == NULL){ //if no data, send failure message
                        $task_details['status'] = FALSE;
                        $task_details['data'] = NULL;
                        $task_details['msg'] = "No activity in this date.";
                    }else{ //if data is present, send the data
                        $task_details['status'] = TRUE;
                    }
                    echo json_encode($task_details);
                }
            }else{
                //to display monthly activities of the user
                $type = 'monthly_chart';
                $task_details['data'] = $this->user_model->get_task_details($type,$filter_type = '',$date,$filter = ''); //get task data
                if($task_details['data'] == NULL){ //if no data, send failure message
                    $task_details['status'] = FALSE;
                    $task_details['data'] = NULL;
                    $task_details['msg'] = "No activity in this date.";
                }else{ //if data is present, send the data
                    $task_details['status'] = TRUE;
                }
                echo json_encode($task_details);
            }
        }
        
    }

    //stop the old task by updating end time(in user dashboard page)
    public function get_running_task()
    {
        $result['data'] = $this->user_model->running_task_data();
        if($result['data'] == NULL){
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
        $data['userid'] = $this->session->userdata('userid');

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
        $data['userid'] = $this->session->userdata('userid');
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
                $output_result['msg']    = "Timer stop.";
            }
            echo json_encode($output_result);
        } else {
            $task_id = $this->input->get('id', TRUE);
            $data['task_id'] = $task_id;
            if ($task_id == '') { //if task id is not sent through get request, send failure message
                $output_result['status'] = FALSE;
                $output_result['msg'] = "Bad request, parameter missing.";
                $this->load->view('user/header');
                $output_result['task_info'] = $this->user_model->task_status();
                $output_result['project_list'] = $this->user_model->get_project_name();
                $this->load->view('user/user_dashboard', $output_result);
                $this->load->view('user/footer');
            } else { //if id is sent through get request, go to stop timer function
                $this->form_validation->set_rules('time','Time','required|trim|callback_validate_time');
                if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to the form
                    $this->load->view('user/header');
                    $output_result['task_info'] = $this->user_model->task_status();
                    $output_result['project_list'] = $this->user_model->get_project_name();
                    $this->load->view('user/user_dashboard', $output_result);
                    $this->load->view('user/footer');
                }else{
                    $result = $this->user_model->stop_timer($data);
                    if ($result == FALSE) {
                        $output_result['status'] = $result;
                        $output_result['msg']    = "Something went wrong.";
                        $this->load->view('user/header');
                        $output_result['task_info'] = $this->user_model->task_status();
                        $output_result['project_list'] = $this->user_model->get_project_name();
                        $this->load->view('user/user_dashboard', $output_result);
                        $this->load->view('user/footer');
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
        $this->load->view('user/header');
        $this->load->view('user/employee_activities');
        $this->load->view('user/footer');
    }

    //Show daily,weekly,monthly activity chart of the user(employee activities page)
    public function activity_chart()
    {
        //ajax call
        if (isset($_GET['chart_type']) && isset($_GET['date'])) {
            $date       = $_GET['date'];
            if ($_GET['chart_type'] == 'daily_chart') {
                $chart_type = 'daily_chart';
                $chart_data = $this->user_model->get_activity($chart_type, $date); //get activity of the user for given arguments
                echo json_encode($chart_data);
            }
            if ($_GET['chart_type'] == 'weekly_chart') {
                $chart_type = 'weekly_chart';
                if(!preg_match('/^\d{1,4}-[W](\d|[0-4]\d|5[0123])$/',$date)){
                    $chart_data['status'] = FALSE;
                    $chart_data['msg'] = "Invalid input format.";
                    echo json_encode($chart_data);
                }else{
                    $chart_data = $this->user_model->get_activity($chart_type, $date); //get activity of the user for given arguments
                    echo json_encode($chart_data);
                }
            }
            if ($_GET['chart_type'] == 'monthly_chart') {
                $chart_type = 'monthly_chart';
                $chart_data = $this->user_model->get_activity($chart_type, $date); //get activity of the user for given arguments
                echo json_encode($chart_data);
            }
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
        $loggedin_userid = $this->session->userdata('userid');
        if (isset($_GET['t_id'])) {//if task id is sent, load edit task page
            $GLOBALS['page_title'] = 'Edit Task';
            $t_id = $this->input->get('t_id', TRUE);
            $task_data = $this->user_model->get_task_info($t_id); //get task details for the requested task id
            $task_data['punch_in_time'] = $this->user_model->get_punch_in_time($loggedin_userid);
            $this->load->view('user/header');
            $this->load->view('user/add_task', $task_data);
            $this->load->view('user/footer');
        } else {//if task id is not sent, load add task page
            $GLOBALS['page_title'] = 'Add Task';
            $this->load->view('user/header');
            $data['result'] = $this->user_model->get_project_name();
            $data['punch_in_time'] = $this->user_model->get_punch_in_time($loggedin_userid);
            $this->load->view('user/add_task', $data);
            $this->load->view('user/footer');
        }
    }

    //Add task option to users
    public function add_tasks()
    {
        $data['action'] = 'add_task';
        //form inputs validation
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|callback_task_exists|xss_clean');
        $this->form_validation->set_rules('project', 'Project name', 'required');
        if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to add task page
            $GLOBALS['page_title'] = 'Add Task';
            $this->load->view('user/header');
            $data['result'] = $this->user_model->get_project_name();
            $data['punch_in_time'] = $this->user_model->get_punch_in_time($this->session->userdata('userid'));
            $this->load->view('user/add_task', $data);
            $this->load->view('user/footer');
        } else { //if inputs are valid, insert task information into db
            $data['userid'] = $this->session->userdata('userid');
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
                redirect('user');
            } 
        }
    }
    
    //Function to edit task
    public function edit_task()
    {
        //form inputs validation
        $user_id = $this->session->userdata('userid');
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to edit task page
            $GLOBALS['page_title'] = 'Edit Task';
            $this->load->view('user/header');
            $t_id = $this->input->post('task_id', TRUE);
            $task_data = $this->user_model->get_task_info($t_id);
            $task_data['punch_in_time'] = $this->user_model->get_punch_in_time($user_id);
            $this->load->view('user/add_task', $task_data);
            $this->load->view('user/footer');
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

    //Upload profile
    public function upload_profile()
    {
        if (!empty($_FILES['change_img']['name'])) { //if image file present, upload image file
            $config['upload_path']   = USER_UPLOAD_PATH;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['overwrite']     = FALSE;
            // $config['encrypt_name']  = TRUE;
            $config['remove_spaces'] = TRUE;
            $config['file_name']     = $_FILES['change_img']['name'];
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('change_img')) {
                $uploadData = $this->upload->data();
                $picture    = $uploadData['file_name']; //to update profile in db
            } else {
                //if image is not uploaded, print error message
                $this->session->set_flashdata('failure', $this->upload->display_errors());
                redirect('user/load_my_profile','refresh');
            }
        } else {
            //if image file is not present, assign default image to $picture variable
            //$picture = 'images.png';
            redirect('user/load_my_profile','refresh');
        }
        $this->user_model->submit_profile($picture);
        if ($this->user_model->submit_profile($picture) == TRUE) {
        //if update successful, redirect to profile page with success message
            $this->session->set_flashdata('success', 'Profile picture updated successfully.');
            redirect('user/load_my_profile','refresh');
        }else{
            //if update is unsuccessful, redirect with error message
            $this->session->set_flashdata('failure', 'Profile picture not updated.');
            redirect('user/load_my_profile','refresh');
        }
    }

    //Display User Profile Page
    public function load_my_profile()
    {
        $GLOBALS['page_title'] = 'My Profile';
        $userid = $this->session->userdata('userid');
        $data['res']           = $this->user_model->my_profile($userid); //get user profile details
        if ($data['res'] != NULL) {
            $this->load->view('user/header');
            $this->load->view('user/profile', $data);
            $this->load->view('user/footer');
        } else {
            $data['res'] = 'No profile Data present.';
            $this->load->view('user/header');
            $this->load->view('user/profile', $data);
            $this->load->view('user/footer');
        }
    }

    //Edit profile function to edit User name and phone number
    public function edit_profile()
    {
        $user_data['name'] = $this->input->post('profile-name');
        $user_data['phone'] = $this->input->post('profile-ph');
        $result = $this->user_model->edit_profile($user_data);
        if($result == TRUE){
            $this->session->set_flashdata('success', 'Profile data updated.');
            redirect('user/load_my_profile');
        } else {
            $this->session->set_flashdata('err_msg', 'Unable to update profile data.');
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
            $this->load->view('user/header');
            $this->load->view('user/change_password');
            $this->load->view('user/footer');
        } else { //if inputs are valid, go to change_password method
            $result = $this->user_model->change_password();
            if (!$result) {
                //if change_password method is unsuccessful, redirect with failure message
                $this->session->set_flashdata('err_msg', 'Passwords do not match..');
                $this->load->view('user/header');
                $this->load->view('user/change_password');
                $this->load->view('user/footer');
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
            $userid = $this->session->userdata('userid');
            $punchout_time = $this->input->post('punch_out_time');
            $result = $this->user_model->update_logout_time_web($userid,$punchout_time);
            if($result == TRUE){
                $res['status'] = TRUE;
                $res['msg'] = 'Punchout successful.';
            }else{
                $res['status'] = FALSE;
                $res['msg'] = 'Punchout unsuccessful.';
            }
        }
        echo json_encode($res);    
    }

    public function delete_task_data()
    {
        //delete option
        $user_rid = $this->session->userdata('userid');
        $table_id = $this->input->post('id');
        $result = $this->user_model->delete_task_data($user_rid, $table_id);
        if($result == TRUE){
            $res['status'] = TRUE;
            $res['msg'] = 'Delete successful.';
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'Delete unsuccessful.';
        }
        echo json_encode($res);
    }
}
?>
