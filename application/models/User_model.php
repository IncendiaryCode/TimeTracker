<?php
class User_model extends CI_Model {

    public function __construct()
    {
        $this->load->library('email');
        $this->load->database();
        $this->load->library('session');
        $userid = $this->session->userdata('userid');
    }
    //Task Status
    public function task_status(){
        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        //$this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->where(array('d.user_id'=>$userid,'d.total_minutes'=>'0'));
        //$this->db->where('d.end_time IS NULL');
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $task_status = $query->result_array();
            return array('task_status'=>$task_status,'type'=>'task');
        }else{
            return array('task_status'=>'','type'=>'login');
        }
    }
    //load tasks
    public function get_task_details($sort_type){
        $userid = $this->session->userdata('userid');
        $this->db->select('p.name,d.start_time,p.image_name,t.task_name,t.id');
        //$this->db->select_sum('d.total_hours','t_hours');
        $this->db->select("SUM(IF(d.total_minutes=0,1,0)) AS running_task",FALSE);
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a','a.task_id = t.id');
        $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m','m.id = t.module_id');        
        $this->db->where(array('a.user_id'=>$userid));
        $this->db->group_by('t.id');
        if($sort_type == 'name'){
            $this->db->order_by("t.task_name", "asc");
        }else if($sort_type =='date'){
            $this->db->order_by("d.task_date", "asc");
        }else if($sort_type == 'task'){
            $this->db->order_by("t.id", "asc");
        }
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }
    //get task details to edit task
    public function get_task_info($id){
        $userid = $this->session->userdata('userid');
        $taskid = $id;
        $this->db->select('*');
        $this->db->from('time_details');
        $this->db->join('task', 'task.id = time_details.task_id');
        $this->db->join('project', 'project.id = task.project_id');
        $this->db->where(array('task.id'=>$taskid,'user_id'=>$userid));
        $this->db->order_by("task.id", "asc");
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }
    //Start Timer...
    public function start_timer($type){
        $userid = $this->session->userdata('userid');
        $task_type = $type;
        if($task_type == 'login'){
            $array1 = array('user_id'=>$userid,'task_date'=>date('Y:m:d'),'start_time'=>date('Y:m:d H:i:s'),'created_on'=>date('Y:m:d H:i:s'));
            $this->db->set($array1);
            $query1 = $this->db->insert('login_details',$array1);
            if($query1){
                return true;
            }else{
                return false;
            }
        }else if($task_type == 'task'){
            $id = $this->input->post('id');
            $array2 = array('task_id'=>$id,'user_id'=>$userid,'task_date'=>date('Y:m:d'),'start_time'=>date('Y:m:d H:i:s'),'total_hours'=>'0','total_minutes'=>'0','created_on'=>date('Y:m:d H:i:s'));
            $this->db->set($array2);
            $query2 = $this->db->insert('time_details',$array2);
            if($query2){
                return true;
            }else{
                return false;
            }
        }else if($this->input->post('action') == 'save_and_start'){
            $array = array('task_id'=>$task_type,'user_id'=>$userid,'task_date'=>date('Y:m:d'),'start_time'=>date('Y:m:d H:i:s'),'total_hours'=>'0','total_minutes'=>'0','created_on'=>date('Y:m:d H:i:s'));
            $this->db->set($array);
            $query = $this->db->insert('time_details',$array);
            if($query){
                return true;
            }else{
                return false;
            }
        }
    }
    //Stop Timer
    public function stop_timer($task_id, $end_time){

        //print_r($task_id);exit;
        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('time_details');
        $this->db->where(array('task_id'=>$task_id,'user_id'=>$userid,'total_minutes'=>'0'));
        //$this->db->where('end_time IS NULL');
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $data = $query->row_array(); 
            if($end_time !=''){
                $update_time = $end_time; 
            }else{
                $update_time = date('Y:m:d H:i:s'); 
            }
            
            $diff = (strtotime($update_time) - strtotime($data['start_time']));            
            $t_minutes = round((abs($diff) /60),2);           

            $this->db->where(array('id'=>$data['id']));
            $query2 = $this->db->update('time_details',array('end_time'=> $update_time, 'total_minutes' => $t_minutes,'modified_on' => date('Y:m:d H:i:s') ));

            if($query2){
                return true;
            }else{
                return false;
            }
                /*$this->db->where(array('start_time'=>$data['start_time'],'task_id'=>$data['task_id'],'user_id'=>$userid));
                $result = $this->db->get('time_details')->row()->id;
                //print_r($result);exit;
                $query = $this->db->query("SELECT start_time,end_time,id,task_id FROM time_details WHERE id=".$result);
                if($query->num_rows() > 0){
                    $data2 = $query->row_array();
                    $start_time = strtotime($data2['start_time']);
                    $end_itme = strtotime($data2['end_time']);
                    $diff = $end_itme - $start_time;
                    //$diff=date_diff(date_create($data['end_time']),date_create($data['start_time']));
                    //print_r($diff);exit;
                    $hours = round(abs($diff / ( 60 * 60 )));
                    //$minutes = intval(floor($diff/60));
                    $minutes = round(abs($diff) / 60,2);
                    $this->db->where(array('user_id'=>$userid,'task_id'=>$task_id,'total_minutes'=>'0'));
                    $query = $this->db->update('time_details',array('total_hours'=>$hours,'total_minutes'=>$minutes));
                    if($query){*/
        }else{
            echo "There is no running task with this userid";
        }
    }
    //edit end time of the running task
    public function edit_end_time(){
        $userid = $this->session->userdata('userid');
        $array = array('end_time'=>$this->input->post('end'));
        $this->db->where(array('task_id'=>$this->input->get('id'),'user_id'=>$userid,'total_minutes'=>'0'));
        
        if($query){
            return true;
        }else{
            return false;
        }
    }
    //Activity Chart Data
    public function get_activity($chart_type,$date){
        //get task activities
        $userid = $this->session->userdata('userid');
        $taskdate = $date;
        if($chart_type == "daily_chart"){
            $this->db->select('*');
            $this->db->from('time_details');
            $this->db->where(array('user_id'=>$userid,'task_date' => $taskdate));
            $this->db->where('end_time IS NOT NULL');
            $this->db->limit(1);
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $data = $query->row_array();
                $starttime = $data['start_time'];
                $endtime = $data['end_time'];
                //$label_data = $this->split_time($starttime, $endtime, "60");
                //if(!empty($label_data)){
                    /*$chart_data = array('daily_chart',
                    "status"=>TRUE,
                    "labels"=> $label_data[0],
                    "data"=> $label_data[1]
                    );*/
                   // $chart_data = array($label_data);
                    //print_r($chart_data);
                    return array($starttime,$endtime);
               // }
            }else{
                $chart_data = array('daily_chart',
                    "status"=>TRUE,
                    "labels"=> array('9AM','10AM','11AM', '12PM','1PM','2PM', '3PM','4PM','12PM', '6PM', '9PM', '12PM'),
                    "data"=> array(0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0)
                ); 
            }
        }

        if($chart_type == "weekly_chart"){
            $date = $this->input->get('date');
            $year_value = explode('-',$date);
            $week_value = $year_value[1];
            $week = explode('W',$week_value);
            $getdate = $this->get_start_and_end_date($week[1], $year_value[0]);
            $this->db->select('*');
            $this->db->from('time_details');
            $this->db->where(array('user_id'=>$userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "'. date('Y-m-d', strtotime($getdate[0])). '" and "'. date('Y-m-d', strtotime($getdate[1])).'"');
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $data = $query->result_array();
                foreach($data as $d){
                    $day = date('D', strtotime($d['task_date']));
                    //print_r($day);
                   /* $start = strtotime($d['start_time']);
                    $end = strtotime($d['end_time']);
                    $total_time = $end-$start;
                    $total['total_hours'] = floor($total_time/3600);*/
                    $minutes = $d['total_minutes'];
                    $tohours = $minutes/60;
                    $total_hours = $d['total_hours'];
                    $mins_to_hour = $total_hours + $tohours;
                    $total['total_hours'] = $mins_to_hour;
                    $total['day'] = $day;
                    if(!empty($total)){
                        $chart_data = array('weekly_chart',
                            "status"=>TRUE,
                            "labels"=> $total['day'],
                            "data"=> $total['total_hours']
                        ); 
                    }else{
                        $chart_data = array('weekly_chart',
                                "status"=>TRUE,
                                "labels"=> array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'),
                                "data"=> array(7, 4, 8, 7, 9, 5)
                            );
                    }
                }
            }
        }

        if($chart_type == "monthly_chart"){
            $chart_data = array('monthy_chart',
                    "status"=>TRUE,
                    /*date with working hours in standard format*/
                    "data"=> array("Wed Feb 13 2019 00:00:00 GMT+0530 (India Standard Time) 10",
                    "Wed Feb 13 2019 00:00:00 GMT+0530 (India Standard Time) 8",
                    "Wed Feb 13 2019 00:00:00 GMT+0530 (India Standard Time) 7", 
                    "{Wed Feb 13 2019 00:00:00 GMT+0530 (India Standard Time) 5}")
                );
        }

        return $chart_data;
    }
    //to make time intervals in daily chart
    public function split_time($start__time, $end_time, $duration="60"){
        $result_array = array ();// Define output
        $start_time    = strtotime ($start__time); //Get Timestamp
        $end_time      = strtotime ($end_time); //Get Timestamp
        $add_mins  = $duration * 60;
        $dat = date('Y-m-d', $start_time);
        $starttime = date('Y-m-d H:i:s',strtotime('+1 hour',$start_time));
        $start = strtotime($starttime);
        $i = strtotime($dat.'09:00:00');
        while($i <= $start_time){
            $result_array[] = date ("G:00", $i);
            $returndata[] = '0';
            $i += $add_mins;
        }
        while ($start <= $end_time) //Run loop
        {

            $result_array[] = date ("G:00", $start);
            $returndata[] = '1';
            $start += $add_mins; //Endtime check
        }
        $endtime = date('Y-m-d H:i:s',strtotime('+1 hour',$end_time));
        $end__time = strtotime($endtime);
        $end = strtotime($dat.'23:59:59');
        while($end__time <= $end){
            $result_array[] = date ("G:00", $end__time);
            $returndata[] = '0';
            $end__time += $add_mins;
        }
        return array($result_array,$returndata);
    }
    //get start date and end date from the week input
    public function get_start_and_end_date($week, $year) {
        return [
            (new DateTime())->setISODate($year, $week)->format('Y-m-d'), //start date
            (new DateTime())->setISODate($year, $week, 7)->format('Y-m-d') //end date
        ];
        /*$date_string = $year . 'W' . sprintf('%02d', $week);
        $return[0] = date('Y-n-j', strtotime($date_string));
        $return[1] = date('Y-n-j', strtotime($date_string . '7'));
        return $return;*/
    }
	//update profile model
    public function submit_profile($picture){
    	$useremail = $this->session->userdata('email');
    	$this->db->where('email',$useremail);
        $query = $this->db->update('users', $picture);
        if(!$query){
    		return false;
    	}else{
    		$this->db->where('email',$useremail);
    		$query2 = $this->db->get('users');
    		if($query2->num_rows() > 0){
    			$row = $query2->row();
    			$this->session->set_userdata('user_profile',$row->profile);
    			return true;
   			}else{
   				return false;
   			}
        }
	}
    //get project name for the add task page
	public function get_project_name(){
        $userid = $this->session->userdata('userid');
    	$query = $this->db->query("SELECT p.* FROM project AS p JOIN project_assignee AS ps ON p.id=ps.project_id WHERE ps.user_id =".$userid);
    	$result = $query->result_array();
    	return $result;
    }
    //get project module into add task page
    public function get_module_name($project_id){
        $p_id = $project_id;
        $query = $this->db->query("SELECT * FROM project_module WHERE project_id = {$p_id} OR project_id = 0"); 
        //  
        $result = $query->result();
        return $result;
    }
    public function task_exists(){
        $task_name = $this->input->post('task_name');
        //print_r($this->input->post('project_name'));exit;
        $this->db->select('task_name');
        $this->db->from('task');
        $this->db->where(array('project_id'=>$this->input->post('project_name')));
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $row = $query->row_array();
            if($row['task_name'] == $task_name){
                $this->form_validation->set_message('task_exists','Task name exists.');
                return false;
            }else{
                return true;
            }
        }
    }
	//add task model
    public function add_tasks(){

        $userid = $this->session->userdata('userid');
        if(!empty($this->input->post('project_module'))){
            $module_id = $this->input->post('project_module');
        }else{
            $module_id = 1;
        }

        if($this->input->get('type') == 'edit'){
            //print_r($this->input->get('type'));exit;
            $array = array('task_name'=>$this->input->post('task_name'),'description'=>$this->input->post('task_desc'),'modified_on'=>date('Y:m:d H:i:s'));
            $this->db->where(array('project_id'=>$this->input->post('project_name'),'id'=>$this->input->post('task_id')));
            $query = $this->db->update('task',$array);
            if($this->db->affected_rows() > 0){
                /*$start_time = strtotime($this->input->post('start_date'));
                $end_time = strtotime($this->input->post('end_date'));
                $diff = $end_time - $start_time;
                $hours = $diff / ( 60 * 60 );
                $minutes = $diff/60; 
                $total_mins = ($minutes < 0 ? 0 : abs($minutes));
                $array = array('end_time'=>$this->input->post('end_date'),'total_hours'=>$hours,'total_minutes'=>$total_mins);
                $this->db->where(array('user_id'=>$userid,'start_time'=>$this->input->post('start_date')));
                $query = $this->db->update('time_details',$array);
                if($this->db->affected_rows() > 0){
                    return true;
                }else{
                    return false;
                }*/
                return true;
            }else{
                return false;
            }
        }
        else{
            if($this->input->post('action') == 'save_and_start'){
                $array = array('task_name'=>$this->input->post('task_name'),'description'=>$this->input->post('description'),'project_id'=>$this->input->post('project'),'module_id'=>$this->input->post('project_module'),'created_on'=>date('Y:m:d H:i:s'));
                $this->db->set($array);
                $query = $this->db->insert('task',$array);
                if(!$query){
                    return false;
                }else{
                    $last_insert_id = $this->db->insert_id();
                    $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'created_on'=>date('Y:m:d H:i:s'));
                    $this->db->set($array);
                    $query = $this->db->insert('task_assignee',$array);
                    if(!$query){
                        return false;
                    }else{
                        return $last_insert_id;
                    }
                }
            }else{
            	$array = array('task_name'=>$this->input->post('task_name'),'description'=>$this->input->post('task_desc'),'project_id'=>$this->input->post('project_name'),'module_id'=>$module_id,'created_on'=>date('Y:m:d H:i:s'));
            	$this->db->set($array);
        	    $query = $this->db->insert('task',$array);
        	    if(!$query){
        	    	return false;
        	    }else{
                    $last_insert_id = $this->db->insert_id();
                    $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'created_on'=>date('Y:m:d H:i:s'));
                    $this->db->set($array);
                    $query = $this->db->insert('task_assignee',$array);
                    if(!$query){
                        return false;
                    }else{
                        //Add timings into time_details table
                        $members = ($this->input->post('daterange'));
                        if(sizeof($members) > 1){
                            foreach($members as $values)
                            {
                                $start_time = strtotime($values['start']);
                                $end_itme = strtotime($values['end']);
                                $diff = $end_itme - $start_time;
                                $hours = $diff / ( 60 * 60 );
                                $minutes = $diff/60; 
                                $total_mins = ($minutes < 0 ? 0 : abs($minutes));
                                $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'task_date'=>$values['date'],'start_time'=>$values['start'],'end_time'=>$values['end'],'total_hours'=>$hours,'total_minutes'=>$total_mins,'created_on'=>date('Y:m:d H:i:s'));
                                $this->db->set($array);
                                $query = $this->db->insert('time_details',$array);
                                /*if($query){  
                                    return $last_insert_id;
                                }else{
                                    return false;
                                }*/
                            }
                        }
                        return $last_insert_id;
                    }
        	    }
            }
        }
	}
    //Check for the Old Password
    public function password_exists(){
        $email = $this->session->userdata('email');
        $query = $this->db->get_where('login', array('email' => $email,'password'=>md5($this->input->post('psw1'))));
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
    public function my_profile(){
        $userid = $this->session->userdata('userid');
        $this->db->where('id',$userid);
        $query = $this->db->get('users');
        if($query->num_rows() == 1){
            return $query->row_array();
        }
    }
}
?>