<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
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
        if (!$this->session->userdata('logged_in')) {
            redirect('login/index', 'refresh');
        }
    }
    public function index()
    {
        //loading user dashboard
        $this->load->view('user/header');
        $task_details['task_info'] = $this->user_model->task_status();
        $this->load->view('user/user_dashboard', $task_details);
        $this->load->view('user/footer');
    }
    public function load_task_data() //Load task data to user dashboard
    {
        if ($this->input->get('type',TRUE)) {
            $type                 = $this->input->get('type', TRUE);
            $date                 = '';
        }else if(!empty($this->input->get('chart_type'))){
            $date = $this->input->get('date');
            if($this->input->get('chart_type') == 'daily_chart'){
                $type = 'daily_chart'; 
            }else if($this->input->get('chart_type') == 'weekly_chart'){
                $type = 'weekly_chart';
            }else{
                $type = 'monthly_chart';
            }
        }
        $task_details['data'] = $this->user_model->get_task_details($type,$date);
        echo json_encode($task_details);
    }
    //Start timer function
    public function start_timer()
    {
        $task_type   = $this->input->post('id');
        $data['userid'] = $this->session->userdata('userid');
        $data['task_type'] = $this->input->post('action', TRUE);
        if($data['task_type']  == 'task')
            $data['task_id'] = $this->input->post('id'); 
        $result = $this->user_model->start_timer($data);
        if (!$result) {
            $output_result['status'] = FALSE;
            $output_result['msg']    = "Timer did not start.";
        }
        else {
            $output_result['status'] = TRUE;
            $output_result['msg']    = "Timer started.";
        }
        echo json_encode($output_result);
    }
    //Stop Timer function
    public function stop_timer()
    {
        $post_data = $this->input->post();
        if ($this->input->post('id')) {
            $task_id = $this->input->post('id', TRUE);
            $end_time = !empty($this->input->post('end_time')) ? $this->input->post('end_time') : '';
            $result = $this->user_model->stop_timer($task_id, $end_time);
            if ($result == FALSE) {
                $output_result['status'] = FALSE;
                $output_result['msg']    = "Something went wrong.";
            }
            else if ($result == TRUE) {
                $output_result['flag']   = $result;
                $output_result['status'] = TRUE;
                $output_result['msg']    = "Timer stop.";
            }
            echo json_encode($output_result);
        }
        else {
            $task_id = $this->input->get('id', TRUE);
            $end_time = isset($post_data['end_time']) ? $post_data['end_time'] : '';
            if ($task_id == '') {
                $output_result['status'] = FALSE;
                $output_result['msg'] = "Bad request parameter missing.";
                $this->load->view('user/header');
                $output_result['task_info'] = $this->user_model->task_status();
                $this->load->view('user/user_dashboard', $output_result);
                $this->load->view('user/footer');

            }else{
                
                $result = $this->user_model->stop_timer($task_id, $end_time);
                if ($result == FALSE) {
                    $output_result['status'] = $result;
                    $output_result['msg']    = "Something went wrong.";
                    $this->load->view('user/header');
                    $output_result['task_info'] = $this->user_model->task_status();
                    $this->load->view('user/user_dashboard', $output_result);
                    $this->load->view('user/footer');
                }
                else if ($result == TRUE) {
                    $output_result['flag']   = $result;
                    $output_result['status'] = TRUE;
                    $output_result['msg']    = "Task stopped and end time is updated.";
                    $this->load->view('user/header');
                    $output_result['task_info'] = $this->user_model->task_status();
                    $this->load->view('user/user_dashboard', $output_result);
                    $this->load->view('user/footer');
                }
            }
        } 
    }
    //Load employee activities page
    public function load_employee_activities()
    {
        $this->load->view('user/header');
        $this->load->view('user/employee_activities');
        $this->load->view('user/footer');
    }
    //Show daily,weekly,monthly activity
    public function activity_chart()
    {
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
            $chart_data = $this->user_model->get_activity($chart_type, $date);
            echo json_encode($chart_data);
        }
    }
    //fetch project module
    public function get_project_module()
    {
        $projectid      = $this->input->post('id');
        $data['result'] = $this->user_model->get_module_name($projectid);
        echo json_encode($data);
    }
    //check whether task exists in order to add the task
    public function task_exists()
    {
        if ($this->user_model->task_exists() == TRUE) {
            $this->form_validation->set_message('task_exists','Task name exists.');
            return false;
            
        }
        else {
            return true;
        }
    }
    //Add tasks
    public function add_tasks()
    {
        $GLOBALS['page_title'] = 'Add Task';
        if ($this->input->post('action') == 'save_and_start') {
            $data['userid'] = $this->session->userdata('userid');
            $data['project_module'] = $this->input->post('project_module');
            $data['project_id'] = $this->input->post('project');
            $data['action'] = $this->input->post('action');
            $data['task_name'] = $this->input->post('task_name');
            $data['task_desc'] = $this->input->post('task_desc');
            $result = $this->user_model->add_tasks($data);
            if (!$result) {
                $output_result['status'] = FALSE;
                $output_result['msg']    = "Something went wrong.";
            }
            else {
                $timer['userid'] = $this->session->userdata('userid');
                $timer['task_type'] = 'task';
                $timer['task_id'] = $result;
                $data = $this->user_model->start_timer($timer);
                if ($data) {
                    $output_result['status'] = TRUE;
                    $output_result['msg']    = "Task Saved and Timer started.";
                } //$data
                else {
                    $output_result['status'] = FALSE;
                    $output_result['msg']    = "Something went wrong.";
                }
                echo json_encode($output_result);
            }
        }
        else {
            $data['action'] = 'add_task';
            $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|callback_task_exists|xss_clean');
            //$this->form_validation->set_rules('task_desc', 'Task Description', 'trim|required');
            $this->form_validation->set_rules('project_name', 'Project name', 'required');
            //$this->form_validation->set_rules('project_module', 'Module name', 'required');
            $this->form_validation->set_rules('task_type', 'Radio button', 'required');
            if ($this->form_validation->run() == FALSE) {
                $GLOBALS['page_title'] = 'Add Task';
                $this->load->view('user/header');
                $data['result'] = $this->user_model->get_project_name();
                $this->load->view('user/add_task', $data);
                $this->load->view('user/footer');
            }
            else {
                $data['userid'] = $this->session->userdata('userid');
                $data['project_module'] = $this->input->post('project_module');
                $data['project_id'] = $this->input->post('project_name');
                $data['task_name'] = $this->input->post('task_name');
                $data['task_desc'] = $this->input->post('task_desc');
                $data['time_range'] = $this->input->post('daterange');
                $result = $this->user_model->add_tasks($data);
                if (!$result) {
                    $this->load->view('user/header');
                    $data['result']  = $this->user_model->get_project_name();
                    $data['failure'] = "Something went wrong.";
                    $this->load->view('user/add_task', $data);
                    $this->load->view('user/footer');
                }
                else {
                    $this->load->view('user/header');
                    $data['result']  = $this->user_model->get_project_name();
                    $data['success'] = "Successfully added.";
                    $this->load->view('user/add_task', $data);
                    $this->load->view('user/footer');
                }
            }
        }
    }
    // Load Edit task Page
    public function load_edit_task()
    {
        $GLOBALS['page_title'] = 'Edit Task';
        if (isset($_GET['t_id'])) {
            $t_id = $this->input->get('t_id', TRUE);
        }
        else if (isset($_POST['t_id'])) {
            $t_id = $this->input->post('t_id', TRUE);
        }
        else {
            $t_id = $this->input->post('task_id', TRUE);
        }
        $taskid['task_data'] = $this->user_model->get_task_info($t_id);
        $this->load->view('user/header');
        $this->load->view('user/edit_task', $taskid);
        $this->load->view('user/footer');
    }
    //Function to edit task
    public function edit_task()
    {
        $GLOBALS['page_title'] = 'Edit Task';
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('task_desc', 'Task Description', 'trim|required');
       // $this->form_validation->set_rules('start_time','Task Start Date','required');
       // $this->form_validation->set_rules('end_time','Task End Date','required');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('user/header');
            $t_id              = $this->input->post('task_id', TRUE);
            $data['task_data'] = $this->user_model->get_task_info($t_id);
            $this->load->view('user/edit_task', $data);
            $this->load->view('user/footer');
        }
        else {
            $data['action'] = 'edit';
            $data['userid'] = $this->session->userdata('userid');
            $data['project_module'] = $this->input->post('project_module');
            $data['project_id'] = $this->input->post('project_name');
            $data['task_name'] = $this->input->post('task_name');
            $data['task_id'] = $this->input->post('task_id');
            $data['task_desc'] = $this->input->post('task_desc');
            $data['time_range'] = $this->input->post('time');
            $result = $this->user_model->add_tasks($data);
            if (!$result) {
                $this->load->view('user/header');
                $t_id              = $this->input->post('task_id', TRUE);
                $data['task_data'] = $this->user_model->get_task_info($t_id);
                $data['failure']   = "Something went wrong.";
                $this->load->view('user/edit_task', $data);
                $this->load->view('user/footer');
            }
            else {
                $this->load->view('user/header');
                $t_id              = $this->input->post('task_id', TRUE);
                $data['task_data'] = $this->user_model->get_task_info($t_id);
                $data['success']   = "Edit successful.";
                $this->load->view('user/edit_task', $data);
                $this->load->view('user/footer');
            }
        }
    }
    //Upload profile
    public function upload_profile()
    {
        if (!empty($_FILES['change_img']['name'])) {
            $config['upload_path']   = '/var/www/html/time_tracker_ci/assets/user/images/user_profiles/';
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
                    'profile' => $uploadData['file_path'].$uploadData['file_name']
                ); //to update profile in db(profile column)
            }
            else {
                
                echo $this->upload->display_errors();
                $picture = '';
            }
        }
        else {
            $picture = '';
        }
        $this->user_model->submit_profile($picture);
        if ($this->user_model->submit_profile($picture) == TRUE) {
            redirect('user/load_my_profile', 'refresh');
        }
    }
    //Display My Profile Page
    public function load_my_profile()
    {
        $GLOBALS['page_title'] = 'My profile';
        $data['res']           = $this->user_model->my_profile();
        $this->load->view('user/header');
        $this->load->view('user/profile', $data);
        $this->load->view('user/footer');
    }

    public function password_exists()
    {
        if ($this->user_model->password_exists() == TRUE) {
            return true;
        }
        else {
            return false;
        }
    }
    //Change password..
    public function change_password()
    {
        $this->form_validation->set_rules('psw1', 'Old Password', 'trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
        $this->form_validation->set_rules('psw11', 'New Password', 'trim|required|min_length[3]|max_length[100]|trim|xss_clean');
        $this->form_validation->set_rules('psw22', 'Confirm Password', 'trim|required|matches[psw11]|min_length[3]|max_length[100]|trim|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('user/header');
            $this->load->view('user/change_password');
            $this->load->view('user/footer');
        }
        else {
            $result = $this->user_model->change_password();
            if (!$result) {
                $this->session->set_flashdata('err_msg', 'Passwords do not match..');
                $this->load->view('user/header');
                $this->load->view('user/change_password');
                $this->load->view('user/footer');
            }
            else {
                $this->session->set_flashdata('success', 'Successfully Changed.');
                redirect('/login/index', 'refresh');
            }
        }
    }
    //update logout time
    public function update_end_time()
    {
        $result = $this->user_model->update_logout_time();
        if ($result == TRUE) {
            $this->load->view('user/header');
            $task_details['task_info'] = $this->user_model->task_status();
            $task_details['msg']       = "Logout time updated.";
            $this->load->view('user/user_dashboard', $task_details);
            $this->load->view('user/footer');
        }
        else {
            $this->load->view('user/header');
            $task_details['task_info'] = $this->user_model->task_status();
            $task_details['msg']       = "Logout time not updated.";
            $this->load->view('user/user_dashboard', $task_details);
            $this->load->view('user/footer');
        }
    }
}
?>