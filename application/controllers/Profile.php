<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');
        $this->load->helper('url_helper');
    }
    public function index(){
    	$this->lang->load('form_validation_lang');
		$this->load->view('header');
		$this->load->view('adminprofile');
		$this->load->view('footer');
    }
}
?>