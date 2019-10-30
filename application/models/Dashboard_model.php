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
	    $array1 = array('name'=>$this->input->post('task_name'),'email'=>$this->input->post('user_email'),'phone'=>$this->input->post('contact'),'type'=>'user','created_on'=>'time()');
	    $this->db->set($array1);
	    $query = $this->db->insert('users',$array1);
	    $last_insert_id = $this->db->insert_id();
	    $array2 = array('email'=>$this->input->post('user_email'),'password'=>$this->input->post('task_pass'),'type'=>'user','ref_id'=>'$last_insert_id','created_on'=>'time()');
	    $this->db->set($array2);
	    $query2 = $this->db->insert('login',$array2);

    }
    public function users_exists(){
        $this->db->where('name', $this->input->post('task_name'));
        $query = $this->db->get('users');
        if($query->num_rows() == 1){
            return false;
        } else {
            return true;
        }
    }
}
?>