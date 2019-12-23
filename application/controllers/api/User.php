<?php
   
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Verify.php';
     
class User extends REST_Controller {
    
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


    public function resetpassword_post()
    {
      $headers = $this->input->request_headers();
      $verify_data = $this->verify->verify_request($headers);
      if(isset($verify_data->username))
        {
            $post = $this->input->post();
            if(!empty($post['userid']) && !empty($post['old_password']) && !empty($post['new_password'])){
                $res = $this->user_model->my_profile($post['userid']);
                $input['email'] = $res['email'];
                $input['password'] = $post['old_password'];
                $res = $this->user_model->check_credentials($input);
                if($res == true){
                  $res = $this->user_model->change_password_device($post['new_password'],$input['email']);
                  if($res){
                    $data['success'] = 1;
                    $data['msg'] = 'Password updated successfully!';
                    $this->response($data, REST_Controller::HTTP_OK);
                  }else{
                    $data['success'] = 0;
                    $data['msg'] = 'Password Error!';
                    $this->response($data, parent::HTTP_NOT_FOUND);
                  }
                }else{
                  $data['success'] = 0;
                  $data['msg'] = 'Invalid Old Password!';
                  $this->response($data, parent::HTTP_NOT_FOUND);
                }
                
            }else{
                $data['success'] = 0;
                $data['msg'] = 'Fields are invalid!';
                $this->response($data, parent::HTTP_NOT_FOUND);
            }
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
    }
}