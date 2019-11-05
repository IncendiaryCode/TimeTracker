<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');
        $this->load->helper('url_helper');
        $this->load->library('session');
    }
    public function index(){
    	$this->load->library('upload');
    	$this->load->helper(array('form', 'url'));
    	$this->lang->load('form_validation_lang');
		$this->load->view('header');
		$this->load->view('profile');
		$this->load->view('footer');
    }
    //upload profile....
    public function upload_profile(){
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
			redirect("profile");
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
	public function change_password(){
		$this->load->helper('url_helper');
        $this->load->helper(array('form','url'));
		$this->lang->load('form_validation_lang');
		$this->load->library('form_validation');
  		$this->load->helper('security');
  		$this->form_validation->set_rules('psw1','Old Password','trim|required|min_length[6]|max_length[100]|md5|trim|callback_password_exists|xss_clean');
  		$this->form_validation->set_rules('psw11','New Password','trim|required|min_length[6]|max_length[100]|md5|trim|xss_clean');
  		$this->form_validation->set_rules('psw22','Confirm Password','trim|required|min_length[6]|max_length[100]|md5|trim|xss_clean');
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
                echo "Something went wrong.";
            }else{
            	$this->load->helper('url');
            	$this->load->view('header');
				$this->load->view('login');
				$this->load->view('footer');
               
            }
        }
	}
}
?>