<?php
   
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Verify.php';
     
class Task extends REST_Controller {
    
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
       $this->load->library('verify');
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_post()
	{
        // Get all the headers
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
        
            $input = $this->input->post();
            $data['success'] = 0;
            if(!empty($input['userid'])){
                $data['success'] = 1;
                $type = 'task';
                $data['details'] =  $this->user_model->get_user_task_info($type,$input);
                $this->response($data, REST_Controller::HTTP_OK);
                //$data = $this->db->get_where("task", ['id' => $input['id']])->row_array();
            }else{
                $data['success'] = 0;
                //$data['details'] = $this->db->get("task")->result();
                $data['msg'] = 'Userid is required!';
                $this->response($data, parent::HTTP_NOT_FOUND);
            }
            
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
	}
      
    /**
     * Post method to add new task or update new task
     *
     * @return Response
    */
    public function create_edit_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
            $data['success'] = 1;
            $post = $this->input->post();
             if(!empty($post['userid']) && !empty($post['task_name']) && !empty($post['task_desc']) && !empty($post['project_id']) && !empty($post['project_module'])){
                if(!empty($post['task_id'])){
                    $post['action'] = 'edit';
                    $result = $this->user_model->add_tasks($post);
                    $data['success'] = 1;
                    $data['msg'] = 'Task Updated Successfully!';
                }else{
                    $post['action'] = 'create';
                    $result = $this->user_model->add_tasks($post);
                    if (!$result) {
                        $data['success'] = 0;
                        $data['msg'] = 'Failed to add task!';
                    }else{
                        $data['success'] = 1;
                        $data['task_id'] = $result;
                        $data['msg'] = 'Task Added Successfully!';
                    }
                }
            }else{
                    $data['success'] = 0;
                    $data['msg'] = 'Parameters error!';
            }
            
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
    }

    public function start_timer_post(){
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
            $post = $this->input->post();
            if(!empty($post['type']) && !empty($post['userid']) && (($post['type'] =='task' && !empty($post['task_id'])) || $post['type'] == 'login')){
                $data['userid'] = $post['userid'];
                $data['task_type'] = $post['type'];//task or login
                $data['task_id'] = $post['task_id'];
                $resp = $this->user_model->start_timer($data);
                if ($resp) {
                    $data['success'] = 1;
                    $data['msg']    = "Timer started Successfully.";
                } //$data
                else {
                    $data['success'] = 0;
                    $data['msg']    = "Something went wrong.";
                }
            }else{
                $data['success'] = 0;
                $data['msg'] = 'Parameters error!';
            }
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
    }
     
     public function stop_timer_post(){
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
            $post = $this->input->post();
            if(!empty($post['type']) && !empty($post['userid']) && && (($post['type'] =='task' && !empty($post['task_id'])) || $post['type'] == 'login')){
                if($post['type'] == 'task')
                {
                    $data['userid'] = $post['userid'];
                    $data['end_time'] = '';
                    $data['task_desc'] = isset($post_data['task_desc'])?$post_data['task_desc']:'';
                    $data['flag'] = 0;
                    $data['task_id'] = $post['task_id'];
                    $resp = $this->user_model->stop_timer($data);
                    if ($resp) {
                        $data['success'] = 1;
                        $data['msg']    = "Timer stopped Successfully.";
                    } //$data
                    else {
                        $data['success'] = 0;
                        $data['msg']    = "Something went wrong.";
                    }
                }else if($post['type'] == 'login'){
                    $result = $this->user_model->update_logout_time($post['userid']);
                    if ($result) {
                        $data['success'] = 1;
                        $data['msg']    = "Logout time updated successfully.";
                    } //$data
                    else {
                        $data['success'] = 0;
                        $data['msg']    = "Logout time updated successfully.";
                    }
                }
                $this->response($data, REST_Controller::HTTP_OK);
            }else{
                $data['success'] = 0;
                $data['msg'] = 'Parameters error!';
            }
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
    }
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function projects_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
            $data['success'] = 1;
            //$post = json_decode(file_get_contents("php://input"), true);
            $post = $this->input->post();
            if(!empty($post['userid'])){
                $data['details'] =  $this->user_model->get_user_projects($post['userid']);
                $data['default_module'] =  $this->user_model->get_default_module();
                //$data = $this->db->get_where("task", ['id' => $input['id']])->row_array();
            }else{
                $data['details'] = $this->user_model->get_user_projects(null);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
        
    }
    	
}