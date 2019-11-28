<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license   http://www.dbad-license.org/
 */
class Verify extends \CI_Controller {

	public function __construct() {
       $this->_CI = &get_instance();
       $this->_CI->load->helper(['jwt', 'authorization']); 
       //$this->load->database();
       //$this->load->model('user_model');
       //$this->load->model('dashboard_model');
    }

	public function verify_request($headers)
	{
	    
	    // Extract the token
	    $token = $headers['Authorization-Key'];
	    // Use try-catch
	    // JWT library throws exception if the token is not valid
	    try {
	        // Validate the token
	        // Successfull validation will return the decoded user data else returns false
	        $data = AUTHORIZATION::validateToken($token);
	        if ($data === false) {
	            $status = REST_Controller::HTTP_UNAUTHORIZED;
	            $response = ['success'=>0,'status' => $status, 'msg' => 'Unauthorized Access!'];
	            //$this->response($response, $status);
	            return $response;
	            exit();
	        } else {
	            return $data;
	        }
	    } catch (Exception $e) {
	        // Token is invalid
	        // Send the unathorized access message
	        $status = REST_Controller::HTTP_UNAUTHORIZED;
	        $response = ['success'=>0,'status' => $status, 'msg' => 'Unauthorized Access! '];
	        return $response;
	        //$this->response($response, $status);
	    }
	}

}