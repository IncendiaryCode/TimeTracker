<?php
   
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Verify.php';
     
class Login extends REST_Controller {
    
      /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->helper(['jwt', 'authorization']); 
       $this->load->database();
       $this->load->model('user_model');
       $this->load->model('dashboard_model');
    }


    public function index_post()
    {
        
        $result = $this->dashboard_model->login_device();
        if($result == false)
        {
            $this->response(['msg' => 'Invalid username or password!'], parent::HTTP_NOT_FOUND);
        }else{
            // Create a token from the user data and send it as reponse
            $token = AUTHORIZATION::generateToken(['username' => $this->input->post('username')]);
            $result['token'] = $token;
            $this->response($result,REST_Controller::HTTP_OK);
        }

    }
}