<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Change_pwd extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->helper('url');
			$this->load->model('dashboard_model');
			$this->load->library('session');
		}
		public function index() {
			$this->load->view('header');
			$this->load->view('changepwd');
			$this->load->view('footer');
		}
		public function change_pass(){
			$this->load->helper('form');
			$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			$this->load->helper('url_helper');
			$this->load->helper('security');
			$this->lang->load('form_validation_lang');
			$this->form_validation->set_rules('mail', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('psw11','New Password','trim|required|min_length[3]|max_length[100]|md5|trim|xss_clean');
  			$this->form_validation->set_rules('psw22','Confirm Password','trim|required|matches[psw11]|min_length[3]|max_length[100]|md5|trim|xss_clean');
			if ($this->form_validation->run() == FALSE) {

				$this->session->set_flashdata('err_msg', 'Passwords do not match.');
				$this->load->view('header');
				$this->load->view('forgotpwd');
				$this->load->view('footer');
			}else{
				$send = $this->dashboard_model->change_password();
				if($send){
					$this->load->view('header');
					$this->load->view('login');
					$this->load->view('footer');
					return true;
				}else{
					$this->session->set_flashdata('err_msg', 'Password did not change.');
					$this->load->view('header');
					$this->load->view('changepwd');
					$this->load->view('footer');
					return false;
				}
			}
		}
	}
?>