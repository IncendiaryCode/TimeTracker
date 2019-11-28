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
	public function index_post($id = 0)
	{
        // Get all the headers
        $headers = $this->input->request_headers();
        $verify_data = $this->verify->verify_request($headers);
        if(isset($verify_data->username))
        {
        //$tokenData = 'Hello World!';
        // Create a token
        //$token = AUTHORIZATION::generateToken($tokenData);
        //print_r($token); die;
            $input = $this->input->post();
            if(!empty($input['id'])){
                $type = 'task';
                $data['details'] =  $this->user_model->get_task_details($type,$input['id']);
                //$data = $this->db->get_where("task", ['id' => $input['id']])->row_array();
            }else{
                $data['details'] = $this->db->get("task")->result();
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
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('items', $input, array('id'=>$id));
     
        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);
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