<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Forgot_pwd extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->helper('url');
			$this->load->model('dashboard_model');
			$this->load->library('session');
		}
		public function index() {
			$this->load->view('header');
			$this->load->view('forgotpwd');
			$this->load->view('footer');
		}
		public function send_otp(){
			$this->load->helper('form');
			$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			$this->load->helper('url_helper');
			$this->load->helper('security');
			$this->lang->load('form_validation_lang');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('err_msg', 'Enter valid email.');
				$this->load->view('header');
				$this->load->view('forgotpwd');
				$this->load->view('footer');
			}else{
				$send = $this->dashboard_model->send_otp();
				if($send){
					return true;
				}else{
					$this->session->set_flashdata('err_msg', 'Error sending OTP.');
					$this->load->view('header');
					$this->load->view('forgotpwd');
					$this->load->view('footer');
					return false;
				}
			}
		}
		public function check_otp(){
			$this->load->helper('url_helper');
        	$this->load->helper(array('form','url'));
			$this->load->helper('security');
			$this->load->library('form_validation');
			$this->lang->load('form_validation_lang');
			$this->form_validation->set_rules('otp', 'OTP', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error_msg', 'Please enter OTP.');
				$this->load->view('header');
				$this->load->view('forgotpwd');
				$this->load->view('footer');
			}else{
				$check['result'] = $this->dashboard_model->check_otp();
				if(!empty($check['result'])){
					$this->load->view('header');
					$this->load->view('changepwd',$check);
					$this->load->view('footer');
				}else{
					$this->session->set_flashdata('error_msg', 'Enter correct OTP...');
					$this->load->view('header');
					$this->load->view('forgotpwd');
					$this->load->view('footer');
				}
			}
		}
	}
?>