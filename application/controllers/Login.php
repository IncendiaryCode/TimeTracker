<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller
{
    //Constructor for login
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
        //form inputs validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are invalid, redirect to login page with validation error message
            redirect('/login', 'refresh');
        } else { //if inputs are valid, go to login process
            $result = $this->dashboard_model->login_process();
            if ($result == 'admin') { //if the user type is 'admin', goto admin home page
                redirect('/admin','refresh');
                $this->session->set_userdata('logged_in', TRUE);
            } else if ($result == 'user') { //if the user type is 'user', goto user homepage
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
        $this->load->view('forgotpwd'); //view to reset password
        $this->load->view('footer');
    }

    //To store OTP into database
    public function send_otp()
    {
        //form inputs validation
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('err_msg', 'Enter valid email.');
            redirect('/login/forgot_pwd', 'refresh');
        } else {
            $send = $this->dashboard_model->send_otp(); //otp is inserted into db
            if ($send == true) { //if otp is insertion is successful, send success message
                $result['status'] = TRUE;
                $result['msg'] = "OTP sent...";
            } else { //if failed to insert otp, send failure message
                $result['status'] = FALSE;
                $result['msg'] = "OTP not sent...";
            }
            echo json_encode($result); //send response data to the ajax call
        }
    }

    //function to validate OTP
    public function check_otp()
    {
        //form inputs validation
        $this->form_validation->set_rules('otp', 'OTP', 'trim|required');
        if ($this->form_validation->run() == FALSE) { //if input is invalid, redirect with form validation error
            $this->session->set_flashdata('error_msg', 'Please enter OTP.');
            redirect('/login/forgot_pwd', 'refresh');
        } else { //if input is valid, go to check_otp function
            $check['result'] = $this->dashboard_model->check_otp();
            if (!empty($check['result'])) {
                $this->load->view('header');
                $this->load->view('changepwd', $check); //if otp is correct, get user email and goto change password page
                $this->load->view('footer');
            } else { //if otp is not correct, redirect with error message
                $this->session->set_flashdata('error_msg', 'Enter correct OTP...');
                redirect('/login/forgot_pwd', 'refresh');
            }
        }
    }

    //function to change password
    public function change_pass()
    {
        //form inputs validation
        $this->form_validation->set_rules('mail', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('psw11', 'New Password', 'trim|required|min_length[3]|max_length[100]|md5|trim|xss_clean');
        $this->form_validation->set_rules('psw22', 'Confirm Password', 'trim|required|matches[psw11]|min_length[3]|max_length[100]|md5|trim|xss_clean');
        if ($this->form_validation->run() == FALSE) { //if inputs are invalid,load change password page with validation error message
            $this->load->view('header');
            $this->load->view('changepwd');
            $this->load->view('footer');
        } else { //if inputs are valid, go to change_password method
            $send = $this->dashboard_model->change_password();
            if ($send) {
                //if change_password is successful, redirect with success message
                $this->session->set_flashdata('success', 'Successfully Changed.');
                redirect('/login', 'refresh');
                return true;
            } else {
                 //if change_password method is unsuccessful, redirect with failure message
                $this->session->set_flashdata('err_msg', 'Unable to reset password.');
                $this->load->view('header');
                $this->load->view('changepwd');
                $this->load->view('footer');
                return false;
            }
        }
    }
}
?>