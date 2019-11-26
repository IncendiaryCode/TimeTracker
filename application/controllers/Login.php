<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Login extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->load->helper('url');
			$this->load->model('dashboard_model');
			$this->load->library('session');
			$this->load->helper('url_helper');
        	$this->load->helper(array('form','url'));
			$this->load->helper('security');
			$this->lang->load('form_validation_lang');
			$this->load->library('email');
		}
		// Show login page
		public function index() {
			$this->load->view('header');
			$this->load->view('login');
			$this->load->view('footer');
		}
		// Check for user login process
		public function login_process() {
			$this->form_validation->set_rules('username', 'Username', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[2]|max_length[100]|xss_clean');
			if ($this->form_validation->run() == FALSE) {
				redirect('/login/index', 'refresh');
			}else{
				$result = $this->dashboard_model->login_process();
				if ($result == 'admin') {
					$this->load->view('header');
					$data['total_users'] = $this->dashboard_model->get_users();
					$data['total_tasks'] = $this->dashboard_model->get_tasks();
					$data['total_projects'] = $this->dashboard_model->get_projects();
					$this->session->set_userdata('logged_in',TRUE);
					$this->load->view('dashboard',$data);
					$this->load->view('footer');
				}else if($result == 'user'){
					//$this->load->view('user/header');
					$this->session->set_userdata('logged_in',TRUE);
					/*$this->load->model('user_model');
					$result = $this->user_model->task_status();
					$task_details['task_status'] = $result['task_status'];
					$task_details['type'] = $result['type'];
					$this->load->view('user/user_dashboard.php',$task_details);
					$this->load->view('user/footer');*/
					redirect('/user/index', 'refresh');
				}else{
					$this->session->set_flashdata('err_message', 'Wrong Email/Password');
					redirect('/login/index', 'refresh');
				}
			}
		}
		public function logout(){
			//$this->session->sess_destroy();
			$result = $this->dashboard_model->logout();
			if($result){
				$this->session->unset_userdata('email');
				$this->session->unset_userdata('userid');
			    $this->session->unset_userdata('logged_in');
			    redirect('/login/index', 'refresh');
			}
		}
		public function load_forgot_pwd(){
			$this->load->view('header');
		    $this->load->view('forgotpwd');
		    $this->load->view('footer');
		}
		public function send_otp(){
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('err_msg', 'Enter valid email.');
				redirect('/login/load_forgot_pwd', 'refresh');
			}else{
				$send = $this->dashboard_model->send_otp();
				if($send){
					return true;
				}else{
					$this->session->set_flashdata('err_msg', 'Error sending OTP.');
					redirect('/login/load_forgot_pwd', 'refresh');
					return false;
				}
			}
		}
		public function check_otp(){
			$this->form_validation->set_rules('otp', 'OTP', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error_msg', 'Please enter OTP.');
				redirect('/login/load_forgot_pwd', 'refresh');
			}else{
				$check['result'] = $this->dashboard_model->check_otp();
				if(!empty($check['result'])){
					$this->load->view('header');
					$this->load->view('changepwd',$check);
					$this->load->view('footer');
				}else{
					$this->session->set_flashdata('error_msg', 'Enter correct OTP...');
					redirect('/login/load_forgot_pwd', 'refresh');
				}
			}
		}
		public function change_pass(){
			$this->form_validation->set_rules('mail', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('psw11','New Password','trim|required|min_length[3]|max_length[100]|md5|trim|xss_clean');
  			$this->form_validation->set_rules('psw22','Confirm Password','trim|required|matches[psw11]|min_length[3]|max_length[100]|md5|trim|xss_clean');
			$send = $this->dashboard_model->change_password();
			if($send){
					$this->session->set_flashdata('success','Successfully Changed.');
					redirect('/login/index', 'refresh');
					return true;
			}else{
				$this->session->set_flashdata('err_msg', 'Unable to reset password.');
				$this->load->view('header');
				$this->load->view('changepwd');
				$this->load->view('footer');
				return false;
			}
		}
	}
?>