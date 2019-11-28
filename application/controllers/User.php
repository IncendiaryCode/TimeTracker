<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class User extends CI_Controller {

		public function __construct()
		{
		    parent::__construct();
		    $this->load->model('user_model');
		    $this->load->helper('url');
	        $this->load->helper('url_helper');
	        $this->load->library('session');
			$this->load->helper(array('form','url'));
			$this->lang->load('form_validation_lang');
			$this->load->library('form_validation');
			$this->load->helper('security');
			$this->load->library('upload');
	        if(!$this->session->userdata('logged_in')){
	        	redirect('login/index','refresh');
	        }
	    }

		public function index()
		{
			//loading user dashboard
			$this->load->view('user/header');
			$task_details['task_info'] = $this->user_model->task_status();
			$this->load->view('user/user_dashboard',$task_details);
			$this->load->view('user/footer');
		}
		public function load_task_data(){
			if(isset($_GET['type'])){
				$type = $this->input->get('type', TRUE);
				$task_details['data'] = $this->user_model->get_task_details($type);
				echo json_encode($task_details);
			}else{
				$status = "No Tasks assigned to this user..";
				echo json_encode($status);
			}
		}
		//Start timer controller
		public function start_timer(){
			$type = $this->input->post('action',TRUE);
			$result = $this->user_model->start_timer($type);
			if(!$result){
				$output_result['status'] = FALSE;
				$output_result['msg'] = "Timer didnot start.";
			}else{
				$output_result['status'] = TRUE;
				$output_result['msg'] = 'Timer started.';
			}
			echo json_encode($output_result);
		}
		//Stop Timer
		public function stop_timer(){
				$post_data = $this->input->post();
				if($this->input->post('id')){
					$task_id = $this->input->post('id',TRUE);
				}else{
					$task_id = $this->input->get('id',TRUE);
				}
				$end_time = isset($post_data['end_time']) ? $post_data['end_time'] : '';
		        if ($task_id == '') {
		            echo "Bad request parameter missing.";
		        }
				$result = $this->user_model->stop_timer($task_id,$end_time);
				if($result == FALSE){
					$output_result['status'] = FALSE;
					$output_result['msg'] = "Timer didnot stop.";
				}else{
					//redirect('user/index','refresh');
					$output_result['status'] = TRUE;
					$output_result['msg'] = 'Timer stop.';
				}
				echo json_encode($output_result);
		}
		//Load employee activities page
		public function load_employee_activities(){
			$this->load->view('user/header');
			$this->load->view('user/employee_activities');
			$this->load->view('user/footer');
		}
		//Show daily,weekly,monthly activity
		public function activity_chart(){
			if(isset($_GET['chart_type']) && isset($_GET['date'])){
				if($_GET['chart_type'] == 'daily_chart'){
					$chart_type = 'daily_chart';
				}
				if($_GET['chart_type'] == 'weekly_chart'){
					$chart_type = 'weekly_chart';
				}
				if($_GET['chart_type'] == 'monthly_chart'){
					$chart_type = 'monthly_chart';
				}
				$date = $_GET['date'];
				$chart_data = $this->user_model->get_activity($chart_type,$date);
				//print_r($chart_data);exit;
				//$chart_data['status'] = "No activity in this date.";
				echo json_encode($chart_data);
			}
		}
		//fetch project module
		public function get_project_module(){
			$projectid = $this->input->post('id');
			$data['result'] = $this->user_model->get_module_name($projectid);
			echo json_encode($data);
		}

		public function task_exists(){
			if($this->user_model->task_exists() == TRUE){
				return true;
			}else{
				return false;
			}
		}
		//Add tasks
		public function add_tasks(){
			if($this->input->post('action') == 'save_and_start'){
				$result=$this->user_model->add_tasks();
		            if(!$result){
		                $output_result['status'] = FALSE;
		                $output_result['msg'] = "Something went wrong.";
		            }else{
		            	//print_r($result);exit;
		            	$data = $this->user_model->start_timer($result);
		            	if($data){
		            		$output_result['status'] = TRUE;
		                	$output_result['msg'] = "Task Saved and Timer started.";
		            	}else{
		            		$output_result['status'] = FALSE;
		                	$output_result['msg'] = "Something went wrong.";
		            	}
		            	echo json_encode($output_result);
		            }
			}
		        $this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|callback_task_exists|xss_clean');
		        $this->form_validation->set_rules('task_desc','Task Description','trim|required');
		        $this->form_validation->set_rules('project_name','Project name','required');
		        $this->form_validation->set_rules('project_module','Module name','required');
		        $this->form_validation->set_rules('task_type','Radio button','required');
		       // $this->form_validation->set_rules('start_date','Task Start Date','required');
		       // $this->form_validation->set_rules('end_date','Task End Date','required');
		        if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('user/header');
					$data['result'] = $this->user_model->get_project_name();
					$this->load->view('user/add_task',$data);
					$this->load->view('user/footer');
		        }
		        else
		        {
		            $result=$this->user_model->add_tasks();
		            if(!$result){
		                $this->load->view('user/header');
		               	$data['result'] = $this->user_model->get_project_name();
		                $data['failure'] = "Something went wrong.";
						$this->load->view('user/add_task',$data);
						$this->load->view('user/footer');
		            }else{
		               	$this->load->view('user/header');
		               	$data['result'] = $this->user_model->get_project_name();
		                $data['success'] = "Successfully added.";
						$this->load->view('user/add_task',$data);
						$this->load->view('user/footer');
		            }
		        }
		}
		//Edit task
		public function load_edit_task(){
				if(isset($_GET['t_id'])){
					$t_id = $this->input->get('t_id', TRUE);
				}else if(isset($_POST['t_id'])){
					$t_id = $this->input->post('t_id', TRUE);
				}else{
					$t_id = $this->input->post('task_id',TRUE);
				}
				$taskid['task_data'] = $this->user_model->get_task_info($t_id);
				$this->load->view('user/header');
				$this->load->view('user/edit_task',$taskid);
				$this->load->view('user/footer');
		}

		public function edit_task(){	
				$this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|callback_task_exists|xss_clean');
				$this->form_validation->set_rules('task_desc','Task Description','trim|required');
				//$this->form_validation->set_rules('end_date','Task End Date','required');
				if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('user/header');
					$t_id = $this->input->post('task_id',TRUE);
					$data['task_data'] = $this->user_model->get_task_info($t_id);
					$this->load->view('user/edit_task',$data);
					$this->load->view('user/footer');
				}else{
				    $result=$this->user_model->add_tasks();
				    if(!$result){
				       	$this->load->view('user/header');
				       	$t_id = $this->input->post('task_id',TRUE);
				        $data['task_data'] = $this->user_model->get_task_info($t_id);
				        $data['failure'] = "Something went wrong.";
						$this->load->view('user/edit_task',$data);
						$this->load->view('user/footer');
				    }else{
				       	$this->load->view('user/header');
				       	$t_id = $this->input->post('task_id',TRUE);
				        $data['task_data'] = $this->user_model->get_task_info($t_id);
				        $data['success'] = "Edit successful.";
						$this->load->view('user/edit_task',$data);
						$this->load->view('user/footer');
				    }
				}
		}
		//Upload profile
		public function upload_profile(){
		    	if(!empty($_FILES['change_img']['name'])){
			    	$config['upload_path'] = '/var/www/html/time_tracker_ci/assets/user/images/user_profiles/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['overwrite'] = FALSE;
					$config['encrypt_name'] = TRUE;
 					$config['remove_spaces'] = TRUE;
				    $config['file_name'] = $_FILES['change_img']['name'];
				    $this->load->library('upload',$config);
		            $this->upload->initialize($config);

					if($this->upload->do_upload('change_img')){
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
				$this->user_model->submit_profile($picture);
				if($this->user_model->submit_profile($picture) == TRUE){
					redirect('user/load_my_profile','refresh');
				}
		}
		//Display My Profile Page
		public function load_my_profile(){
			
			$data['res'] = $this->user_model->my_profile();
			$this->load->view('user/header');
			$this->load->view('user/profile',$data);
			$this->load->view('user/footer');
			
		}
		public function password_exists(){
	        if ($this->user_model->password_exists() == TRUE)
	        { 
	           	return true;
	        }else
	        {
	            return false;
	        }
		}
		//Change password..
		public function change_password(){
		  		$this->form_validation->set_rules('psw1','Old Password','trim|required|min_length[3]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
		  		$this->form_validation->set_rules('psw11','New Password','trim|required|min_length[3]|max_length[100]|trim|xss_clean');
  				$this->form_validation->set_rules('psw22','Confirm Password','trim|required|matches[psw11]|min_length[3]|max_length[100]|trim|xss_clean');
  				
		  		if ($this->form_validation->run() == FALSE)
				{
					$this->load->view('user/header');
					$this->load->view('user/change_password');
					$this->load->view('user/footer');
		        }
		        else
		        {
		            $result=$this->user_model->change_password();
		            if(!$result){
		     			$this->session->set_flashdata('err_msg', 'Passwords do not match..');
		            	$this->load->view('user/header');
						$this->load->view('user/change_password');
						$this->load->view('user/footer');
		            }else{
		            	$this->session->set_flashdata('success','Successfully Changed.');
						redirect('/login/index', 'refresh'); 
		            }
		        }	
		}
		public function update_end_time(){
			$result = $this->user_model->update_logout_time();
			if($result == TRUE){
				$this->load->view('user/header');
				$task_details['task_info'] = $this->user_model->task_status();
				$task_details['msg'] = "Logout time updated.";
				$this->load->view('user/user_dashboard',$task_details);
				$this->load->view('user/footer');
			}else{
				$this->load->view('user/header');
				$task_details['task_info'] = $this->user_model->task_status();
				$task_details['msg'] = "Logout time not updated.";
				$this->load->view('user/user_dashboard',$task_details);
				$this->load->view('user/footer');
			}
		}
	}
?>