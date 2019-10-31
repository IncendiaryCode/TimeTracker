 <?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('dashboard_model');
        $this->load->helper('url_helper');
    }

	public function index()
	{
		$this->lang->load('form_validation_lang');
		$this->load->view('header');
		$data['total_users'] = $this->dashboard_model->get_users();
		$data['total_tasks'] = $this->dashboard_model->get_tasks();
		$data['total_projects'] = $this->dashboard_model->get_projects();
		$this->load->view('dashboard',$data);
		$this->load->view('footer');
	}
}
?>