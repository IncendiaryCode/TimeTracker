<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Admin extends CI_Controller {

		public function __construct()
		{
		    parent::__construct();
		    $this->load->model('dashboard_model');
	        $this->load->helper('url_helper');
	        $this->load->library('session');
	    }

		public function index()
		{
			if($this->session->userdata('logged_in')){
				//loading admin dashboard
				$this->lang->load('form_validation_lang');
				$this->load->view('header');
				$data['total_users'] = $this->dashboard_model->get_users();
				$data['total_tasks'] = $this->dashboard_model->get_tasks();
				$data['total_projects'] = $this->dashboard_model->get_projects();
				$this->load->view('dashboard',$data);
				$this->load->view('footer');
			}else{
				$this->load->view('header');
			    $this->load->view('login');
			    $this->load->view('footer');
			}
		}
		//To check whether Project exists.....
		public function project_exists()
	    {

	       	$this->load->model('dashboard_model');
	        if ($this->dashboard_model->project_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	        	$this->form_validation->set_message('project_exists','Project Already Exists.');
	            return false;
	        }
	    }
	    //To Add Projects..
		public function add_projects(){
			if($this->session->userdata('logged_in')){
				$this->load->helper('url_helper');
		        $this->load->helper(array('form','url'));
				$this->lang->load('form_validation_lang');
				$this->load->library('form_validation');
		  		$this->load->helper('security');
		  		$this->form_validation->set_rules('task-name','Project Name','required|min_length[1]|trim|callback_project_exists|xss_clean');
		  		if ($this->form_validation->run() == FALSE)
				{
					$this->load->helper('url');
					$this->load->view('header');
					$this->load->view('addproject');
					$this->load->view('footer');
		        }
		        else
		        {
		            $this->load->model('dashboard_model');
		            $result=$this->dashboard_model->add_projects();
		            if(!$result){
		                echo "Something went wrong.";
		            }else{
		            	$this->load->helper('url');
		            	$this->load->view('header');
		                $data['success'] = "Successfully added.";
						$this->load->view('addproject',$data);
						$this->load->view('footer');
		               
		            }
		        }
		    }else{
	            $this->load->view('header');
	            $this->load->view('login');
	            $this->load->view('footer');
	        }
	    }
	    //To check whether user exists...
	    public function users_exists()
	    {
	       	$this->load->model('dashboard_model');
	        if ($this->dashboard_model->users_exists() == TRUE)
	        { 
	           
	           	return true;
	        }else
	        {
	        	$this->form_validation->set_message('users_exists','User Already Exists.');
	            return false;
	        }
	    }
	    //To add users...
	    public function add_users(){
	    	if($this->session->userdata('logged_in')){
				$this->load->helper('url_helper');
		        $this->load->helper(array('form','url'));
				$this->lang->load('form_validation_lang');
				$this->load->library('form_validation');
		  		$this->load->helper('security');
		  		$this->form_validation->set_rules('task_name','Username','required|min_length[2]|trim|callback_users_exists|xss_clean');
		        $this->form_validation->set_rules('task_pass','Password','trim|required|min_length[6]|max_length[100]|md5|trim|xss_clean');
		        $this->form_validation->set_rules('user_email','Email','trim|required|valid_email');
		        //$this->form_validation->set_rules('contact','Contact Number','required|min_length[10]|max_length[10]|numeric');

		        if ($this->form_validation->run() == FALSE)
				{
					$this->load->helper('url');
					$this->load->view('header');
					$this->load->view('adduser');
					$this->load->view('footer');
		        }
		        else
		        {
		            $this->load->model('dashboard_model');
		            $result=$this->dashboard_model->add_users();
		            if(!$result){
		                echo "Something went wrong.";//mysqli_error($result);
		            }else{
		            	$this->load->helper('url');
		            	$this->load->view('header');
		                $data['success'] = "Successfully added.";
						$this->load->view('adduser',$data);
						$this->load->view('footer');
		               
		            }
		        }
			}else{
				$this->load->view('header');
	            $this->load->view('login');
	            $this->load->view('footer');
			}
		}
		//To check whether username exists inorder to assign task to him
	/*	public function username_exists()
	    {
	       	$this->load->model('dashboard_model');
	        if ($this->dashboard_model->username_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	        	$this->form_validation->set_message('username_exists','User do not exist.');
	            return false;
	        }
	    }*/
	    //Assign tasks to users
		public function add_tasks(){
			if($this->session->userdata('logged_in')){
				$this->load->helper('url_helper');
		        $this->load->helper(array('form','url'));
				$this->lang->load('form_validation_lang');
				$this->load->library('form_validation');
		  		$this->load->helper('security');
		  		$this->form_validation->set_rules('user_name','Username','required|min_length[1]|trim|xss_clean');
		        $this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|xss_clean');
		        $this->form_validation->set_rules('description','Task Description','trim|required');
		        $this->form_validation->set_rules('chooseProject','Project name','required');

		        if ($this->form_validation->run() == FALSE)
				{
					$this->load->helper('url');
					$this->load->view('header');
					$data['names'] = $this->dashboard_model->get_usernames();
					$data['result'] = $this->dashboard_model->get_project_name();
					$this->load->view('addtask',$data);
					$this->load->view('footer');
		        }
		        else
		        {
		            $this->load->model('dashboard_model');
		            $result=$this->dashboard_model->add_tasks();
		            if(!$result){
		                echo "Something went wrong.";
		            }else{
		            	$this->load->helper('url');
		               	$this->load->view('header');
		               	$data['names'] = $this->dashboard_model->get_usernames();
		               	$data['result'] = $this->dashboard_model->get_project_name();
		                $data['success'] = "Successfully added.";
						$this->load->view('addtask',$data);
						$this->load->view('footer');
		               
		            }
		        }
		    }else{
		    	$this->load->view('header');
	            $this->load->view('login');
	            $this->load->view('footer');
		    }
		}
		//Profile...
		public function upload_profile(){
			if($this->session->userdata('logged_in')){
				$this->load->library('session');
		    	if(!empty($_FILES['change_image']['name'])){
			    	$config['upload_path'] = '/var/www/html/time_tracker_ci/assets/images/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['overwrite'] = TRUE;
				    $config['file_name'] = $_FILES['change_image']['name'];
				    $this->load->library('upload',$config);
		            $this->upload->initialize($config);
					if($this->upload->do_upload('change_image')){
		                $uploadData = $this->upload->data();
		               // $picture = $uploadData['file_name'];
		                $picture = array(
		                'profile' => $uploadData['file_name']);//to update profile in db(profile column)
		            }else{
		            	echo $this->upload->display_errors();
		                $picture = '';
		            }
		        }else{
		            $picture = '';
		        }
				$this->dashboard_model->submit_profile($picture);
				if($this->dashboard_model->submit_profile($picture) == TRUE){
					$this->load->view('header');
					$this->load->view('profile');
					$this->load->view('footer');
				}
			}else{
				$this->load->view('header');
				$this->load->view('login');
				$this->load->view('footer');
			}
		}
		public function password_exists(){
			$this->load->model('dashboard_model');
	        if ($this->dashboard_model->password_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	        	//$this->form_validation->set_message('password_exists','Unable to update your password.');
	            return false;
	        }
		}
		//Change password..
		public function change_password(){
			if($this->session->userdata('logged_in')){
				$this->load->helper('url_helper');
		        $this->load->helper(array('form','url'));
				$this->lang->load('form_validation_lang');
				$this->load->library('form_validation');
		  		$this->load->helper('security');
		  		$this->form_validation->set_rules('psw1','Old Password','trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
		  		$this->form_validation->set_rules('psw11','New Password','trim|required|min_length[3]|max_length[100]|trim|xss_clean');
  				$this->form_validation->set_rules('psw22','Confirm Password','trim|required|matches[psw11]|min_length[3]|max_length[100]|trim|xss_clean');
  				
		  		if ($this->form_validation->run() == FALSE)
				{
					$this->load->helper('url');
					$this->load->view('header');
					$this->load->view('profile');
					$this->load->view('footer');
		        }
		        else
		        {
		            $this->load->model('dashboard_model');
		            $result=$this->dashboard_model->change_password();
		            if(!$result){
		     			$this->session->set_flashdata('err_msg', 'Passwords do not match..');
		            	$this->load->view('header');
						$this->load->view('profile');
						$this->load->view('footer');
		            }else{
		            	$data['success'] = "Successfully changed.";
		            	$this->load->helper('url');
		            	$this->load->view('header');
						$this->load->view('login',$data);
						$this->load->view('footer'); 
		            }
		        }
			}else{
				$this->load->view('header');
				$this->load->view('login');
				$this->load->view('footer');
			}	
		}
	}
?>