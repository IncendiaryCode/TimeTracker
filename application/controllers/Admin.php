<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Admin extends CI_Controller {

		public function __construct()
		{
		    parent::__construct();
		    $this->load->model('dashboard_model');
	        $this->load->helper('url_helper');
	        $this->load->library('session');
		    $this->load->helper(array('form','url'));
			$this->lang->load('form_validation_lang');
			$this->load->library('form_validation');
		  	$this->load->helper('security');
	    }

		public function index()
		{
			if($this->session->userdata('logged_in')){
				//loading admin dashboard
				$this->load->view('header');
				$data['total_users'] = $this->dashboard_model->get_users();
				$data['total_tasks'] = $this->dashboard_model->get_tasks();
				$data['total_projects'] = $this->dashboard_model->get_projects();
				$this->load->view('dashboard',$data);
				$this->load->view('footer');
			}else{
				redirect('login/index','refresh');
			}
		}

		//Load user analytics page
		public function load_snapshot(){
			
			$type = $this->input->get('type',TRUE);
			if($type == 'user'){
				$this->load->view('header');
				$result['data'] = $this->dashboard_model->get_task_details($type);
		        $this->load->view('user_snapshot',$result);
		        $this->load->view('footer');
			}
			else if($type == 'project'){
				$this->load->view('header');
				$result['data'] = $this->dashboard_model->get_task_details($type);
		        $this->load->view('project_snapshot',$result);
		        $this->load->view('footer');
			}
			else if($type == 'task'){
				$this->load->view('header');
				$result['data'] = $this->dashboard_model->get_task_details($type);
		        $this->load->view('task_snapshot',$result);
		        $this->load->view('footer');
			}	
		}
		
		//load list of projects for an ajax call
		public function get_project_list(){
			$data['result'] = $this->dashboard_model->get_project_name();	
			echo json_encode($data);
		}

		//get graph data
		public function get_graph_data(){
			if($this->input->post('project_name') == 'undefined' || $this->input->post('project_name') == ''){
				$data['result'] = NULL;
				$data['status'] = FALSE; 
			}else{
				$data['result'] = $this->dashboard_model->user_graph_data();
				$data['status'] = TRUE;
			}
			echo json_encode($data);
		}
		//To load add user page
	    public function load_add_user(){
	    	$this->load->view('header');
			$this->load->view('adduser');
			$this->load->view('footer');
	    }

		//Function to load add project page
		public function load_add_project(){
			$this->load->view('header');
			$data['names'] = $this->dashboard_model->get_usernames();
			$this->load->view('addproject',$data);
			$this->load->view('footer');
		}

		//Load add task page
	    public function load_add_task(){
	    	$this->load->view('header');
		    $data['names'] = $this->dashboard_model->get_usernames();
		    $data['result'] = $this->dashboard_model->get_project_name();
		   	$this->load->view('addtask',$data);
			$this->load->view('footer');
	    }

	    //To load admin profile
		public function load_profile(){
			$this->load->view('header');
			$data['res']           = $this->dashboard_model->my_profile();
			$this->load->view('admin_profile',$data);
			$this->load->view('footer');
		}

		//function to show admin notifications
		public function load_notification(){
			$this->load->view('header');
			$this->load->view('adminNotifications');
			$this->load->view('footer');
		}

		//To check whether Project exists.....
		public function project_exists()
	    {
	        if ($this->dashboard_model->project_exists() == TRUE)
	        { 
	           	return true;
	        }
	        else
	        {
	            return false;
	        }
	    }

	    //To check whether user exists to add user
	    public function users_exists()
	    {
	        if ($this->dashboard_model->users_exists() == TRUE)
	        { 
	           	return true;
	        }
	        else
	        {
	            return false;
	        }
	    }

	    public function password_exists(){
	        if ($this->dashboard_model->password_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	            return false;
	        }
		}

	    //To Add Projects..
		public function add_projects(){
			if($this->session->userdata('logged_in')){
				if($this->input->post('project_name') == ''){
					$this->form_validation->set_rules('project-name','Project Name','required|min_length[1]|trim|callback_project_exists|xss_clean');
				}else{
					$this->form_validation->set_rules('project_name','Project Name','required');
				}
				$this->form_validation->set_rules('type', 'Radio button', 'required');
		  		
		  		//$this->form_validation->set_rules('project-logo','Project Logo','required');
		  		//$this->form_validation->set_rules('new-module','Project Module','required');
		  		if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('header');
					$data['names'] = $this->dashboard_model->get_usernames();
					$this->load->view('addproject',$data);
					$this->load->view('footer');
					//redirect('admin/load_add_project');
		        }
		        else
		        {
		            $result=$this->dashboard_model->add_projects();
		            if($result == FALSE){
		                $this->session->set_flashdata('err', "Something went wrong.");
		                redirect('admin/load_add_project','refresh');
		            }else{
		            	$this->session->set_flashdata('true', 'Successfully Added.');
		               	redirect('admin/load_add_project','refresh');   
		            }
		        }
		    }
		    else
		    {
	            redirect('login/index','refresh');
	        }
	    }
	    
	    //To add users...
	    public function add_users(){
	    	if($this->session->userdata('logged_in')){
		  		$this->form_validation->set_rules('task_name','Username','required|min_length[2]|trim|callback_users_exists|xss_clean');
		        $this->form_validation->set_rules('task_pass','Password','trim|required|min_length[6]|max_length[100]|md5|trim|xss_clean');
		        $this->form_validation->set_rules('user_email','Email','trim|required|valid_email');
		        //$this->form_validation->set_rules('contact','Contact Number','required|min_length[10]|max_length[10]|numeric');
		        if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('header');
					$this->load->view('adduser');
					$this->load->view('footer');
		        }
		        else
		        {
		            $result=$this->dashboard_model->add_users();
		            if(!$result){
		                $this->session->set_flashdata('err', "Something went wrong.");
		                redirect('admin/load_add_user','refresh');
		            }else{
		            	$this->session->set_flashdata('true', 'Successfully Added.');
		               	redirect('admin/load_add_user','refresh'); 
		            }
		        }
			}
			else
			{
				redirect('login/index','refresh');
			}
		}
		 
	    //get user name list into add task page
	    public function get_username_list(){
	    	$data['users'] = $this->dashboard_model->get_usernames();
	    	echo json_encode($data);
	    }

	    //get project module list to add task page 
	    public function get_project_module(){
	    	$projectid      = $this->input->post('project_id');
	        $data['result'] = $this->dashboard_model->get_module_name($projectid);
	        echo json_encode($data);
	    }

	    //Assign tasks to users
		public function add_tasks(){
			if($this->session->userdata('logged_in')){
		  		//$this->form_validation->set_rules('user-name','Username','required|min_length[1]|trim|xss_clean');
		        $this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|xss_clean');
		        //$this->form_validation->set_rules('description','Task Description','trim|required');
		        $this->form_validation->set_rules('chooseProject','Project name','required');

		        if ($this->form_validation->run() == FALSE)
				{
					//redirect('admin/load_add_task');
					$this->load->view('header');
				    $data['names'] = $this->dashboard_model->get_usernames();
				    $data['result'] = $this->dashboard_model->get_project_name();
				   	$this->load->view('addtask',$data);
					$this->load->view('footer');
		        }
		        else
		        {
		            $result=$this->dashboard_model->assign_tasks();
		            if(!$result){
		            	$this->session->set_flashdata('err', "Something went wrong.");
		                redirect('admin/load_add_task','refresh');
		            }else{
		            	$this->session->set_flashdata('true', 'Successfully Added.');
		               	redirect('admin/load_add_task','refresh');
		            }
		        }
		    }
		    else
		    {
		    	redirect('login/index','refresh');
		    }
		}
		
		//Profile...
		public function upload_profile(){
			if($this->session->userdata('logged_in')){
		    	if(!empty($_FILES['change_img']['name'])){
			    	$config['upload_path'] = '/var/www/html/time_tracker_ci/assets/images/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['overwrite'] = TRUE;
				    $config['file_name'] = $_FILES['change_img']['name'];
				    $this->load->library('upload',$config);
		            $this->upload->initialize($config);
					if($this->upload->do_upload('change_img')){
		                $uploadData = $this->upload->data();
		               // $picture = $uploadData['file_name'];
		                $picture = array(
		                'profile' => $uploadData['file_path'].$uploadData['file_name']);//to update profile in db(profile column)
		            }else{
		            	echo $this->upload->display_errors();
		                $picture = '';
		            }
		        }else{
		            $picture = '';
		        }
				$this->dashboard_model->submit_profile($picture);
				if($this->dashboard_model->submit_profile($picture) == TRUE){
					redirect('admin/load_profile','refresh');
				}
			}else{
				redirect('login/index','refresh');
			}
		}
		
		//Change password..
		public function change_password(){
			if($this->session->userdata('logged_in')){
		  		$this->form_validation->set_rules('old-pass','Old Password','trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
		  		$this->form_validation->set_rules('new-pass','New Password','trim|required|min_length[3]|max_length[100]|trim|xss_clean');
  				$this->form_validation->set_rules('confirm-pass','Confirm Password','trim|required|matches[new-pass]|min_length[3]|max_length[100]|trim|xss_clean');
  				
		  		if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('header');
					$this->load->view('admin_profile');
					$this->load->view('footer');
		        }
		        else
		        {
		            $result=$this->dashboard_model->change_password();
		            if($result == FALSE){
		     			$this->session->set_flashdata('err_msg', 'Unable to change password..');
		            	redirect('admin/load_profile','refresh');
		            }else{
		            	$this->session->set_flashdata('success', 'Password changed successfully.');
		            	redirect('login/index','refresh');
		            }
		        }
			}else{
				redirect('login/index','refresh');
			}	
		}
	}
?>