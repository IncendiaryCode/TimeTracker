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
                $data['details'] =  $this->user_model->get_user_task_info($type,$input['userid']);
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
     * Get All Data from this method.
     *
     * @return Response
    */
    /*public function index_post()
    {
        $input = $this->input->post();
        $this->db->insert('items',$input);
     
        $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);
    }*/
     
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
                //$data = $this->db->get_where("task", ['id' => $input['id']])->row_array();
            }else{
                $data['details'] = $this->user_model->get_user_projects(null);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response($verify_data, REST_Controller::HTTP_OK);
        }
        
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('items', array('id'=>$id));
       
        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}