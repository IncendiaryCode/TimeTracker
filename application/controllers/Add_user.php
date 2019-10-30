<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Add_user extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');
        $this->load->helper('url_helper');
        $this->load->helper('form');
    }

	public function index()
	{
		
		$this->load->view('header');
		$this->load->view('adduser');
		$this->load->view('footer');
	}
	public function add_users(){
		$this->lang->load('form_validation_lang');
		$this->load->library('form_validation');
  		$this->load->helper('security');
  		$this->form_validation->set_rules('task_name','Username','required|min_length[4]|trim|callback_users_exist|xss_clean');
        $this->form_validation->set_rules('task_pass','Password','trim|required|min_length[6]|max_length[10]|md5|trim|xss_clean');
        $this->form_validation->set_rules('user_email','Email','trim|required|valid_email');
        $this->form_validation->set_rules('contact','Contact Number','required|min_length[10]|max_length[10]|numeric');
        if ($this->form_validation->run() == FALSE)
		{
			$this->form_validation->set_message('users_exists','User Already Exists.');
            return false;
			$this->load->view('adduser');
	
        }
        else
        {
            $this->load->model('dashboard_model');
            $result=$this->dashboard_model->add_users();
            if(!$result){
                echo mysqli_error($result);
            }else{
            	$this->load->view('header');
               	$this->load->view('dashboard');
               	$this->load->view('footer');
            }
        }
	}
	public function users_exists()
    {
       	$this->load->model('dashboard_model');
        if ($this->dashboard_model->users_exists())
        {
           	return true;
        }else
        {
            $this->form_validation->set_message('users_exists','User Already Exists.');
            return false;
        }
    }
}
?>