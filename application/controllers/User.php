<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller
{
    //User panel constructor
    public function __construct()
    {
        parent::__construct();
        //$GLOBALS['dark_mode'] = 0;
            /*if($GLOBALS['dark_mode'] == 0){
                $GLOBALS['dark_mode'] = 0;
            }else{
                $GLOBALS['dark_mode'] = 1;
            }*/
        $this->load->model('user_model');
        $this->load->helper('url');
        $this->load->helper('url_helper');
        $this->load->library('session');
        $this->load->helper(array(
            'form',
            'url'
        ));
        $this->lang->load('form_validation_lang');
        $this->load->library('form_validation');
        $this->load->helper('security');
        $this->load->library('upload');
        if (!$this->session->userdata('logged_in')) { //check for user login
            redirect('login/index', 'refresh'); //if not logged in, redirect to login page
        }
    }
    
    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            //loading user dashboard
            $this->load->view('user/header');
            $task_details['task_info'] = $this->user_model->task_status(); //get details about login time, running tasks
            $this->load->view('user/user_dashboard', $task_details);
            $this->load->view('user/footer');
        }else{
            redirect('login/index', 'refresh');
        }
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
            $this->load->view('user/user_dashboard', $res);
            $this->load->view('user/footer');
        }else{
            //$this->session->set_userdata($res);
            $res['msg'] = 'Login timer started.';
            $this->load->view('user/header');
            $res['task_info'] = $this->user_model->task_status();
            $this->load->view('user/user_dashboard', $res);
            $this->load->view('user/footer');
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
            $type                 = $this->input->get('type', TRUE);
            $date                 = '';
        }else if(!empty($this->input->get('chart_type'))){
            //load task data into employee activities page
            $date = $this->input->get('date');
            if($this->input->get('chart_type') == 'daily_chart'){
                //to display daily activities of the user
                $type = 'daily_chart'; 
            }else if($this->input->get('chart_type') == 'weekly_chart'){
                //to display weekly activities of the user
                $type = 'weekly_chart';
            }else{
                //to display monthly activities of the user
                $type = 'monthly_chart';
            }
        }
        $task_details['data'] = $this->user_model->get_task_details($type,$date); //get task data
        if($task_details['data'] == NULL){ //if no data, send failure message
            $task_details['status'] = FALSE;
            $task_details['data'] = NULL;
            $task_details['msg'] = "No activity in this date.";
        }else{ //if data is present, send the data
            $task_details['status'] = TRUE;
        }
        echo json_encode($task_details);
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
        }else{
            $data['task_type'] = 'task';
            $result['details'] = $this->user_model->start_timer($data); //start the timer for the requested task id 
            if ($result['details'] == FALSE) {
                $output_result['status'] = FALSE;
                $output_result['msg']    = "Timer not initiated.";
            } else {
                $output_result['status'] = TRUE;
                $output_result['msg']    = "Timer started.";
                $output_result['data'] = $result;
            }
            if($this->input->get('id')){ //if the request is sent via url, then redirect to the dashboard page
                redirect('user/index');
            }else{
                echo json_encode($output_result); //send response to the ajax call
            }
        }

    }

    //Stop Timer function
    public function stop_timer()
    {
        $post_data = $this->input->post();
        $end_time = (!empty($post_data['stop_end_time'])) ? $post_data['stop_end_time'] : '';//set end time if end time is sent
        $data['userid'] = $this->session->userdata('userid');
        $data['end_time'] = $end_time;
        $data['task_desc'] = isset($post_data['stop_task-description']) ? $post_data['stop_task-description'] : '';//get task description if sent
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
                $this->load->view('user/user_dashboard', $output_result);
                $this->load->view('user/footer');
            } else {
                //if id is sent through get request, go to stop timer function
                $result = $this->user_model->stop_timer($data);
                if ($result == FALSE) {
                    $output_result['status'] = $result;
                    $output_result['msg']    = "Something went wrong.";
                    $this->load->view('user/header');
                    $output_result['task_info'] = $this->user_model->task_status();
                    $this->load->view('user/user_dashboard', $output_result);
                    $this->load->view('user/footer');
                } else if ($result == TRUE) {
                    redirect('user/index','refresh');
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
        $GLOBALS['page_title'] = 'My activities';
        $this->load->view('user/header');
        $this->load->view('user/employee_activities');
        $this->load->view('user/footer');
    }

    //Show daily,weekly,monthly activity chart of the user(employee activities page)
    public function activity_chart()
    {
        //ajax call
        if (isset($_GET['chart_type']) && isset($_GET['date'])) {
            if ($_GET['chart_type'] == 'daily_chart') {
                $chart_type = 'daily_chart';
            }
            if ($_GET['chart_type'] == 'weekly_chart') {
                $chart_type = 'weekly_chart';
            }
            if ($_GET['chart_type'] == 'monthly_chart') {
                $chart_type = 'monthly_chart';
            }
            $date       = $_GET['date'];
            $chart_data = $this->user_model->get_activity($chart_type, $date); //get activity of the user for given arguments
            echo json_encode($chart_data);
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
            $GLOBALS['page_title'] = 'Edit task';
            $t_id = $this->input->get('t_id', TRUE);
            $task_data = $this->user_model->get_task_info($t_id); //get task details for the requested task id
            $this->load->view('user/header');
            $this->load->view('user/add_task', $task_data);
            $this->load->view('user/footer');
        } else {//if task id is not sent, load add task page
            $GLOBALS['page_title'] = 'Add task';
            $this->load->view('user/header');
            $data['result'] = $this->user_model->get_project_name(); 
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
            $GLOBALS['page_title'] = 'Add task';
            $this->load->view('user/header');
            $data['result'] = $this->user_model->get_project_name();
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
                $this->session->set_flashdata('success', 'Successfully added.');
                redirect('user/load_add_task', 'refresh');
            } 
        }
    }
    
    //Function to edit task
    public function edit_task()
    {
        //form inputs validation
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are not valid, return validation error to edit task page
            $this->load->view('user/header');
            $t_id = $this->input->post('task_id', TRUE);
            $task_data = $this->user_model->get_task_info($t_id);
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
            if(!empty($this->input->post('time')))
            $data['timings'] = $this->input->post('time');

            $result = $this->user_model->add_tasks($data);
            if (!$result) {
                //if edit is unsuccessful, redirect to edit task page with error message
                $t_id = $this->input->post('task_id', TRUE);
                $this->session->set_flashdata('failure', 'Unable to edit.');
                redirect('user/load_add_task?t_id=' . $t_id);
            } else {
                //if edit method is successful, redirect with success message
                $t_id = $this->input->post('task_id', TRUE);
                $this->session->set_flashdata('success', 'Edit successful.');
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
                $picture    = array(
                    'profile' => $uploadData['file_name']); //to update profile in db
            } else {
                //if image is not uploaded, print error message
                echo $this->upload->display_errors();
                $picture = 'images.png';
            }
        } else {
            //if image file is not present, assign default image to $picture variable
            $picture = 'images.png';
        }
        $this->user_model->submit_profile($picture);
        if ($this->user_model->submit_profile($picture) == TRUE) {
        //if update successful, redirect to profile page with success message
            $this->session->set_flashdata('success', 'Profile picture updated successfully.');
            redirect('user/load_my_profile','refresh');
        }else{
            //if update is unsuccessful, redirect with error message
            $this->session->set_flashdata('failure', 'Profile picture not updated.');
            redirect('admin/load_my_profile','refresh');
        }
    }

    //Display User Profile Page
    public function load_my_profile()
    {
        $GLOBALS['page_title'] = 'My profile';
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
        $GLOBALS['page_title'] = 'Change password';
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
        $userid = $this->session->userdata('userid');
        $result = $this->user_model->update_logout_time($userid);
        if($result == TRUE){
            $res['status'] = TRUE;
            $res['msg'] = 'Punchout successful.';
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'Punchout unsuccessful.';
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
