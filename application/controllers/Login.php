<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model('dashboard_model');
        $this->load->library('session');
        $this->load->helper('url_helper');
        $this->load->helper(array(
            'form',
            'url'
        ));
        $this->load->helper('security');
        $this->lang->load('form_validation_lang');
        //$this->load->library('email');
    }
    // Show login page
    public function index()
    {
        $this->load->helper('url_helper');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $header_data = array();
        $header_data["title"] = "Login";
        $this->load->view('header', $header_data);
        $this->load->view('login');
        $this->load->view('footer');
    }
    // Check for user login process
    public function login_process()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            redirect('/login', 'refresh');
        } else {
            $result = $this->dashboard_model->login_process();
            if ($result == 'admin') {
                redirect('/admin','refresh');
                $this->session->set_userdata('logged_in', TRUE);
            } else if ($result == 'user') {
                $this->session->set_userdata('logged_in', TRUE);
                redirect('/user', 'refresh');
            } else {
                $this->session->set_flashdata('err_message', 'Wrong Email/Password');
                redirect('/login', 'refresh');
            }
        }
    }
    
    //function to logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/login', 'refresh');
    }

    //load forgot password page
    public function forgot_pwd()
    {
        $this->load->view('header');
        $this->load->view('forgotpwd');
        $this->load->view('footer');
    }

    //To store OTP into database
    public function send_otp()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('err_msg', 'Enter valid email.');
            redirect('/login/forgot_pwd', 'refresh');
        } else {
            $send = $this->dashboard_model->send_otp();
            if ($send == true) {
                $result['status'] = TRUE;
                $result['msg'] = "OTP sent...";
            } else {
                $result['status'] = FALSE;
                $result['msg'] = "OTP not sent...";
            }
            echo json_encode($result);
        }
    }

    //function to validate OTP
    public function check_otp()
    {
        $this->form_validation->set_rules('otp', 'OTP', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_msg', 'Please enter OTP.');
            redirect('/login/forgot_pwd', 'refresh');
        } else {
            $check['result'] = $this->dashboard_model->check_otp();
            if (!empty($check['result'])) {
                $this->load->view('header');
                $this->load->view('changepwd', $check);
                $this->load->view('footer');
            } else {
                $this->session->set_flashdata('error_msg', 'Enter correct OTP...');
                redirect('/login/forgot_pwd', 'refresh');
            }
        }
    }

    //function to change password
    public function change_pass()
    {
        $this->form_validation->set_rules('mail', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('psw11', 'New Password', 'trim|required|min_length[3]|max_length[100]|md5|trim|xss_clean');
        $this->form_validation->set_rules('psw22', 'Confirm Password', 'trim|required|matches[psw11]|min_length[3]|max_length[100]|md5|trim|xss_clean');
        $send = $this->dashboard_model->change_password();
        if ($send) {
            $this->session->set_flashdata('success', 'Successfully Changed.');
            redirect('/login', 'refresh');
            return true;
        } else {
            $this->session->set_flashdata('err_msg', 'Unable to reset password.');
            $this->load->view('header');
            $this->load->view('changepwd');
            $this->load->view('footer');
            return false;
        }
    }
}
?>