<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Add_project extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');
$this->load->library('session');
    }

	public function index()
	{
		
		$this->load->view('header');
		$this->load->view('addproject');
		$this->load->view('footer');
	}
	public function project_exists()
    {

       	$this->load->model('dashboard_model');
        if ($this->dashboard_model->project_exists() == TRUE)
        { 
           
           	return true;
        }else
        {
        	$this->form_validation->set_message('project_exists','User Already Exists.');
            return false;
        }
    }
	public function add_projects(){
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
				$this->load->view('addproject');
				$this->load->view('footer');
               
            }
        }
    }
}
?>