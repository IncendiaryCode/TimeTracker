<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Add_task extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');

    }

	public function index()
	{
		
		$this->load->view('header');
		$data['result'] = $this->dashboard_model->get_project_name();
		$this->load->view('addtask',$data);
		$this->load->view('footer');
	}
	public function username_exists()
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
    }
	public function add_tasks(){
		$this->load->helper('url_helper');
        $this->load->helper(array('form','url'));
		$this->lang->load('form_validation_lang');
		$this->load->library('form_validation');
  		$this->load->helper('security');
  		$this->form_validation->set_rules('user-name','Username','required|min_length[1]|trim|callback_username_exists|xss_clean');
        $this->form_validation->set_rules('task_name','Task Name','trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('description','Task Description','trim|required');
        $this->form_validation->set_rules('chooseProject','Project name','required');

        if ($this->form_validation->run() == FALSE)
		{
		 //echo validation_errors();
			$this->load->helper('url');
			$this->load->view('header');
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
               	$data['result'] = $this->dashboard_model->get_project_name();
				$this->load->view('addtask',$data);
				$this->load->view('footer');
               
            }
        }
	}	
}