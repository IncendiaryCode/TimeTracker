<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* Function to load header and footer at one place.
* @params $tmeplate string, $vars Array, $return Boolian
* @return Void 
* @author Swasthika M
*/

class MY_Loader extends CI_Loader
{
    public function template($template_name, $vars = array(), $type ='user', $return = FALSE)
    {
    	$this->CI =& get_instance();
    	if ($type == 'user') {
    		$header_path = 'user/header';
    		$footer_path = 'user/footer';

    		//fetch punchin / punchout data
    		$header_data = Array();
    		//$this->model('user_model');
    		//print_r($l_model); exit();
    		//$l_model = new User_model();
    		$this->CI->load->library('session');
    		$this->CI->load->model('user_model');
    		$userid = $this->CI->session->userdata('userid');
    		$header_data['punch_in_time'] = $this->CI->user_model->get_punch_in_time($userid,date('Y-m-d'));
        	$header_data['punchout_status'] = $this->CI->user_model->check_user_punchout();
    	}
    	else
    	{
    		$header_path = 'header';
    		$footer_path = 'footer';
    	}

        if($return)
        {
	        $content  = $this->view($header_path, $header_data, $return);
	        $content .= $this->view($template_name, $vars, $return);
	        $content .= $this->view($footer_path, $vars, $return);

	        return $content;
    	}
	    else
	    {
	        $this->view($header_path, $header_data);
	        $this->view($template_name, $vars);
	        $this->view($footer_path, $vars);
	    }
    }
}
?>