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


    public function changepassword_post()
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

    public function send_otp_post()
    {
      $post = $this->input->post();
      if(!empty($post['email']))
      {
          $res = $this->user_model->check_email($post['email']);
          if($res == true){
              $send = $this->dashboard_model->send_otp();
              if ($send == true) {
                $data['success'] = 1;
                $data['msg'] = "OTP sent";
              } else {
                $data['success'] = 0;
                $data['msg'] = "OTP not sent";
              }
              $this->response($data, REST_Controller::HTTP_OK);
          }else{
              $data['success'] = 0;
              $data['msg'] = 'Invalid Email!';
              $this->response($data, parent::HTTP_NOT_FOUND);
          }
      }else{
          $data['success'] = 0;
          $data['msg'] = 'Fields are invalid!';
          $this->response($data, parent::HTTP_NOT_FOUND);
      }
      
    }

    public function validate_otp_post(){
      $post = $this->input->post();
      if(!empty($post['email']) && !empty($post['otp']))
      {
        $result = $this->dashboard_model->check_otp();
        if(!empty($result) || $result !== false) {
          $data['success'] = 1;
          $data['msg'] = "Valid OTP";
        }else{
          $data['success'] = 0;
          $data['msg'] = "Invalid OTP";
        }
        $this->response($data, REST_Controller::HTTP_OK);
      }else{
          $data['success'] = 0;
          $data['msg'] = 'Fields are invalid!';
          $this->response($data, parent::HTTP_NOT_FOUND);
      }
    }

    public function resetpassword_post(){
      $post = $this->input->post();
      if(!empty($post['email']) && !empty($post['new_password'])){
        $res = $this->user_model->change_password_device($post['new_password'],$post['email']);
        if($res){
          $data['success'] = 1;
          $data['msg'] = 'Password updated successfully!';
          $this->response($data, REST_Controller::HTTP_OK);
        }else{
          $data['success'] = 0;
          $data['msg'] = 'Password update failed!';
          $this->response($data, parent::HTTP_NOT_FOUND);
        }
                
      }else{
        $data['success'] = 0;
        $data['msg'] = 'Fields are invalid!';
        $this->response($data, parent::HTTP_NOT_FOUND);
      }
    }

     public function edit_profile_post(){
      $headers = $this->input->request_headers();
      $verify_data = $this->verify->verify_request($headers);
      if(isset($verify_data->username))
      {
           $post = $this->input->post();
           if(!empty($post['userid']))
           {
              if(isset($post['image_data']) && !empty($post['image_data']))
              {
                
                $image_base64 = $post['image_data'];//base64_decode($post['image_data']);
                /*$image_data = explode(',', $image_base64);
                $content = base64_decode($image_data[1]);*/
                $content = base64_decode($image_base64);
                $filename = uniqid() . '.png';
                $file = USER_UPLOAD_PATH . $filename;
                file_put_contents($file, $content);
                $user_data['profile'] = $filename;
              }
              if(isset($post['name']) && !empty($post['name']))
              {
                $user_data['name'] = $post['name'];
              }
              if(isset($post['phone']) && !empty($post['phone']))
              {
                $user_data['phone'] = $post['phone'];
              }
              if(count($user_data) > 0)
              {
                $user_data['modified_on'] = date('Y-m-d H:i:s');
              }
              $result = $this->user_model->update_user_details($post['userid'],$user_data);
              if($result){
                $data['success'] = 1;
                $data['msg'] = 'User Profile updated successfully!';
                $this->response($data, REST_Controller::HTTP_OK);
              }else{
                $data['success'] = 0;
                $data['msg'] = 'User Profile update failed!';
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