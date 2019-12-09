<?php
class User_model extends CI_Model {

    public function __construct()
    {
        $this->load->library('email');
        $this->load->database();
        $this->load->library('session');
        $userid = $this->session->userdata('userid');
    }
    //fetch running tasks into user dashboard page 
    public function task_status(){
        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where(array('d.user_id'=>$userid,'d.total_minutes'=>'0'));
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $task_status = $query->result_array();
        }else{
            $task_status = '';
        }
            $this->db->where(array('user_id'=>$userid,'task_date'=>date('Y:m:d')));
            $query = $this->db->get('login_details');
            if($query->num_rows() > 0){
                $login_status = $query->row_array();
                return array('task_status'=>$task_status,'login_status'=>$login_status);
        }
    }
    //load all tasks of the user into user dashboard
    public function get_task_details($sort_type,$date){  
        $userid =  $this->session->userdata('userid');
        $this->db->select('p.name,d.start_time,p.image_name,t.task_name,t.id');
        $this->db->select("SUM(IF(d.total_minutes=0,1,0)) AS running_task",FALSE); //get running tasks of the user 
        $this->db->select('IF(t.complete_task=1,1,0) AS completed',FALSE);         //get completed tasks of the user                     
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a','a.task_id = t.id');
        $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m','m.id = t.module_id'); 
        if($date == null){ 
            $this->db->select_sum('d.total_minutes','t_minutes');       //get total minutes for a particular task
            $this->db->where(array('a.user_id'=>$userid));
        }else{
            if($sort_type == 'daily_chart'){

                $this->db->where(array('d.task_date'=>$date));
                $this->db->select_sum('d.total_minutes','t_minutes');   //get total minutes for a particular task
                $this->db->where('d.end_time IS NOT NULL');
            }
            else if($sort_type == 'weekly_chart'){
                $year_value = explode('-',$date);  //format: 2019-W23
                $week_value = $year_value[1];      //W23
                $week = explode('W',$week_value);  //format: W23
                $getdate = $this->get_start_and_end_date($week[1], $year_value[0]);  //start and end date for 23rd week and year 2019
                $this->db->where('d.task_date BETWEEN "'. date('Y-m-d', strtotime($getdate[0])). '" and "'. date('Y-m-d', strtotime($getdate[1])).'"');
                $this->db->select_sum('d.total_minutes','t_minutes');  //get total minutes for a particular task
                $this->db->where('d.end_time IS NOT NULL');
            }
            else{
                //for monthly chart
                $year_start = date('Y-m-d',strtotime(date($date.'-01-01')));
                $year_end = date('Y-m-d', strtotime(date($date.'-12-31')));
                $this->db->where('d.task_date BETWEEN "'.$year_start. '" and "'.$year_end.'"');
                $this->db->select_sum('d.total_minutes','t_minutes');   //get total minutes for a particular task
                $this->db->where('d.end_time IS NOT NULL');
            }
        }
        $this->db->group_by('t.id');
        if($sort_type == 'name'){
            $this->db->order_by("t.task_name", "asc");  //sort by task name
        }else if($sort_type =='date'){
            $this->db->order_by("d.task_date", "asc");  //sort by task date
        }else if($sort_type == 'task'){
            $this->db->order_by("t.id", "asc");         //sort by task id
        }
        $query = $this->db->get();
        $data = $query->result_array();
        //print_r($this->db->last_query());die;    
        //print_r($data);exit;
        return $data; 
    }
    public function get_user_task_info($sort_type,$post_data){
        $this->db->select('p.name as project_name,p.id as project_id,p.image_name,t.task_name,t.description,t.id');
        $this->db->select('IF(t.complete_task=1,1,0) AS completed',FALSE);         //get completed tasks of the user
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a','a.task_id = t.id');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m','m.id = t.module_id');
        $this->db->where(array('a.user_id'=>$post_data['userid']));
        /*if($date == null){       
            $this->db->where(array('a.user_id'=>$userid));
        }else{
            if($sort_type == 'daily_chart'){
                $this->db->where(array('d.task_date'=>$date));
            }
            else if($sort_type == 'weekly_chart'){
                $year_value = explode('-',$date);  //format: 2019-W23
                $week_value = $year_value[1];      //W23
                $week = explode('W',$week_value);  //format: W23
                $getdate = $this->get_start_and_end_date($week[1], $year_value[0]);  //start and end date for 23rd week and year 2019
                $this->db->where('d.task_date BETWEEN "'. date('Y-m-d', strtotime($getdate[0])). '" and "'. date('Y-m-d', strtotime($getdate[1])).'"');
            }
            else{
                //condition for monthly chart
            }
        }*/
        $this->db->group_by('t.id');
        if($sort_type == 'name'){
            $this->db->order_by("t.task_name", "asc");  //sort by task name
        }else if($sort_type =='date'){
            $this->db->order_by("d.task_date", "asc");  //sort by task date
        }else if($sort_type == 'task'){
            $this->db->order_by("t.id", "asc");         //sort by task id
        }
        if(isset($post_data['page_no'])){
            $limit  =10;
            $offset =$limit*($post_data['page_no']-1);
            $this->db->limit($limit,$offset);  
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $result = $data;
        foreach ($data as $key => $value) {
           $this->db->select('td.id,td.start_time,td.end_time,td.task_description,td.total_hours,td.total_minutes');
           $this->db->from('time_details AS td');
           $this->db->where(array('td.task_id'=>$value['id'],'td.user_id'=>$post_data['userid']));
           $query = $this->db->get();
           $sub_data = $query->result_array();
           $result[$key]['time_details'] = $sub_data;
           //print_r($this->db->last_query());die;    
        }
        //print_r($this->db->last_query());die;    
        //print_r($data);exit;
        return $result; 
    }
    //get task details to edit task
    public function get_task_info($id){
        $userid = $this->session->userdata('userid');
        $taskid = $id;
        $this->db->select('*,d.id');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->where(array('t.id'=>$taskid,'d.user_id'=>$userid));
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }
    //Function to Start Timer...
    public function start_timer($type){
        $userid = $this->session->userdata('userid');
        $task_type = $type;
        if($task_type == 'login'){ //check if the timer-start request for login
            $array1 = array('user_id'=>$userid,'task_date'=>date('Y:m:d'),'start_time'=>date('Y:m:d H:i:s'),'created_on'=>date('Y:m:d H:i:s'));
            $this->db->set($array1);
            $query1 = $this->db->insert('login_details',$array1);
            if($query1){
                return true;
            }else{
                return false;
            }
        }else if($task025+_type == 'task'){ //check if the timer-start request for task
            $id = $this->input->post('id');
            $array2 = array('task_id'=>$id,'user_id'=>$userid,'task_date'=>date('Y:m:d'),'start_time'=>date('Y:m:d H:i:s'),'total_hours'=>'0','total_minutes'=>'0','created_on'=>date('Y:m:d H:i:s'));
            $this->db->set($array2);
            $query2 = $this->db->insert('time_details',$array2);
            if($query2){
                return true;
            }else{
                return false;
            }
        }else if($this->input->post('action') == 'save_and_start'){  //if the start-timer request for save and start the task
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
    //Function to Stop Timer
    public function stop_timer($task_id, $end_time){

        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('time_details');
        $this->db->where(array('task_id'=>$task_id,'user_id'=>$userid,'total_minutes'=>'0'));
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $data = $query->row_array(); 
            if($end_time != ''){
                $update_time = date('Y-m-d H:i:s',strtotime($end_time)); 
            }else{
                $update_time = date('Y-m-d H:i:s'); 
            }
            
            $diff = (strtotime($update_time) - strtotime($data['start_time']));            
            $t_minutes = round((abs($diff) /60),2);           
            $hours = round(abs($diff / ( 60 * 60 )));

            $this->db->where(array('id'=>$data['id']));
            $query2 = $this->db->update('time_details',array('task_description'=>$this->input->post('task-description'),'end_time'=>$update_time,'total_hours'=>$hours, 'total_minutes' => $t_minutes,'modified_on' => date('Y:m:d H:i:s') ));
            if($query2){
                if($this->input->post('flag') == 1) //if flag is 1, request is to complete the task
                { 
                    $this->db->where('id',$task_id);
                    $query = $this->db->update('task',array('complete_task'=>'1'));
                    if($query){  
                        $flag_status = "complete";
                        return $flag_status;
                    }else{
                        return false;
                    }
                }else if($this->input->post('flag') == 0){ //if flag is 0, request is to stop the task
                    return true;
                }
            }else{
                return false;
            }
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
            //$this->db->select('*,count(t.task_name) as tasks');
            $this->db->from('time_details AS d');
            $this->db->join('task AS t','t.id = d.task_id');
            $this->db->where('d.end_time IS NOT NULL');       //tasks that are not running
            $this->db->where(array('d.user_id' => $userid,'d.task_date' => $taskdate));
            $this->db->group_by('d.start_time');
            $query = $this->db->get();
            $data = $query->result_array();
            if($query->num_rows() > 0){
                $data = $query->result_array();
                foreach($data as $d){
                        $task[] = $d['task_name'];
                        $start[] = $d['start_time'];
                        $end[] = $d['end_time'];
                        $total_minutes[] = $d['total_minutes'];
                }
                $tasks = array_count_values($task); 
                foreach($tasks as $key=>$count){
                    if($count > 1){
                        $task_names[] = $key;
                    }
                }

               
                    $chart_data = array('daily_chart',
                            "status"=>TRUE,
                            //"labels"=> $week_days,
                            "data"=> array($task,array($start,$end,$total_minutes))
                        );
                return $chart_data;
            }else{
                $chart_data = array('daily_chart',
                    'status'=>FALSE,
                    'data'=>"No activity in this date.");
                return $chart_data;
            }
        }

        if($chart_type == "weekly_chart"){
            $date = $this->input->get('date');
            $year_value = explode('-',$date);  //format: 2019-W23
            $week_value = $year_value[1];      //W23
            $week = explode('W',$week_value);  //format: W23
            $getdate = $this->get_start_and_end_date($week[1], $year_value[0]);  //start and end date for 23rd week and year 2019
            $this->db->select('*');
            $this->db->select_sum('total_hours','hours');
            $this->db->from('time_details');
            $this->db->where(array('user_id'=>$userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "'. date('Y-m-d', strtotime($getdate[0])). '" and "'. date('Y-m-d', strtotime($getdate[1])).'"');
            $this->db->group_by('task_date');
            $query = $this->db->get();
            
            if($query->num_rows() > 0){
                $data = $query->result_array();
                foreach($data as $d){
                    $day = date('D', strtotime($d['task_date']));
                    $minutes = $d['total_minutes'];
                    $to_hours[] = $minutes/60;   //get total_minutes interms of hour;
                    $week_days[] = $day;            
                }
                $week = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
                
                $chart_data = array('weekly_chart',
                            "status"=>TRUE,
                            "labels"=> $week_days,
                            "data"=> $to_hours
                        );
                //print_r($chart_data);
                return $chart_data;
            }else{
                $status = "No activity in this week.";
                return $status;
            }
        }

        if($chart_type == "monthly_chart"){
            
            $year_start = date('Y-m-d',strtotime(date($taskdate.'-01-01')));
            $year_end = date('Y-m-d', strtotime(date($taskdate.'-12-31')));
            $this->db->select('*');
            $this->db->select_sum('total_hours','hours');
            $this->db->from('time_details');
            $this->db->where(array('user_id'=>$userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "'.$year_start. '" and "'.$year_end.'"');
            $this->db->group_by('task_date');
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $data = $query->result_array();
                foreach($data as $d){
                    $values[] = array(date('Y-m-d',strtotime($d['task_date'])),$d['hours']);

                }
                $chart_data = array('monthy_chart',
                    "status"=>TRUE,
                    /*date with working hours in standard format*/
                    "data"=> $values
                );
                return $chart_data;
            }else{
                $chart_data = array('monthy_chart',
                    "status"=>FALSE,
                    "data"=> '0'
                );
                return $chart_data;
            }
        }
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
        $this->db->select('*');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS ps','ps.project_id = p.id');
        $this->db->where(array('ps.user_id'=>$userid));
        //$query = $this->db->query("SELECT p.* FROM project AS p JOIN project_assignee AS ps ON p.id=ps.project_id WHERE ps.user_id =".$userid);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $result = $query->result_array();
        }
        else{
            $result = '';
        }
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
        $userid = $this->session->userdata('userid');
        $task_name = $this->input->post('task_name');
        $this->db->select('t.task_name');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a','a.task_id = t.id');
        $this->db->join('project_module AS m','m.project_id = t.project_id');
        $this->db->where(array('t.project_id'=>$this->input->post('project_name'),'a.user_id'=>$userid));
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $this->form_validation->set_message('task_exists','Task name exists.');
            return true;
        }else{
                return false;
        }
        return true;
    }
    //add task model
    public function add_tasks($data){
        //date("H:i", strtotime("1:30 PM"));
        $userid = $data['userid'];
        if(($data['project_module'] == 'Select module') || ($data['project_module'] == '')){
            $module_id = 1;
        }else{
            $module_id = $data['project_module'];
        }
        if($data['action'] == 'edit'){

            $array = array('task_name'=>$data['task_name'],'description'=>$data['task_desc'],'modified_on'=>date('Y:m:d H:i:s'));
            $this->db->where(array('project_id'=>$data['project_id'],'id'=>$data['task_id']));
            $query = $this->db->update('task',$array);

            $time_range = $data['time_range'];
            if(!is_array($time_range)){
                $time_range = json_decode($time_range, true);
            } 
            if(sizeof($time_range) > 0){

                for($i=0;$i<sizeof($time_range);$i++){
                    $table_id[$i] = null;
                    if(isset($time_range[$i]['table_id'])){
                        $table_id[$i] = $time_range[$i]['table_id'];
                    }
                    //$table_id[$i] = $time_range[$i]['table_id'];
                    $start_value = $time_range[$i]['start'];
                    $end_value = $time_range[$i]['end'];
                    $description = $time_range[$i]['task_description'];
                    $date =  date("Y-m-d",strtotime($start_value));
                    $diff = strtotime($end_value) - strtotime($start_value);
                    $minutes = round((abs($diff) /60),2);           
                    $hours = round(abs($diff / ( 60 * 60 )));

                    $array = array('start_time'=>$start_value,'end_time'=>$end_value,'task_description'=>$description,'user_id'=>$userid,'task_id'=>$data['task_id'],'total_hours'=>$hours,'total_minutes'=>$minutes,'task_date'=>$date);
                    if($table_id[$i] != null){
                        $this->db->where(array('user_id'=>$userid,'task_id'=>$data['task_id'],'id'=>$table_id[$i]));
                        $query = $this->db->update('time_details',$array);
                    }else{
                        $array = array('user_id'=>$userid,'start_time'=>$start_value,'end_time'=>$end_value,'task_description'=>$description,'user_id'=>$userid,'task_id'=>$data['task_id'],'total_hours'=>$hours,'total_minutes'=>$minutes,'task_date'=>$date);
                        $this->db->set($array);
                        $query = $this->db->insert('time_details',$array);
                    }
                    
                    
                }
            }
            return true;
        }
        else{
            if($data['action'] == 'save_and_start'){
                $array = array('task_name'=>$data['task_name'],'description'=>$data['task_desc'],'project_id'=>$data['project_id'],'module_id'=>$module_id,'created_on'=>date('Y:m:d H:i:s'));
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
                
                $array = array('task_name'=>$data['task_name'],'description'=>$data['task_desc'],'project_id'=> $data['project_id'],'module_id'=>$module_id,'created_on'=>date('Y:m:d H:i:s'));
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
                        $date_value = $data['time_range'];
                        if(!is_array($date_value)){
                                $date_value = json_decode($date_value, true);
                        }
                        if(sizeof($date_value) >= 1){
                            for($i=0;$i<sizeof($date_value);$i++)
                            {
                                $start_time = strtotime($date_value[$i]['start']);
                                $end_itme = strtotime($date_value[$i]['end']);
                                $task_description = "";
                                if(isset($date_value[$i]['task_description'])){
                                    $task_description = $date_value[$i]['task_description'];
                                }
                                
                                $diff = $end_itme - $start_time;
                                $hours = $diff / ( 60 * 60 );
                                $minutes = $diff/60; 
                                $total_mins = ($minutes < 0 ? 0 : abs($minutes));
                                $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'task_date'=>$date_value[$i]['date'],'start_time'=>$date_value[$i]['date'].' '.date('H:i:s',$start_time),'end_time'=>$date_value[$i]['date'].' '.date('H:i:s',$end_itme),'task_description'=>$task_description,'total_hours'=>$hours,'total_minutes'=>$total_mins,'created_on'=>date('Y:m:d H:i:s'));
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
        $query = $this->db->get_where('users', array('email' => $email,'password'=>md5($this->input->post('psw1'))));
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
            $query = $this->db->update('users');
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
    //fetch user profile data to profile page
    public function my_profile(){
        $userid = $this->session->userdata('userid');
        $this->db->where('id',$userid);
        $query = $this->db->get('users');
        if($query->num_rows() == 1){
            return $query->row_array();
        }
    }
    public function update_logout_time(){
        $userid = $this->session->userdata('userid');
        //check for entry with the same login date
        $this->db->where(array('task_date'=>date('Y:m:d'),'end_time IS NULL'));
        $query_check = $this->db->get('login_details');
        if($query_check->num_rows()>0){
            $data = $query_check->row_array();
            $array = array('end_time'=>date('Y:m:d H:i:s'),'modified_on'=>date('Y:m:d H:i:s'));
            $this->db->where('id',$data['id']);
            $query = $this->db->update('login_details',$array);
            if($this->db->affected_rows() == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function get_user_projects($user_id)
    {
        $this->db->select('p.name as project_name,p.id,p.color_code,p.image_name');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS pa','p.id = pa.project_id');
        if($user_id!=null)
            $this->db->where(array('pa.user_id'=>$user_id));
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }
}
?>
