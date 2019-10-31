<?php
class Dashboard_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
    public function get_users(){
		$get_users_q = $this->db->get('users');
        $row = $get_users_q->num_rows();
        return $row;
    }
    public function get_tasks(){
		$get_task_q = $this->db->query("SELECT * FROM time_details WHERE type = 'task'");
		$row_task = $get_task_q->num_rows();
		return $row_task;
	}
	public function get_projects(){
		$get_proj_q = $this->db->query("SELECT * FROM project");
		$row_proj = $get_proj_q->num_rows();
		return $row_proj;	   
	}
	public function add_users(){
	    $array1 = array('name'=>$this->input->post('task_name'),'email'=>$this->input->post('user_email'),'phone'=>$this->input->post('contact'),'type'=>'user','created_on'=>time());
	    $this->db->set($array1);

	    $query = $this->db->insert('users',$array1);
	    
	    $last_insert_id = $this->db->insert_id();
	    //print_r('came to here-'. $last_insert_id); exit;
	    $array2 = array('email'=>$this->input->post('user_email'),'password'=>$this->input->post('task_pass'),'type'=>'user','ref_id'=> $last_insert_id,'created_on'=>time());
	    $this->db->set($array2);
	    $query2 = $this->db->insert('login',$array2);
	    return true;
    }
    public function users_exists(){
        $this->db->where('email', $this->input->post('user_email'));
        $query = $this->db->get('users');
        if($query->num_rows() > 0){
       
        	$this->form_validation->set_message('users_exists','User Already Exists.');
            return false;
        } else {

            return true;
        }
    }
    public function get_project_name(){
    	$query = $this->db->query("SELECT * FROM project");
    	$result = $query->result_array();
    	return $result;
    }
    public function username_exists(){
        $this->db->where('name', $this->input->post('user-name'));
        $query = $this->db->get('users');
        if($query->num_rows() > 0){
       
        	return true;
        } else {

            return false;
        }
    }
    public function add_tasks(){
    	$query1 = $this->db->get_where('project', array('name' => $this->input->post('chooseProject')));
    	if($query1->num_rows() > 0){
    		$proj_id = $query1->row_array();
    		$query2 = $this->db->get_where('users',array('name' => $this->input->post('user-name')));
    		if($query2->num_rows() >0){
    			$user_id = $query2->row_array();
    			$array = array('task_name'=>$this->input->post('task_name'),'description'=>$this->input->post('description'),'project_id'=>$proj_id['id'],'type'=>'task','created_on'=>time(),'ref_id' => $user_id['id']);
    			$this->db->set($array);
	    		$query3 = $this->db->insert('time_details',$array);
	    		if(!$query3){
	    			return false;
	    		}else{
	    			return true;
	    		}
    		}else{
    			echo "Not a valid user id.";
    		}
		}else{
			echo "project id not present.";
    	}
	}
	public function project_exists(){
        $this->db->where('name', $this->input->post('task-name'));
        
        $query = $this->db->get('project');
        if($query->num_rows() > 0){
       		$this->form_validation->set_message('project_exists','Project Name Already Exists.');
        	return false;
        } else {

            return true;
        }
    }
    public function add_projects(){
    	$array = array('name'=>$this->input->post('task-name'), 'created_on'=>time());
    	$query = $this->db->insert('project', $array);
    	if(!$query){
    		return false;
    	}else{
    		return true;
   		}
    }
}
?>