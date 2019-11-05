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
		}

		// Show login page
		public function index() {
			
			$this->load->view('header');
			$this->load->view('login');
			$this->load->view('footer');
		}
		// Check for user login process
		public function login_process() {
			$this->load->helper('url_helper');
        	$this->load->helper(array('form','url'));
			$this->load->helper('security');
			$this->load->library('form_validation');
			$this->lang->load('form_validation_lang');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[2]|max_length[100]|xss_clean');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('header');
				$this->load->view('login');
				$this->load->view('footer');
			}else{
				
				//$data = array('username' => $this->input->post('username'),'password' => $this->input->post('password'));
				$result = $this->dashboard_model->login_process();
				if ($result) {
					$this->load->view('header');
					$data['total_users'] = $this->dashboard_model->get_users();
					$data['total_tasks'] = $this->dashboard_model->get_tasks();
					$data['total_projects'] = $this->dashboard_model->get_projects();
					$this->load->view('dashboard',$data);
					$this->load->view('footer');
				}else{
					$this->load->helper('url');
					$this->load->library('session');
					$this->session->set_flashdata('err_message', 'Wrong Email/Password');
					$this->load->view('header');
					$this->load->view('login');
					$this->load->view('footer');
				}
			}
		}
		public function logout(){
		    $this->session->unset_userdata('logged_in');
		    $this->load->view('header');
		    $this->load->view('login');
		    $this->load->view('footer');
		}
	}
?>