<?php
class Dashboard_model extends CI_Model {

    public function __construct()
    {
        $this->load->library('email');
        $this->load->database();
    }
    //Dashboard model
    public function get_users(){
		$get_users_q = $this->db->get('users');
        $row = $get_users_q->num_rows();
        return $row;
    }
    public function get_tasks(){
		$get_task_q = $this->db->query("SELECT * FROM task");
		$row_task = $get_task_q->num_rows();
		return $row_task;
	}
	public function get_projects(){
		$get_proj_q = $this->db->query("SELECT * FROM project");
		$row_proj = $get_proj_q->num_rows();
		return $row_proj;	   
	}
	//add user model
	public function add_users(){
	    $array1 = array('name'=>$this->input->post('task_name'),'email'=>$this->input->post('user_email'),'phone'=>$this->input->post('contact'),'type'=>'user','created_on'=>date('Y:m:d H:i:s'));
	    $this->db->set($array1);

	    $query = $this->db->insert('users',$array1);
	    
	    $last_insert_id = $this->db->insert_id();
	    //print_r('came to here-'. $last_insert_id); exit;
	    $array2 = array('email'=>$this->input->post('user_email'),'password'=>$this->input->post('task_pass'),'type'=>'user','ref_id'=> $last_insert_id,'created_on'=>date('Y:m:d H:i:s'));
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
    //add task model
    public function add_tasks(){
    	$query1 = $this->db->get_where('project', array('name' => $this->input->post('chooseProject')));
    	if($query1->num_rows() > 0){
    		$proj_id = $query1->row_array();
    		$query2 = $this->db->get_where('users',array('name' => $this->input->post('user-name')));
    		if($query2->num_rows() >0){
    			$user_id = $query2->row_array();
    			$array = array('task_name'=>$this->input->post('task_name'),'description'=>$this->input->post('description'),'project_id'=>$proj_id['id'],'module_id'=>'1','created_on'=>date('Y:m:d H:i:s'));
    			$this->db->set($array);
	    		$query3 = $this->db->insert('task',$array);
	    		if(!$query3){
	    			return false;
	    		}else{
                    $last_insert_id = $this->db->insert_id();
                    $array2 = array('user_id'=>$user_id['id'],'task_id'=>$last_insert_id,'created_on'=>date('Y:m:d H:i:s'));
                    $this->db->set($array2);
                    $query4 = $this->db->insert('task_assignee',$array2);
                    if(!$query4){
                        return false;
                    }else{
                        return true;
                    }
	    		}
    		}else{
    			echo "Not a valid user id.";
    		}
		}else{
			echo "project id not present.";
    	}
	}
	//add project model
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
    	$array = array('name'=>$this->input->post('task-name'), 'created_on'=>date('Y:m:d H:i:s'));
    	$query = $this->db->insert('project', $array);
    	if(!$query){
    		return false;
    	}else{
    		return true;
   		}
    }
    //update profile model
    public function submit_profile($picture){
    	$useremail = $this->session->userdata('email');
        print_r($picture);exit;
    	$this->db->where('email',$useremail);
        $query = $this->db->update('users', $picture);
        if(!$query){
    		return false;
    	}else{
    		$this->db->where('email',$useremail);
    		$query2 = $this->db->get('users');
    		if($query2->num_rows() > 0){
    			$user_profile = $query2->row();
    			$this->session->set_userdata('user_profile',$user_profile->profile);
    			//return $user_profile->type;
                return true;
   			}else{
   				return false;
   			}
        }
	}
	public function password_exists(){
		$email = $this->session->userdata('email');
        $query = $this->db->get_where('login', array('email' => $email,'password'=>md5($this->input->post('psw1'))));
       // print_r($query->result_array());exit;
        if($query->num_rows() == 1){
        	return true;
		}else{
			$this->form_validation->set_message('password_exists','Please enter your old password properly.');
        	return false;
		}
	}
    //function to change password
	public function change_password(){
		$new_pwd = $this->input->post('psw11');
		$confirm_pwd = $this->input->post('psw22');

		if($new_pwd == $confirm_pwd){
            if($this->session->userdata('email')){       
                $email = $this->session->userdata('email');
            }else{
                
                $email = $this->input->post('mail');
            }
			$this->db->set('password',md5($new_pwd));
			$this->db->where('email', $email);
			$query = $this->db->update('login');
			if($query){
				return true;
			}else{
				return false;
			}
		}else{
            $this->session->set_flashdata('err_msg', 'Passwords do not match..');
			return false;
		}
	}
    //To login
	public function login_process(){
		$username = $this->input->post('username');
        $password = $this->input->post('password');
		$query = $this->db->get_where('login', array('email' => $username,'password'=>md5($password)));
		if($query->num_rows() == 1){
			$row = $query->row();
            $query2 = $this->db->get_where('users', array('email' => $username));
            if($query2->num_rows() == 1){
                $row2 = $query2->row();
                //check for entry with the same login date
                $this->db->where(array('task_date'=>date('Y:m:d'), 'user_id' => $row2->id));
                $query_check = $this->db->get('login_details');

                if($query_check->num_rows()>0){ //multiple logins on the same date
                    $login_data = $query_check->row_array();                 
                    $data = array('userid' => $row2->id,'email' => $row->email,'logged_in' => TRUE,'user_profile' => $row2->profile,'username' => $row2->name,'login_time' => $login_data['end_time']);

                    $this->session->set_userdata($data);
                }else{ //first login for the day
                    $array = array('user_id'=>$row2->id,'task_date'=>date('Y:m:d'),'start_time'=>date("Y:m:d H:i:s"),'created_on'=>date('Y:m:d H:i:s'));
                    $this->db->set($array);
                    $query = $this->db->insert('login_details',$array);
                    $data = array('userid' => $row2->id,'email' => $row->email,'logged_in' => TRUE,'user_profile' => $row2->profile,'username' => $row2->name,'login_time' => date('Y:m:d H:i:s'));
                    $this->session->set_userdata($data);    
                }
                return $row->type;
            }else{
                return false;
            }
		}else{
			$this->form_validation->set_message('Wrong inputs.');
			return false;
		}
	}
    public function send_otp(){
        $email = $this->security->xss_clean($this->input->post('email'));
        if(empty($email)){
            $this->session->set_flashdata('err_msg', 'Please enter Email.');
        }else{
            $query = $this->db->get_where('login', array('email'=>$email));
            if($query->num_rows() == 1){
                $token = substr(mt_rand(), 0, 6);

                $this->db->set('reset_token',$token);
                $this->db->where('email', $email);
                $query = $this->db->update('login');
                if($query){
                   /* $to = $email;
                    $this->email->from('admin1@printgreener.com', 'Admin');
                    $this->email->to('swasthika@printgreener.com');
                    $this->email->subject('OTP for login');
                    $this->email->message('Use this OTP:'.$token);
                    $this->email->send();
                    if(!$this->email->send()){
                        echo "mail not sent.";exit;
                    }else{
                        echo "sent.";
                    }*/
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    public function check_otp(){
        $email = $this->security->xss_clean($this->input->post('email'));
        $otp = $this->security->xss_clean($this->input->post('otp'));
        $query = $this->db->get_where('login', array('reset_token'=>$otp,'email'=>$email));
        if($query->num_rows() == 1){
            $row = $query->row_array();
            $result = $row['email'];
            return $result;
        }else{
            $this->session->set_flashdata('error_msg', 'Enter correct OTP.');
            return false;
        }
    }
}
?>