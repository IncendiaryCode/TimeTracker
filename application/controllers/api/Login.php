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
       $this->load->library('verify');
    }


    public function index_post()
    {
        $result = array();
        log_message('info', 'Login Api', false);
        log_message('info', 'username:::'.$this->input->post('username'), false);
        log_message('info', 'password:::'.$this->input->post('password'), false);
        //$post = json_decode(file_get_contents("php://input"), true);
        $post = $this->input->post();
        $data = $this->dashboard_model->login_device($post);
        log_message('info', 'POST :::'.print_r($post, TRUE), false);
        if($data == false)
        {
            $result['success'] = 0;
            $result['msg'] = 'Invalid username or password!';
            $this->response($result, parent::HTTP_NOT_FOUND);

        }else{
            // Create a token from the user data and send it as reponse
            $token = AUTHORIZATION::generateToken(['username' => $post['username']]);
            $data['auth_key'] = $token;
            $result['success'] = 1;
            $result['user_details'] = $data;
            $this->response($result,REST_Controller::HTTP_OK);
        }

    }

    public function details_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
            $post = $this->input->post();
            if(!empty($post['userid'])){
                if(!empty($post['page_no']))
                    $page_no = $post['page_no'];
                else
                    $page_no = 1;
                $data['details'] =  $this->user_model->get_login_details($post['userid'],$page_no);
                $this->response($data, REST_Controller::HTTP_OK);
            }else{
                $data['success'] = 0;
                $data['msg'] = 'Userid is required!';
                $this->response($data, parent::HTTP_NOT_FOUND);
            }
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
    }
}