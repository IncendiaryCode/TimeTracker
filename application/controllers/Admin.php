<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Admin extends CI_Controller {

		//Admin panel contructor
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
			if($this->session->userdata('logged_in') == FALSE || $this->session->userdata('user_type') == 'user'){ //login check
				redirect('login/index','refresh'); //if not logged in, move to login page
			}
		}

		public function index()
		{
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$data['total_users'] = $this->dashboard_model->get_users();
			$data['total_tasks'] = $this->dashboard_model->get_tasks();
			$data['total_projects'] = $this->dashboard_model->get_projects();
			$data['top_users'] = $this->dashboard_model->get_top_users();
			$data['top_projects'] = $this->dashboard_model->get_top_projects();
			$this->load->view('dashboard',$data);
			$this->load->view('footer');
		}

		//Load user analytics page
		public function load_snapshot()
		{
			$result = array();
			$get_data = $this->input->get();
			if($this->input->get('type')){
				$type = $this->input->get('type',TRUE);
			}else{
				$type = $this->input->post('type',TRUE);
			}

			if ($type != 'task') {
				$header_data = array();
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
			}
			if($type == 'user'){  //load user snapshot page
				// $this->load->view('header');
				$result['data'] = $this->dashboard_model->get_task_details($type); //get user information
		        $this->load->view('user_snapshot',$result);
		        $this->load->view('footer');
			}
			else if($type == 'project'){ //load project snapshot page
				// $this->load->view('header');
				$result['data'] = $this->dashboard_model->get_task_details($type); //get project information
		        $this->load->view('project_snapshot',$result);
		        $this->load->view('footer');
			}
			else if($type == 'task'){
				$draw = intval($this->input->post("draw"));
				$result['data'] = $this->dashboard_model->get_task_details($type); //get task information
				if($result['data'] == NULL){ //if no data, return failure message
					$final_result['status'] = FALSE;
					$final_result['msg'] = "No results Found.";
				}else{ //if data present, send the data
					$total_data = $this->dashboard_model->original_task_data($type,$id='');
					$final_result['status'] = TRUE;
					$final_result = array(
					            "draw" => $draw,
					            "status" => TRUE,
					            "recordsTotal" => $total_data,
					            "recordsFiltered" => $total_data,
					            "data" => $result['data']
					        );
				}
		        echo json_encode($final_result); //send response data to ajax call
			}
		}

		//load edit project page
		public function load_edit_project(){
			$header_data = array();
			$project_id = $this->input->get('project_id');
			$check_proj_id = $this->dashboard_model->check_project_id($project_id);
			if($check_proj_id == FALSE){
				show_error("Project doesn't exist.");
			}else{
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
				$get_project_data['project_data'] = $this->dashboard_model->load_edit_project_data($project_id);
				$this->load->view('edit_project',$get_project_data);
				$this->load->view('footer');
			}
		}

		//assign user to the project (in project_details.php)
		public function assign_user_to_project()
		{
			$user_id = $this->input->post('assigning-user-name');
			$project_id = $this->input->post('project-id');
			$result = $this->dashboard_model->assign_user($user_id,$project_id); //assign user to the project
			if($result == TRUE){ //if assign is successful, send success message
				$this->session->set_flashdata('success','User Assigned Successfully.');
				redirect('admin/load_project_detail?project_id='.$project_id,'refresh');
			}else{ //if already assigned or assign method fails, send failure message
				$this->session->set_flashdata('error','Unable to assign the user!');
				redirect('admin/load_project_detail?project_id='.$project_id,'refresh');
			}
		}
		
		/*load user details page
			Page has the data about the opted project
			Contains :
				*Tasks assigned to the project
				*Personal info
				*Time spent by the user on the project
				*Time spent by the user on each task of the project */
		public function load_userdetails_page()
		{
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$result['data'] = $this->dashboard_model->get_user_data(); //get project details of a chosen user
			$this->load->view('user_detail',$result);
			$this->load->view('footer');
		}

		/*load project details page
			Page has the data about the opted project
			Contains :
				*Users assigned to the project
				*Tasks in the project
				*Time spent for the project
				*Time spent by the user on the project
				*Time spent by each task of the project */	
		public function load_project_detail()
		{
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$result['data'] = $this->dashboard_model->get_project_data($this->input->get('project_id')); //get project details of a chosen project
			$result['user_names'] = $this->dashboard_model->get_usernames(); //get usernames list to assign project.
			$this->load->view('project_details',$result);
			$this->load->view('footer');
		}

		public function load_task_snapshot()
		{
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$load_data['users'] = $this->dashboard_model->get_usernames();
			$load_data['projects'] = $this->dashboard_model->get_project_name();
			$this->load->view('header', $header_data);
			$this->load->view('task_snapshot',$load_data); //contains task information(chart,table containing dat about all tasks)
			$this->load->view('footer');
		}

		//get user gragh data (in user_detail.php)
		public function user_chart()
		{
			if($this->input->post('type')){
				$type = $this->input->post('type');
				$result['data'] = $this->dashboard_model->get_user_chart($type); //contains date,total number of tasks and time spent in the date
				if($result['data'] == NULL || $result['data'] == FALSE){
					$result['status'] = FALSE;
					$result['data'] = NULL;
					$result['msg'] = "No data Found.";
				}else{
					$result['status'] = TRUE;
				}	
			}
			echo json_encode($result);
		}

		//Load Task table
		public function user_task_table()
		{
			//type to check whether the request is from user_detail page or project_details page
			//type = user_task (for user_detail.php) OR type = user_project (for project_details.php)
			$table_type = $this->input->post('type');
			$draw = intval($this->input->post("draw"));
			$result['data'] = $this->dashboard_model->user_task_data($table_type); //contains taskname,project of the task and timespent on the task
			if($result['data'] == NULL || $result['data'] == FALSE){
				$result['status'] = FALSE;
				$result['data'] = NULL;
				$result['msg'] = "No results Found.";
			}else{
				if(!empty($this->input->post('project_id'))){
					$id = $this->input->post('project_id');
				}else{
					$id = $this->input->post('user_id');
				}
				$total_data = $this->dashboard_model->original_task_data($table_type,$id);
				$result['status'] = TRUE;
				$result = array(
					            "draw" => $draw,
					            "recordsTotal" => $total_data,
					            "recordsFiltered" => $total_data,
					            "data" => $result['data']
					        );
			}
			echo json_encode($result);
		}

		//Load Project table
		public function user_project_table()
		{
			//type to check whether the request is from user_detail page or project_details page
			//type = project_task (for user_detail.php) OR project_user (for project_details.php)
			$table_type = $this->input->post('type');
			$draw = intval($this->input->post("draw"));
			$result['data'] = $this->dashboard_model->user_project_data($table_type);
			if($result['data'] == NULL || $result['data'] == FALSE){
				$result['status'] = FALSE;
				$result['data'] = NULL;
				$result['msg'] = "No results Found.";
			}else{
				if(!empty($this->input->post('project_id'))){
					$id = $this->input->post('project_id');
				}else{
					$id = $this->input->post('user_id');
				}
				$total_data = $this->dashboard_model->original_task_data($table_type,$id);
				$result['status'] = TRUE;
				$result = array(
					            "draw" => $draw,
					            "recordsTotal" => $total_data,
					            "recordsFiltered" => $total_data,
					            "data" => $result['data']
					        );
			}
			echo json_encode($result);
		}

		public function get_project_list()
		{
			if($this->input->post('type')){
				if($this->input->post('type') == 'get_graph_data'){ //request is to get graph data for dashboard page
					$data['result'] = $this->dashboard_model->dashboard_graph();//graph data in admin dashboard page
					if($data['result'] == NULL){
						$data['status'] = FALSE;
						$data['result'] = NULL;
					}else{
						$data['status'] = TRUE;
					}
				}else if($this->input->post('type') == 'get_user'){ //request is to get project list in user_snapshot page
					$data['result'] = $this->dashboard_model->get_project_name(); //Project list(Select Project option for users chart in user_snapshot page)
					if($data['result'] == FALSE){
						$data['status'] = FALSE;
						$data['result'] = NULL;
					}else{
						$data['status'] = TRUE;
					}
				}
			}else{
				$data['result'] = NULL;
				$data['status'] = FALSE;
			}
			echo json_encode($data);
		}

		//contains graph data(in user_snapshot page)
		public function get_graph_data()
		{
			$data['result'] = $this->dashboard_model->user_graph_data(); //contains username and time used by the user
			if($data['result'] == NULL){
				$data['result'] = NULL;
				$data['status'] = FALSE;
			}else{
				$data['status'] = TRUE;
			}
			echo json_encode($data);
		}
		
		//To load add user page
	    public function load_add_user()
	    {
	    	//Admin can add new user here..
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$this->load->view('adduser');
			$this->load->view('footer');
	    }

		//Function to load add project page
		public function load_add_project()
		{
			//Admin can add new project here..
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$data['names'] = $this->dashboard_model->get_usernames(); //to get the list of usernames to assign the project
			$this->load->view('addproject',$data);
			$this->load->view('footer');
		}

		//Load add task page
	    public function load_add_task()
	    {
	    	//Admin can add task in this page
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$data['names'] = $this->dashboard_model->get_usernames(); //to get the list of usernames to assign the task
			$data['result'] = $this->dashboard_model->get_project_name(); //to get the list of projects to add the task to it
			$this->load->view('addtask',$data);
			$this->load->view('footer');
	    }

	    //To load admin profile
		public function load_profile()
		{
			$header_data = array();
			$header_data['profile'] = $this->session->userdata('user_profile');
			$this->load->view('header', $header_data);
			$data['res']           = $this->dashboard_model->my_profile(); //Contains profile information of the admin
			$this->load->view('admin_profile',$data);
			$this->load->view('footer');
		}

		//While adding project, check whether Project exists
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

	    //While adding user, check whether user exists
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

	    //While changing password, check whether the user input password is valid or not
	    public function password_exists()
	    {
	        if ($this->dashboard_model->password_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	            return false;
	        }
		}

	    //To Add Projects..
		public function add_projects()
		{
			if($this->input->post('project_name') == ''){
				//form inputs validation
				$this->form_validation->set_rules('project-name','Project Name','required|min_length[1]|trim|callback_project_exists|xss_clean');
			}else{
				$this->form_validation->set_rules('project_name','Project Name','required');
			}
			if ($this->form_validation->run() == FALSE) //if inputs are not valid, return validation error to add project page
			{
				//load add project page
				$header_data = array();
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
				$data['names'] = $this->dashboard_model->get_usernames();
				$this->load->view('addproject',$data);
				$this->load->view('footer');
				//redirect('admin/load_add_project');
	        }
	        else
	        {
	        	if (!empty($_FILES['project-logo']['name'])) { //if project logo is given, the upload it and insert project logo into db.

		            $config['upload_path']   = UPLOAD_PATH;
		            $config['allowed_types'] = 'gif|jpg|png|jpeg';
		            $config['overwrite']     = FALSE;
		           // $config['encrypt_name']  = TRUE;
		            $config['remove_spaces'] = TRUE;
		            $config['file_name']     = $_FILES['project-logo']['name'];
		            $this->load->library('upload', $config);
		            $this->upload->initialize($config);
		            if ($this->upload->do_upload('project-logo')) { //upload project logo
		                $uploadData = $this->upload->data();
		                $picture    = $uploadData['file_name']; //to insert project logo into db
		            } else {
		                //if upload is not successful, print upload errors
		                echo $this->upload->display_errors();
		                $picture = 'project.png';
		            }
		        }
		        else {
		            $picture = 'project.png';
		        }
	            $result=$this->dashboard_model->add_projects($picture); //insert project into db
	            if($result == FALSE){ //if not added, redirect to add project page with error message
	                $this->session->set_flashdata('err', "Something went wrong.");
	                redirect('admin/load_add_project','refresh');
	            }else{ //if add method is successful, redirect with success message
	            	$this->session->set_flashdata('true', 'Successfully Added.');
	            	redirect('admin/load_add_project','refresh');
	            }
	        }
	    }
	    
	    //To add users...
	    public function add_users()
	    {
	    	//form inputs validation
    		$this->form_validation->set_rules('task_name','Username','required|min_length[2]|trim|callback_users_exists|xss_clean');
	        $this->form_validation->set_rules('task_pass','Password','trim|required|min_length[6]|max_length[100]|md5|trim|xss_clean');
	        $this->form_validation->set_rules('user_email','Email','trim|required|valid_email');
	        if ($this->form_validation->run() == FALSE)//if inputs are not valid, return validation error to add users page
			{
				$header_data = array();
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
				$this->load->view('adduser');
				$this->load->view('footer');
	        }
	        else
	        { //if inputs are valid, insert user information into db
	            $result=$this->dashboard_model->add_users();
	            if(!$result){ //if not added, redirect to add users page with error message
	                $this->session->set_flashdata('err', "Something went wrong.");
	                redirect('admin/load_add_user','refresh');
	            }else{ //if add method is successful, redirect with success message
					$this->session->set_flashdata('true', 'Successfully Added.');
					redirect('admin/load_add_user','refresh');
	            }
	        }
		}
		 
	    //get user name list into add task page
	    public function get_username_list()
	    {
	    	$data['users'] = $this->dashboard_model->get_usernames();
	    	echo json_encode($data);
	    }

	    //get project module list to add task page 
	    public function get_project_module()
	    {
	    	$projectid      = $this->input->post('project_id');
	        $data['result'] = $this->dashboard_model->get_module_name($projectid);
	        if($data['result'] == FALSE){
	        	$data['result'] = NULL;
	        	$data['status'] = FALSE;
	        }else{
	        	$data['status'] = TRUE;
	        }
	        echo json_encode($data);
	    }

	    //Assign tasks to users
		public function add_tasks()
		{
			//form inputs validation
	        $this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|xss_clean');
	        $this->form_validation->set_rules('chooseProject','Project name','required');

	        if ($this->form_validation->run() == FALSE)//if inputs are not valid, return validation error to add task page
			{
				//redirect('admin/load_add_task');
				$header_data = array();
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
			    $data['names'] = $this->dashboard_model->get_usernames();
			    $data['result'] = $this->dashboard_model->get_project_name();
				$this->load->view('addtask',$data);
				$this->load->view('footer');
	        }
	        else
	        { //if inputs are valid, insert task information into db
	            $result=$this->dashboard_model->assign_tasks();
	            if(!$result){ //if not added, redirect to add task page with error message
					$this->session->set_flashdata('err', "Something went wrong.");
	                redirect('admin/load_add_task','refresh');
	            }else{ //if add method is successful, redirect with success message
					$this->session->set_flashdata('true', 'Successfully Added.');
					redirect('admin/load_add_task','refresh');
	            }
	        }
		}
		
		//delete user
		public function delete_data()
		{
			if($this->input->post('user_id')){
				//delete user
				$data['result'] = $this->dashboard_model->delete_user($this->input->post('user_id'));
				if(($data['result']) == FALSE){ //if not deleted, send error message
					$data['status'] = FALSE;
					$data['msg'] = "User not removed.";
				}else{ //if deleted, send successs message
					$data['status'] = TRUE;
					$data['msg'] = "User removed successfully.";
				}
			}else if($this->input->post('task_id')){
				//delete task
				$data['result'] = $this->dashboard_model->delete_task($this->input->post('task_id'));
				if(($data['result']) == FALSE){ //if not deleted, send error message
					$data['status'] = FALSE;
					$data['msg'] = "Task not removed.";
				}else{ //if deleted, send successs message
					$data['status'] = TRUE;
					$data['msg'] = "Task removed successfully.";
				}
			}
			else{ //if proper request is not sent, send error message
				$data['status'] = FALSE;
				$data['msg'] = "Something went wrong.";
			}
			echo json_encode($data);
		}

		//Profile...
		public function upload_profile()
		{
			if(!empty($_FILES['change_img']['name'])){ //if image file present, upload image file
				$config['upload_path'] = UPLOAD_PATH;
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['overwrite'] = TRUE;
			    $config['file_name'] = $_FILES['change_img']['name'];
			    $this->load->library('upload',$config);
	            $this->upload->initialize($config);
				if($this->upload->do_upload('change_img')){
	                $uploadData = $this->upload->data();
	               // $picture = $uploadData['file_name'];
	                $picture = $uploadData['file_name'];//to update profile in db
	            }else{
					//if image is not uploaded, print error message
					echo $this->upload->display_errors();
	                $picture = 'icons8-admin-settings-male-100.png';
	            }
	        }else{
				//if image file is not present, assign default image to $picture variable
	            $picture = 'icons8-admin-settings-male-100.png';
	        }
			$this->dashboard_model->submit_profile($picture); //update profile photo into db
			if($this->dashboard_model->submit_profile($picture) == TRUE){
				//if update successful, redirect to profile page with success message
				$this->session->set_flashdata('success', 'Profile picture updated successfully.');
				redirect('admin/load_profile','refresh');
			}else{
				//if update is unsuccessful, redirect with error message
				$this->session->set_flashdata('failure', 'Profile picture not updated.');
				redirect('admin/load_profile','refresh');
			}
		}
		
		//Change password option
		public function change_password()
		{
			//form inputs validation
			$this->form_validation->set_rules('old-pass','Old Password','trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
			$this->form_validation->set_rules('new-pass','New Password','trim|required|min_length[3]|max_length[100]|trim|xss_clean');
			$this->form_validation->set_rules('confirm-pass','Confirm Password','trim|required|matches[new-pass]|min_length[3]|max_length[100]|trim|xss_clean');

			if ($this->form_validation->run() == FALSE) //if inputs are invalid
			{
				//load admin profile with validation error message
				$header_data = array();
				$header_data['profile'] = $this->session->userdata('user_profile');
				$this->load->view('header', $header_data);
				$this->load->view('admin_profile');
				$this->load->view('footer');
	        }
	        else
	        {
				//if inputs are valid, change the password
	            $result=$this->dashboard_model->change_password();
	            if($result == FALSE){
					//if changepassword method is not successful, redirect with failure message
					$this->session->set_flashdata('err_msg', 'Unable to change password..');
					redirect('admin/load_profile','refresh');
	            }else{
					//if changepassword is successful, redirect with success message
					$this->session->set_flashdata('success', 'Password changed successfully.');
					redirect('login/index','refresh');
	            }
	        }
		}
	}
