<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Privacy extends CI_Controller
{
    //User panel constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('url_helper');
        $this->load->library('session');
        $this->load->helper(array(
            'form',
            'url'
        ));
    }
    
    public function index()
    {
        //$this->load->view('user/header');
        $this->load->view('privacy');
        //$this->load->view('user/footer');
    }

}
?>
