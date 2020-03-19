<?php
class User_model extends CI_Model {
    /**
     * To load the database.
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function __construct() {
        //$this->load->library('email');
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');
    }

    /**
     * Function to start login timer
     * 
     * @param void
     * 
     * @return TRUE/FALSE
     * 
     */
    public function start_login_timer(){
        //check whether login data already present in logindetails table
        $this->db->where(array('task_date' => date('Y-m-d'),'user_id' => $this->userid));
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) { //if present, update the login timings
            $result = $query_check->row_array();
            return TRUE;
        }else{ //if login data is not present, insert a new row into login details table
            $login_data = array('user_id'=>$this->userid,
                        'task_date'=>date('Y-m-d'),
                        'start_time'=>date('Y-m-d H:i:s',strtotime($this->input->post('start-login-time'))),
                        'created_on'=>date('Y-m-d H:i:s')
                    );
            $this->db->set($login_data);
            $query = $this->db->insert('login_details', $login_data);
            if($query){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    /**
     * Function to fetch running tasks into user dashboard page
     * 
     * @param void
     * 
     * @returns array($task_status,$login_status)
     * 
     */
    public function task_status() {
        $details =array();
        $userid = $this->userid;

        //send task details to user dashboard
        $this->db->select('d.start_time');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where(array('d.user_id' => $userid, 'd.total_minutes' => '0'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $details['task_status'] = $query->result_array();
            foreach($details['task_status'] AS &$d){
                $c_sdate = $this->convert_date($d['start_time']);
                $d['start_time'] =  ($c_sdate) ? $c_sdate : $d['start_time'];                
            }
        }

        //send present login details to user dashboard
        $this->db->where(array('user_id' => $userid, 'task_date' => date('Y-m-d')));
        //$this->db->where('end_time IS NULL');
        $query = $this->db->get('login_details');
        if ($query->num_rows() > 0) {
            $details['login_status'] =$query->row_array();
            $start = $this->convert_date($details['login_status']['start_time']);
            $details['login_status']['start_time'] = ($start) ? $start : $details['login_status']['start_time'];
        }

        //send previous day running task data to user dashboard
        $this->db->select('d.task_id,d.start_time,t.task_name,t.description,d.task_date');
        //$this->db->select('count(IF(d.total_minutes=0,1,0)) AS running_task_count');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where('d.total_minutes','0');
        $this->db->where('d.task_date !=',date('Y-m-d'));
        $this->db->where('user_id',$this->session->userdata('userid'));
        $tasks_data = $this->db->get();
        if($tasks_data->num_rows() > 0){
            $details['task_run'] = $tasks_data->result_array();
            foreach($details['task_run'] AS &$rd){
                $c_r_sdate = $this->convert_date($rd['start_time']);
                $rd['start_time'] =  ($c_r_sdate) ? $c_r_sdate : $rd['start_time'];
            }
        }else{
            $details['task_run'] = '';
        }

        //send previous day login details to user dashboard
        $this->db->select('id,user_id,task_date,start_time');
        $this->db->from('login_details');
        $this->db->where('task_date !=',date('Y-m-d'));
        $this->db->where('end_time IS NULL');
        $this->db->where('user_id',$this->session->userdata('userid'));
        $login_check = $this->db->get();
        if($login_check->num_rows() > 0){
            $details['login_run'] = $login_check->row_array();
            $r_start = $this->convert_date($details['login_run']['start_time']);
            $details['login_run']['start_time'] = ($r_start) ? $r_start : $details['login_run']['start_time'];
            if($details['task_run'] == ''){
                $this->db->select_max('end_time');
                $this->db->from('time_details');
                $this->db->where('task_date',date('Y-m-d', strtotime("-1 days")));
                $this->db->where('user_id',$userid);
                $get_task_end_time = $this->db->get();
                if($get_task_end_time->num_rows() > 0){
                    $details['task_end_time'] = $this->convert_date($get_task_end_time->row()->end_time);
                }
            }
        }else{
            $details['login_run'] = '';
        }
        return $details;
    }


    /*Function to load all tasks of the user into user dashboard
     * 
     * @params $sort_type and $date
     * 
     * returns $data
     */
    public function get_task_details($sort_type, $date = '', $filter_type = '', $filter_value = '', $today_filter = '',$search_id = '') {
        $userid = $this->userid;
        $this->db->select('p.name,IFNULL(d.start_time,t.created_on) AS started,d.start_time,p.image_name,p.color_code,t.task_name,t.id AS task_id');
        $this->db->select("SUM(IF(d.total_minutes=0,1,0)) AS running_task", FALSE); //get running tasks of the user
        //$this->db->select('IF(t.complete_task=1,1,0) AS completed', FALSE); //get completed tasks of the user
        $this->db->select('CASE WHEN d.start_time IS NULL THEN 0 WHEN d.start_time IS NOT NULL THEN 1 END AS start',FALSE);//sort case
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS ta', 'ta.task_id = t.id');
        $this->db->join('time_details AS d', 'd.task_id = t.id AND d.user_id =ta.user_id','LEFT');
        $this->db->join('project AS p', 'p.id = t.project_id');

        if($filter_type == 'proj_filter'){
            if(!empty($filter_value)){
                $this->db->where_in('t.project_id',$filter_value);
            }
        }
        if($today_filter == 'today'){
            $this->db->where('ta.user_id',$userid);
            $this->db->where('task_date',date('Y-m-d'));
            $this->db->or_where('task_date IS NULL');
            if($filter_type == 'proj_filter'){
                if(!empty($filter_value)){
                    $this->db->where_in('t.project_id',$filter_value);
                }
            }
        }
        if($search_id != ''){
            $this->db->where('t.id',$search_id);
        }
        if($date == ''){
            $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
            $this->db->where('ta.user_id',$userid);
            $this->db->group_by('t.id');
        } else {
            if ($sort_type == 'daily_chart') {
                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where('d.task_date', $date);
                $this->db->where('d.end_time IS NOT NULL');
                $this->db->where('d.user_id', $userid);
                $this->db->group_by('d.id');
                $this->db->order_by('d.start_time','asc');
            } else if ($sort_type == 'weekly_chart') {
                
                $split_week = explode('~', $date);
                $start_date = isset($split_week[0]) ? date('Y-m-d',strtotime($split_week[0])) : date('Y-m-d');
                $end_date = isset($split_week[1]) ? date('Y-m-d',strtotime($split_week[1])) : date('Y-m-d',strtotime("+1 week"));

                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where(array('d.user_id' => $userid));
                $this->db->where('d.end_time IS NOT NULL');
                $this->db->where('d.task_date BETWEEN "' .$start_date. '" and "' .$end_date. '"');
                $this->db->group_by('d.task_date,d.task_id');
                $this->db->order_by('started','desc');
            } else {
                //for monthly chart
                $month_array = explode(' ',$date);
                $month_value = $month_array[0];
                $year_value = $month_array[1];
                $first = date($year_value . '-' . $month_value . '-' . '01');
                $last = date($year_value . '-' . $month_value . '-' . 't');
                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where(array('d.user_id' => $userid));
                $this->db->where('d.end_time IS NOT NULL');
                $this->db->where('d.task_date BETWEEN "' . $first . '" and "' . $last . '"');
                $this->db->group_by('d.task_date,d.task_id');
                $this->db->order_by('started','desc');
            }
        }
        if ($sort_type == 'task') {
            $this->db->order_by("t.task_name", "asc"); //sort by task name
        } else if ($sort_type == 'date') {
            if('start' == 0){
                $this->db->order_by('start','asc');
            }/*else if ('start' == 1){
                $this->db->order_by("start_time",'desc'); //default - sort by task date
            }*/
            $this->db->order_by("started",'desc'); //default - sort by task date
        } else if ($sort_type == 'project') {
            $this->db->order_by('p.name','asc'); //sort by project name
        } else if ($sort_type == 'duration') {
            $this->db->order_by('t_minutes','desc'); //sort by time spent
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $dataa = $query->result_array();
            foreach ($dataa as $d) {
                $data[] = array('image_name' => ($d['image_name'] != NULL) ? (base_url() . UPLOAD_PATH_PROJECT . $d['image_name']) : NULL, 'project' => $d['name'], 'project_color'=>$d['color_code'], 'task_name' => $d['task_name'], 'running_task' => $d['running_task'], 'start_time' => ($d['start_time'] != NULL) ? $d['start_time']: '', 't_minutes' => ($d['t_minutes'] !=NULL) ? $d['t_minutes']:'0', 'id' => $d['task_id']);
            }
        } else {
            $data = NULL;
        }
        return $data;
    }

    /*(API)Function to load all tasks of the user into user dashboard
     * 
     * @params $sort_type, $date and $limit
     * 
     * returns $result
     */
    public function get_user_task_info($sort_type, $post_data, $limit) {
        $this->db->select('p.name as project_name,p.id as project_id,p.image_name,t.task_name,t.description,t.id,t.module_id');
        $this->db->select('IF(t.complete_task=1,1,0) AS completed', FALSE); //get completed tasks of the user
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a', 'a.task_id = t.id');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m', 'm.id = t.module_id');
        $this->db->where(array('a.user_id' => $post_data['userid']));
        $this->db->group_by('t.id');
        if ($sort_type == 'name') {
            $this->db->order_by("t.task_name", "asc"); //sort by task name
            
        } else if ($sort_type == 'date') {
            $this->db->order_by("d.task_date", "asc"); //sort by task date
            
        } else if ($sort_type == 'task') {
            $this->db->order_by("t.id", "desc"); //sort by task id
            
        }
        if (isset($post_data['page_no'])) {
            $offset = $limit * ($post_data['page_no'] - 1);
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $data = $query->result_array();
        $result = $data;
        foreach ($data as $key => $value) {
            $this->db->select('td.id,td.start_time,td.end_time,td.task_description,td.total_hours,td.total_minutes');
            $this->db->from('time_details AS td');
            $this->db->where(array('td.task_id' => $value['id'], 'td.user_id' => $post_data['userid']));
            $query = $this->db->get();
            $sub_data = $query->result_array();
            $result[$key]['time_details'] = $sub_data;
            //print_r($this->db->last_query());die;
            
        }
        //print_r($this->db->last_query());die;
        //print_r($data);exit;
        return $result;
    }

    /**
     * (API)Function to get user tasks count
     * 
     * @param $userid
     * 
     * returns $task_count
     */
    public function get_user_task_count($userid) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a', 'a.task_id = t.id');
        $this->db->where(array('a.user_id' => $userid));
        $query = $this->db->get();
        $task_count = $query->result_array();
        return $task_count[0]['count'];

    }


    /**
     * Function to get task details to edit task page
     * 
     * @param $task_id and $userid
     * 
     * returns $task_data
     */
    public function get_task_info($task_id,$userid) {
        
        $details = array();

        //check whether the task id is valid
        $this->db->select('ta.user_id,p.name,p.id AS project_id,m.id AS module_id,m.name AS module_name,t.task_name,t.description,t.id AS task_id');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS ta','ta.task_id = t.id');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m', 'm.id = t.module_id');
        $this->db->where(array('t.id'=>$task_id,'ta.user_id'=>$userid));
        $task_data = $this->db->get()->row_array();//get task,project and module info

        //check whether there is a timeline data for the task
        $check_timeline = $this->db->get_where('time_details',array('task_id'=>$task_id,'user_id'=>$userid));
        if($check_timeline->num_rows() > 0){//if there is a timeline data
            $this->db->select('id,task_date,start_time,end_time,task_description');
            $this->db->from('time_details');
            $this->db->where(array('task_id' => $task_id, 'user_id' => $userid));
            $this->db->order_by('task_date,start_time','desc');
            $query = $this->db->get();
            $data = $query->result_array();
            foreach($data as $d){
                $details['timeline_data'][] = array('table_id'=>$d['id'],'task_date'=>($d['task_date'])?$d['task_date']:date('Y-m-d'),'start_time'=>isset($d['start_time'])?$this->convert_date($d['start_time']):'','end_time'=>isset($d['end_time'])?$this->convert_date($d['end_time']):'','task_description'=>($d['task_description'])?$d['task_description']:null);
            }
        }//if there is no timeline data, send only the task information to edit task page
        $details['task_data'] = array('task_name'=>$task_data['task_name'],'project_name'=>$task_data['name'],'project_id'=>$task_data['project_id'],'description'=>$task_data['description'],'task_id'=>$task_data['task_id'],'module_name'=>$task_data['module_name'],'module_id'=>$task_data['module_id']);

        //send list of project module to edit task page
        if(isset($details['task_data'])){
            $details['project_list'] = $this->get_project_name();
            $details['project_module_list'] = $this->get_module_name($task_data['project_id']);
        }
        return $details;
    }

    /**
     * Function to get punch-in time to edit task and add task page
     * 
     * @param $user_id
     * 
     * returns $punch-in time
     */
    public function get_punch_in_time($user_id,$date){
        $this->db->where(array('task_date'=>$date,'user_id'=>$user_id,));
        $this->db->where('end_time IS NULL');
        $punch_in = $this->db->get('login_details');
        if($punch_in->num_rows() == 1){
            $punch_in_time = $punch_in->row_array();
            $converted_time = $this->convert_date($punch_in_time['start_time']);
        }else{
            $converted_time = NULL;
        }
        return $converted_time;
    }
    
    /**
     * Function to get running task data to stop
     * 
     * @param void
     * 
     * returns $tasks
     */
    public function running_task_data() {
        $userid = $this->userid;
        $this->db->select('d.task_id,d.start_time,t.task_name,t.description');
        //$this->db->select('count(IF(d.total_minutes=0,1,0)) AS running_task_count');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where('d.total_minutes','0');
        $this->db->where('d.task_date !=',date('Y-m-d'));
        $this->db->where('d.user_id',$userid);
        $tasks_data = $this->db->get();
        if($tasks_data->num_rows() > 0){
            $data['task_data'] = $tasks_data->result_array();
        }else{
            $data['task_data'] = '';
        }
        $this->db->select('id,user_id,task_date,start_time');
        $this->db->from('login_details');
        $this->db->where('task_date !=',date('Y-m-d'));
        $this->db->where('user_id',$userid);
        $this->db->where('end_time IS NULL');
        $login_check = $this->db->get();
        if($login_check->num_rows() > 0){
            $data['login_data'] = $login_check->row_array();
        }else{
            $data['login_data'] = '';
        }
        return $data;
    }


    /**
     * Function to start timer
     * 
     * @param $data
     * 
     * returns $result
     */
    public function start_timer($data) {
        if ($data['task_type'] == 'login') //check if the timer-start request is for login
        {
            $this->db->where(array('task_date' => date('Y-m-d'), 'user_id' => $data['userid']));
            $query_check = $this->db->get('login_details');
            if ($query_check->num_rows() > 0) {
                return true;
            }else{
                 $array1 = array('user_id'=>$data['userid'],'task_date'=>date('Y-m-d'),'start_time'=>$data['start_time'],'created_on'=>date('Y-m-d H:i:s'));
                $this->db->set($array1);
                $query1 = $this->db->insert('login_details', $array1);
                if ($query1) {
                    return true;
                } else {
                    return false;
                }
            }
        } else if ($data['task_type'] == 'task') //check if the timer-start request is for task
        {
            
            $start = (isset($data['start_time']))?$data['start_time']:date('Y-m-d H:i:s');
            $array2 = array('task_id' => $data['task_id'], 'user_id' => $data['userid'], 'task_date' => date('Y-m-d'), 'start_time' => $start, 'total_hours' => '0', 'total_minutes' => '0', 'created_on' => date('Y-m-d H:i:s'));
            $this->db->set($array2);
            $query2 = $this->db->insert('time_details', $array2);
            if ($query2) {
                $this->db->select('t.task_name,d.user_id,d.task_id,d.start_time,t.description');
                $this->db->select_sum('d.total_minutes', 't_minutes');
                $this->db->from('time_details AS d');
                $this->db->join('task AS t', 't.id = d.task_id');
                $this->db->where('d.task_id', $data['task_id']);
                //$this->db->where('d.end_time IS NULL');
                $details = $this->db->get();
                return $details->row_array();
            } else {
                return false;
            }
        }
    }

    /**
     * Function to stop timer
     * 
     * @param $req_data
     * 
     * returns $result
     */
    public function stop_timer($req_data) {
        $this->db->select('id,task_date,start_time');
        $this->db->from('time_details');
        $this->db->where(array('task_id' => $req_data['task_id'], 'user_id' => $req_data['userid'], 'total_minutes' => '0'));
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        //print_r($this->db->last_query());die;
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
            if ($req_data['end_time'] != '') {
                if (!empty($req_data['date'])) {
                    $update_time = $req_data['date'] . " " . date("H:i:s", strtotime($req_data['end_time']));
                } else {
                    $update_time = $data['task_date'] . " " . date('H:i:s', strtotime($req_data['end_time']));
                }
                //$update_time = $req_data['end_time'];
            } else {
                $update_time = $data['task_date'] . " " . date('H:i:s');
            }
            $diff = (strtotime($update_time) - strtotime($data['start_time']));
            $t_minutes = (($diff / 60) < 1) ? ceil(abs($diff / 60)) : round(abs($diff / 60),2);
            $hours = round(abs($diff / (60 * 60)));
            $this->db->where(array('id' => $data['id']));
            $query2 = $this->db->update('time_details', array('task_description' => $req_data['task_desc'], 'end_time' => $update_time, 'total_hours' => $hours, 'total_minutes' => $t_minutes, 'modified_on' => date('Y-m-d H:i:s')));
            if ($query2) {
                if ($req_data['flag'] == 1) //if flag is 1, request is to complete the task
                {
                    $this->db->where('id', $req_data['task_id']);
                    $query = $this->db->update('task', array('complete_task' => '1'));
                    if ($query) {
                        $flag_status = "complete";
                        return $flag_status;
                    } else {
                        return false;
                    }
                } else if ($req_data['flag'] == 0) //if flag is 0, request is to stop the task
                {
                    $this->db->select('t.task_name,d.user_id,d.task_id,d.start_time,t.description');
                    $this->db->select_sum('d.total_minutes', 't_minutes');
                    $this->db->from('time_details AS d');
                    $this->db->join('task AS t', 't.id = d.task_id');
                    $this->db->where('d.task_id', $req_data['task_id']);
                    $details = $this->db->get();
                    return $details->row_array();
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to get Activity Chart Data for daily chart
     * 
     * @params $userid and $taskdate
     * 
     * returns $data
     */
    public function get_daily_activity($userid,$taskdate,$project_id = array()){
        $data = array();
        $this->db->select('d.*,d.id AS table_id,p.id as project_id,p.name,p.color_code,t.task_name,t.id AS task_id,t.created_on');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->join('project AS p','p.id = t.project_id');
        $this->db->where('d.end_time IS NOT NULL'); //tasks that are not running
        $this->db->where(array('d.user_id' => $userid, 'd.task_date' => $taskdate));
        if(!empty($project_id)){
            $this->db->where_in('t.project_id',$project_id);
        }
        $this->db->group_by('d.id');
        $this->db->order_by('d.start_time','asc');
        $query = $this->db->get();
        $data = $query->result_array();
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
        }
        return $data;
    }
    /**
     * Function to get Activity Chart Data for weekly chart
     * 
     * @params $userid and $taskdate
     * 
     * returns $data
     */
    public function get_weekly_activity($userid,$start_date,$end_date,$project_id = array()){
        $data = array();
        $where = "WHERE `d`.`task_date` BETWEEN '".$start_date."' AND '".$end_date."' AND d.end_time IS NOT NULL AND d.user_id = '".$userid."'";
        if(!empty($project_id)){
            $project_filter = implode(',',array_values($project_id));
            $where = "WHERE `d`.`task_date` BETWEEN '".$start_date."' AND '".$end_date."' AND d.end_time IS NOT NULL AND d.user_id = '".$userid."' AND t.project_id IN ($project_filter)";
        }
        $query = $this->db->query("SELECT t.id AS task_id,t.task_name,t.created_on,p.id as project_id,p.name,p.color_code,d.task_id, d.start_time, d.end_time, d.task_description,d.task_date, SUM(`d`.`total_minutes`) AS `minutes` FROM `task` AS `t` JOIN `project` AS `p` ON `p`.`id` = `t`.`project_id` LEFT JOIN `task_assignee` AS `ta` ON ta.task_id = t.id JOIN `time_details` AS `d` ON d.task_id = t.id $where GROUP BY t.id");
        if($query->num_rows() > 0){
            $data = $query->result_array();
        }
        return $data;
    }
    /**
     * Function to get total time for the timeline data
     * 
     * @params $userid,$task_id,$task_date
     * 
     * returns $data
     */
    public function get_total_time_on_date($userid,$task_id,$task_date){
        $timeline_data = array();
        $select_task_data = $this->db->query("SELECT SUM(d.total_minutes) AS t_minutes FROM time_details AS d WHERE `d`.`task_date` = '".$task_date."' AND d.end_time IS NOT NULL AND d.task_id='".$task_id."' AND d.user_id=".$userid);
        if($select_task_data->num_rows() > 0){
            $timeline_data = $select_task_data->result_array();
        }
        return $timeline_data;

    }

    /**
     * Function to get Activity Chart Data for monthly chart
     * 
     * @params $userid,$start_date,$end_date
     * 
     * returns $data
     */
    public function get_monthly_activity($userid,$start_date,$end_date,$project_id = array()){
        $data = array();
        $where = "WHERE `d`.`user_id` = ".$userid." AND `d`.`end_time` IS NOT NULL AND `d`.`task_date` BETWEEN '".$start_date."' and '".$end_date."'";
        if(!empty($project_id)){
            $project_filter = implode(',',array_values($project_id));
            $where = "WHERE `d`.`user_id` = ".$userid." AND `d`.`end_time` IS NOT NULL AND `d`.`task_date` BETWEEN '".$start_date."' and '".$end_date."' AND t.project_id IN ($project_filter)";
        }
        $query = $this->db->query("SELECT `d`.`task_date`, `t`.`task_name`,t.created_on,d.task_id,d.task_date,p.name,p.id as project_id, p.color_code,SUM(`d`.`total_minutes`) AS `t_minutes` FROM `time_details` AS `d` join task as t on d.task_id=t.id join project as p on t.project_id=p.id $where GROUP BY  d.task_date");
        if ($query->num_rows() > 0) {
                $data = $query->result_array();
        }
        return $data;
    }

    /**
     * Function to get Activity Chart Data
     * 
     * @params $chart_type and $date
     * 
     * returns $chart_data if data is present
     * returns $status if no data is present
     */
    public function get_activity($chart_type, $date, $userid, $filter_value = array()) {
        //get task activities
        $taskdate = $date;
        $total_minutes = 0;
        if ($chart_type == "daily_chart") {
            $data = $this->get_daily_activity($userid,$taskdate,$filter_value);
            if(!empty($data)){
                foreach ($data as $d) {
                    $table_id[] = $d['table_id'];
                    $task_id[] = $d['task_id'];
                    $task_name[] = $d['task_name'];
                    $project_color[] = $d['color_code'];
                    $object = new stdClass();
                    $object->start_time = $d['start_time'];
                    $object->end_time = $d['end_time'];
                    $object->total_minutes = $d['total_minutes'];
                    $timing[] = $object;
                }
                foreach($timing as $time){
                    $total_minutes += $time->total_minutes;
                }
                $chart_data = array('daily_chart', "status" => TRUE,
                    //"labels"=> $week_days,
                    "data" => array($task_id, $timing, $task_name, $project_color),
                    "total_minutes" => $total_minutes);
            }else {
                $chart_data = array('daily_chart', 'status' => FALSE, 'data' => "No activity in this date.",'total_minutes' => '0');
            }
            return $chart_data;
        }
        if ($chart_type == "weekly_chart") {
            $total_mins=0;
            $hours = array();
            $split_week = explode('~', $date);
            $start_date = isset($split_week[0]) ? date('Y-m-d',strtotime($split_week[0])) : date('Y-m-d');
            $end_date = isset($split_week[1]) ? date('Y-m-d',strtotime($split_week[1])) : date('Y-m-d',strtotime("+1 week"));


            $date_range = $this->get_dates_from_range($start_date, $end_date); //week days(Sun-Sat) in date format(Y-m-d)

            $data = $this->get_weekly_activity($userid,$start_date,$end_date,$filter_value);
            if(!empty($data)){
                for($i=0;$i<count($data);$i++) {
                    $k=0;
                    foreach($date_range AS $r_day){
                        $timeline_data = $this->get_total_time_on_date($userid,$data[$i]['task_id'],$r_day);
                        if(!empty($timeline_data)){
                            foreach($timeline_data AS $time){
                                $total_mins += $time['t_minutes'];
                                //$hours[$k] = round(($time['t_minutes']/60),2);
                                $format_hours[$k] = sprintf('%02d.%02d',floor($time['t_minutes'] / 60),($time['t_minutes']%60));
                            }
                        }/*else{
                            $hours[$k] = '0';
                        }*/
                        $k=$k+1;
                    }
                    $send_data[$i] = array('task_name'=>$data[$i]['task_name'],'time'=>$format_hours,'project_color'=>$data[$i]['color_code']);
                }
                /*$hours = floor($total_mins / 60);
                $minutes = ($total_mins % 60);
                if(($minutes < 1) && ($hours < 1))
                    $time_taken = sprintf('--');
                else if($minutes < 1)
                    $time_taken = sprintf('%02d:00', $hours);
                else if($hours < 1)
                    $time_taken = sprintf('00:%02d', $minutes);
                else
                    $time_taken = sprintf('%02d:%02d', $hours, $minutes);*/

                $chart_data = array('weekly_chart','status'=>TRUE,'data'=>$send_data,'total_minutes'=>$total_mins);
            }else{
                $chart_data = array('weekly_chart','status'=>FALSE,'data'=>NULL,'total_minutes'=>'0');
            }
            return $chart_data;
        }
        if ($chart_type == "monthly_chart") {
            $month_array = explode(' ',$date);
            $month_value = $month_array[0];
            $year_value = $month_array[1];
            $first = date($year_value . '-' . $month_value . '-' . '01');
            $last = date($year_value . '-' . $month_value . '-' . 't');
            $data = $this->get_monthly_activity($userid,$first,$last,$filter_value);
            if(!empty($data)){
                foreach ($data as $d) {
                    $format_hours = sprintf('%02d.%02d',floor($d['t_minutes'] / 60),($d['t_minutes']%60));
                    $values[] = array(date('Y-m-d', strtotime($d['task_date'])), $format_hours, $d['color_code']);
                    $total_minutes += $d['t_minutes'];
                    
                }
                $chart_data = array('monthly_chart', "status" => TRUE,
                /*date with working hours in standard format*/
                "data" => $values,
                "total_minutes" => $total_minutes);
            } else {
                $chart_data = array('monthly_chart', "status" => FALSE, "data" => array(),'total_minutes' => '0');
            }
            return $chart_data;
        }
    }

    /**
     * Function to make time intervals in daily chart
     * 
     * @params $start__time, $end_time and $duration
     * 
     * returns array($result_array, $returndata);
     */
    public function split_time($start__time, $end_time, $duration = "60") {
        $result_array = array(); // Define output
        $start_time = strtotime($start__time); //Get Timestamp
        $end_time = strtotime($end_time); //Get Timestamp
        $add_mins = $duration * 60;
        $dat = date('Y-m-d', $start_time);
        $starttime = date('Y-m-d H:i:s', strtotime('+1 hour', $start_time));
        $start = strtotime($starttime);
        $i = strtotime($dat . '09:00:00');
        while ($i <= $start_time) {
            $result_array[] = date("G:00", $i);
            $returndata[] = '0';
            $i+= $add_mins;
        }
        while ($start <= $end_time) //Run loop
        {
            $result_array[] = date("G:00", $start);
            $returndata[] = '1';
            $start+= $add_mins; //Endtime check
            
        }
        $endtime = date('Y-m-d H:i:s', strtotime('+1 hour', $end_time));
        $end__time = strtotime($endtime);
        $end = strtotime($dat . '23:59:59');
        while ($end__time <= $end) {
            $result_array[] = date("G:00", $end__time);
            $returndata[] = '0';
            $end__time+= $add_mins;
        }
        return array($result_array, $returndata);
    }

    /**
     * Function to get start date and end date from the week input
     * 
     * @params $week, $year
     * 
     * returns array($start_date,$end_date)
     */
    public function get_start_and_end_date($week, $year) {
        return [
        (new DateTime())->setISODate($year, $week, 0)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 6)->format('Y-m-d') //end date
        ];
    }

    /**
     * Function to get all the dates for the given date range
     * 
     * @params $week, $year
     *
     * returns array(dates in a week)
     */
    public function get_dates_from_range($start, $end, $format = 'Y-m-d') { 
        // Declare an empty array 
        $array = array(); 
        // Variable that store the date interval of period 1 day 
        $interval = new DateInterval('P1D'); 
        $realEnd = new DateTime($end); 
        $realEnd->add($interval); 
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
        // Use loop to store date into array 
        foreach($period as $date) {                  
            $array[] = $date->format($format);  
        }
        // Return the array elements 
        return $array; 
    }

    /**
     * Function to get project name into the add task page
     * 
     * @param void
     * 
     * returns $result
     */
    public function get_project_name() {
        $result = array();
        $this->db->select('p.id,p.name');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS ps', 'ps.project_id = p.id');
        $this->db->where(array('ps.user_id' => $this->userid));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }
        return $result;
    }

    /**
     * Function to get project module into add task page
     * 
     * @param $project_id
     * 
     * returns $result 
     */
    public function get_module_name($project_id) {
        $p_id = $project_id;
        $query = $this->db->query("SELECT id,name FROM project_module WHERE project_id = {$p_id} OR project_id = 0");
        $result = $query->result();
        return $result;
    }

    /**
     * Function to check whether task already exists inorder to add a new task
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
    public function task_exists() {
        $task_name = $this->input->post('task_name');
        $this->db->select('t.task_name');
        $this->db->from('task As t');
        $this->db->join('task_assignee AS ta','ta.task_id = t.id','LEFT');
        $this->db->where(array('t.task_name' => $task_name, 'ta.user_id' => $this->userid, 't.project_id' => $this->input->post('project')));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('task_exists', 'Task name exists.');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to add tasks or edit task
     * 
     * @param $data
     * 
     * returns $task id if task data is added
     * returns false if add task function is not successful
     * returns status if delete option is selected
     */
    public function add_tasks($data) {
        $userid = $data['userid'];
        if (($data['project_module'] == 'Select module') || ($data['project_module'] == '')) {
            $check_module = $this->db->get_where('project_module',array('id'=>1));
            if($check_module->num_rows() == 1){
                $module_id = $check_module->row_array()['id'];
            }else{
                $module_data = array('id'=>1,'project_id'=>$data['project_id'],'name'=>'General','meta_data'=>'Default for any task if not select any module.','created_on'=>date('Y-m-d H:i:s'));
                $this->db->set($module_data);
                $insert_module_id = $this->db->insert('project_module',$module_data);
                $module_id = $this->db->insert_id();
            }
        } else {
            $module_id = $data['project_module'];
        }

        /******** Edit Case Start ********/
        if (isset($data['task_id'])) {
            $array = array('task_name' => $data['task_name'], 'description' => $data['task_desc'], 'modified_on' => date('Y:m:d H:i:s'), 'project_id' => $data['project_id'], 'module_id' => $module_id);
            $this->db->where(array('id' => $data['task_id']));
            $query = $this->db->update('task', $array);

            if(isset($data['timings'])){
                $timings = $data['timings'];
                if (sizeof($timings) > 0) {
                    foreach($timings as $time) {

                        if(($time['start']) == '' || $time['start'] == null)
                            $start = $time['date'] . ' ' . '00:00:00'; // break Return with error
                        else{
                            $start_time = strtotime($time['start']);
                            //$start = date('Y-m-d H:i:s',$start_time);
                            $start = $time['start'];
                        }

                        if(trim($time['end']) == '' || ($time['end'] == null)){
                            $end = null;
                        }
                        else{
                            $end_time = strtotime($time['end']);
                            //$end = date('Y-m-d H:i:s',$end_time);
                            $end = $time['end'];
                        }

                        $description = "";
                        if (isset($time['task_description'])) {
                            $description = $time['task_description'];
                        }
                        $diff = 0;
                        $hours = 0;
                        $total_mins = 0;
                        if($end != null){
                            $diff = $end_time - $start_time;
                            $hours = $diff / (60 * 60);
                            $minutes = $diff / 60;
                            $total_mins = ($minutes < 1) ? ceil(abs($minutes)) : abs($minutes);
                        }
                        if (isset($time['table_id']) && !empty($time['table_id'])) {
                            $table_id = $time['table_id'];
                            $array = array('start_time' => $start, 'end_time' => $end, 'task_description' => $description, 'user_id' => $userid, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $total_mins, 'task_date' => $time['date'], 'modified_on' => date('Y-m-d H:i:s'));
                            $this->db->where(array('user_id' => $userid, 'task_id' => $data['task_id'], 'id' => $table_id));
                            $query = $this->db->update('time_details', $array);
                        } else {
                            $array = array('user_id' => $userid, 'start_time' => $start, 'end_time' => $end, 'task_description' => $description, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $total_mins, 'task_date' => $time['date'], 'created_on' => date('Y-m-d H:i:s'));
                            $this->db->set($array);
                            $query = $this->db->insert('time_details', $array);
                        }
                    }
                }
            }
            if (isset($data['deleted_time_range'])) {
                $data['deleted_time_range'] = json_decode($data['deleted_time_range'], true);
                foreach ($data['deleted_time_range'] as $key => $value) {
                    $this->db->where('id', $value);
                    $this->db->where('user_id', $userid);
                    $this->db->where('task_id', $data['task_id']);
                    $this->db->delete('time_details');
                }
            }
            return true;
        } /******** Edit Case End ********/
        else 
        {/******** Add Case Start ********/
            
            $array = array('task_name' => $data['task_name'], 'description' => $data['task_desc'], 'project_id' => $data['project_id'], 'module_id' => $module_id, 'created_on' => date('Y-m-d H:i:s'));
            $this->db->set($array);
            $query = $this->db->insert('task', $array);
            if (!$query) {
                return false;
            } else {
                $last_insert_id = $this->db->insert_id();
                $array = array('user_id' => $userid, 'task_id' => $last_insert_id, 'created_on' => date('Y-m-d H:i:s'));
                $this->db->set($array);
                $query = $this->db->insert('task_assignee', $array);
                if (!$query) {
                    return false;
                } else {
                    if(isset($data['time_range']))
                    {
                     //Add timings into time_details table
                        $date_value = $data['time_range'];
                        if(!is_array($date_value)){
                                $date_value = json_decode($date_value, true);
                        }
                        if(sizeof($date_value) >= 1)
                        {
                            foreach ($date_value AS $value)
                            {
                                if(($value['start']) == '' || ($value['start'] == null))
                                    $start = $value['date'] . ' ' . '00:00:00';
                                else{
                                    $start_time = strtotime($value['start']);
                                    //$start = $date_value[$i]['date'] . ' ' . date('H:i:s', $start_time);
                                    $start = $value['start'];
                                    if($value['end'] == '' || ($value['end'] == null) || ($value['end'] == ' ') || empty($value['end'])){
                                        $end = null;
                                    }
                                    else{
                                        $end_time = strtotime($value['end']);
                                        //$end = $date_value[$i]['date'].' '.date('H:i:s',$end_time);
                                        $end = $value['end'];
                                    }
                                }
                                $task_description = "";
                                if (isset($value['task_description'])) {
                                    $task_description = $value['task_description'];
                                }
                                $diff = 0;
                                $hours = 0;
                                $total_mins = 0;
                                if($end != null){
                                    $diff = $end_time - $start_time;
                                    $hours = $diff / (60 * 60);
                                    $minutes = $diff / 60;
                                    $total_mins = ($minutes < 1) ? ceil(abs($minutes)) : abs($minutes);
                                }
                                $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'task_date'=>$value['date'],'start_time'=>$start,'end_time'=>$end,'task_description'=>$task_description,'total_hours'=>$hours,'total_minutes'=>$total_mins,'created_on'=>date('Y-m-d H:i:s'));
                                $this->db->set($array);
                                $query = $this->db->insert('time_details',$array);      
                            }
                        }
                    }
                    return $last_insert_id;
                }
            }
            
        }
        /******** Add Case End ********/
    }
    /*End of add task function */

    /**
     * Function to Check for the Old Password
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
    public function password_exists() {
        $email = $this->session->userdata('email');
        $query = $this->db->get_where('users', array('email' => $email, 'password' => md5($this->input->post('psw1'))));
        if ($query->num_rows() == 1) {
            return true;
        } else {
            $this->form_validation->set_message('password_exists', 'Please enter your old password properly.');
            return false;
        }
    }

    /**
     * Function to change password
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
    public function change_password() {
        $new_pwd = $this->input->post('psw11');
        $confirm_pwd = $this->input->post('psw22');
        if ($new_pwd == $confirm_pwd) {
            if ($this->session->userdata('email')) {
                $email = $this->session->userdata('email');
            } else {
                $email = $this->input->post('mail');
            }
            $this->db->set('password', md5($new_pwd));
            $this->db->where('email', $email);
            $query = $this->db->update('users');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->session->set_flashdata('err_msg', 'Passwords do not match..');
            return false;
        }
    }

    /**
     * Function to fetch user profile data to profile page
     * 
     * @param $userid
     * 
     * returns $profile_data
     */
    public function my_profile($userid) {
        $this->db->select('u.name,u.phone,u.email,u.profile');
        $this->db->select_sum('d.total_minutes', 'total_time');
        $this->db->from('users AS u');
        $this->db->join('time_details AS d', 'd.user_id = u.id');
        $this->db->where('u.id', $userid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $u = $query->row_array();
            $this->db->select_sum('d.total_minutes', 't_minutes');
            $this->db->from('time_details AS d');
            $this->db->where('d.user_id', $userid);
            $this->db->where('d.task_date BETWEEN "' . date('Y-m-d', strtotime(date('Y-m-1'))) . '" and "' . date('Y-m-d', strtotime(date('Y-m-t'))) . '"');
            $time = $this->db->get()->row_array();
            $profile_data = array('name' => $u['name'], 'email' => $u['email'], 'phone' => $u['phone'], 'profile' => $u['profile'], 'total_time' => round(($u['total_time'] / 60), 2), 't_minutes' => round(($time['t_minutes'] / 60), 2));
        } else {
            $profile_data = '';
        }
        return $profile_data;
    }

    /**
     * Function to edit user name or phone number in user profile page
     * 
     * @param $username OR $phone
     * 
     * returns TRUE/FALSE
     */
    public function edit_profile($data){
        $userid = $this->userid;

        //set value for profile column
        if(isset($data['profile_photo'])){
            $profile_pic = $data['profile_photo'];
        }else{
            $get_profile_image = $this->db->get_where('users',array('id'=>$userid));
            if($get_profile_image->num_rows() > 0){
                $profile_pic = $get_profile_image->row()->profile;
            }else{
                $profile_pic = 'default.png';
            }
        }

        //update user profile data
        $this->db->where('id',$userid);
        $update = $this->db->update('users',array('phone'=>$data['phone'],'name'=>$data['name'],'profile'=>$profile_pic,'modified_on'=>date('Y-m-d H:i:s')));
        if ($update) {
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('user_profile');
            $this->session->set_userdata('username',$data['name']);
            $this->session->set_userdata('user_profile',$profile_pic);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to get chart data for user profile
     * 
     * @param $year
     * 
     * returns false, no data is present in the given year
     * returns $values, if data is present in the given year
     */
    public function user_chart_data($year) {
        $userid = $this->userid;
        $year_start = date($year.'-01-01');
        $year_end = date($year.'-12-31');
        $this->db->select('id');
        $this->db->from('time_details');
        $this->db->where(array('user_id' => $userid));
        $this->db->where('end_time IS NOT NULL');
        $this->db->where('task_date BETWEEN "' . $year_start . '" and "' . $year_end . '"');
        $check_year = $this->db->get();
        $check = $check_year->row_array();
        if($check['id'] == ''){
            return false;
        }
        for ($i = 1;$i <= 12;$i++) {
            $months[$i] = date($year . "-" . $i . '');
        }
        foreach ($months as $month) {
            $start = date($month.'-01');
            $end = date($month.'-t');
            $this->db->select_sum('total_minutes', 't_minutes');
            $this->db->from('time_details');
            $this->db->where(array('user_id' => $userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "' . $start . '" and "' . $end . '"');
            $query = $this->db->get();
            $data = $query->result_array();
            foreach ($data as $d) {
                $values[] = sprintf('%02d.%02d',floor($d['t_minutes']/60),($d['t_minutes']%60));
            }
        }
        return $values;
          
    }

    /**
     * Function to update punchout time(for same day punchout)
     * 
     * @params $userid and $time
     * 
     * returns TRUE/FALSE
     */
    public function update_logout_time_web($userid,$time) {
        //check for entry with the same login date
        $this->db->where(array('task_date' => date('Y-m-d'), 'user_id' => $userid, 'end_time IS NULL'));
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) {
            $data = $query_check->row_array();
            $array = array('end_time' => $time, 'modified_on' => date('Y-m-d H:i:s'));
            $this->db->where('id', $data['id']);
            $query = $this->db->update('login_details', $array);
            if ($this->db->affected_rows() == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to update logout time
     * 
     * @param $userid
     * 
     * returns TRUE/FALSE
     */
    public function update_logout_time($userid) {
        //check for entry with the same login date
        $this->db->where(array('task_date' => date('Y-m-d'), 'user_id' => $userid, 'end_time IS NULL'));
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) {
            $data = $query_check->row_array();
            $array = array('end_time' => date('Y-m-d H:i:s'), 'modified_on' => date('Y-m-d H:i:s'));
            $this->db->where('id', $data['id']);
            $query = $this->db->update('login_details', $array);
            if ($this->db->affected_rows() == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to update punchout time if previous day punchout is not done
     * 
     * @param $row_id
     * 
     * returns TRUE/FALSE
     */
    public function punchout_previous($punchout_time,$punchout_id)
    {
        $update_logout_time = array('end_time'=>$punchout_time,'modified_on'=>date('Y-m-d H:i:s'));
        $this->db->where('id',$punchout_id);
        $update_login_details = $this->db->update('login_details',$update_logout_time);
        if ($this->db->affected_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * (API)Function to get user projects
     * 
     * @param $user_id
     * 
     * returns $data
     */
    public function get_user_projects($user_id) {
        $this->db->select('p.name as project_name,p.id,p.color_code,p.image_name');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS pa', 'p.id = pa.project_id');
        if ($user_id != null) $this->db->where(array('pa.user_id' => $user_id));
        $query = $this->db->get();
        $data = $query->result_array();
        foreach ($data as $key => $value) {
            $this->db->select('id,name');
            $this->db->from('project_module');
            $this->db->where(array('project_id' => $value['id']));
            $query = $this->db->get();
            $proj_mod = $query->result_array();
            //print_r($this->db->last_query());die;
            $data[$key]['modules'] = $proj_mod;
            if($data[$key]['image_name'] != "")
            {
                $data[$key]['image_name'] = base_url().UPLOAD_PATH.$data[$key]['image_name'];
            }
        }
        return $data;
    }

    /**
     * (API)Function to get default module
     * 
     * @param void
     * 
     * returns $result
     */
    public function get_default_module() {
        $this->db->select('id,name');
        $this->db->from('project_module');
        $this->db->where(array('project_id' => 0));
        $query = $this->db->get();
        $default_mod = $query->result_array();
        return $default_mod;
    }

    /**
     * (API)Function to get login details
     * 
     * @params $userid, $page_no and $limit
     * 
     * returns $result
     */
    public function get_login_details($userid, $page_no, $limit) {
        $offset = $limit * ($page_no - 1);
        $this->db->select('task_date,start_time,end_time');
        $this->db->from('login_details');
        $this->db->where(array('user_id' => $userid));
        $this->db->order_by("task_date", "desc"); //sort by task id
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $login_list = $query->result_array();
        return $login_list;
    }

    /**
     * (API)Function to get total login details
     * 
     * @param $userid
     * 
     * returns $result
     */
    public function total_login_details($userid) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('login_details');
        $this->db->where(array('user_id' => $userid));
        $query = $this->db->get();
        $login_count = $query->result_array();
        return $login_count[0]['count'];
    }

    /**
     * (API)Function to check user credentials
     * 
     * @param $post
     * 
     * returns TRUE/FALSE
     */
    public function check_credentials($post) {
        $username = $post['email'];
        $password = $post['password'];
        $query = $this->db->get_where('users', array('email' => $username, 'password' => md5($password)));
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * (API)Function to change password
     * 
     * @params $new_pwd and $email
     * 
     * returns TRUE/FALSE
     */
    public function change_password_device($new_pwd, $email) {
        $this->db->set('password', md5($new_pwd));
        $this->db->where('email', $email);
        $query = $this->db->update('users');
        if ($this->db->affected_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * (API)Function to check user email
     * 
     * @param $email
     * 
     * returns TRUE/FALSE
     */
    public function check_email($email) {
        $query = $this->db->get_where('users', array('email' => $email));
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * (API)Function to update logout time
     * 
     * @param $req_data
     * 
     * returns TRUE/FALSE
     */
    public function update_logout_time_device($req_data) {
        //check for entry with the same login date
        $this->db->where(array('task_date' => $req_data['date'], 'user_id' => $req_data['userid'], 'end_time IS NULL'));
        $this->db->order_by("id", "desc");
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) {
            $data = $query_check->row_array();
            $array = array('end_time' => $req_data['date'] . " " . date("H:i:s", strtotime($req_data['time'])), 'modified_on' => date('Y:m:d H:i:s'));
            $this->db->where('id', $data['id']);
            $query = $this->db->update('login_details', $array);
            if ($this->db->affected_rows() == 1) {
                //print_r($this->db->last_query());die;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete_task_data($user_id,$table_id)
    {
        $this->db->where(['id' => $table_id, 'user_id'=>$user_id]);
        $res = $this->db->delete('time_details');
        if($res){
            return true;
        }else{
            return false;
        }
    }

    //add task model
    public function add_tasks_device($data) {
        //date("H:i", strtotime("1:30 PM"));
        $userid = $data['userid'];
        
        if ($data['action'] == 'edit') {
            if (($data['project_module'] == 'Select module') || ($data['project_module'] == '')) {
                $module_id = 1;
            } else {
                $module_id = $data['project_module'];
            }

            $array = array('task_name' => $data['task_name'], 'description' => $data['task_desc'], 'modified_on' => date('Y:m:d H:i:s'), 'project_id' => $data['project_id'], 'module_id' => $module_id);
            $this->db->where(array('id' => $data['task_id']));
            $query = $this->db->update('task', $array);
            $time_range = $data['time_range'];
            if (!is_array($time_range)) {
                $time_range = json_decode($time_range, true);
            }
            if (sizeof($time_range) > 0) {
                foreach($time_range as $t) {
                    $task_description = "";
                    $table_id = null;
                    if (isset($t['table_id'])) {
                        $table_id = $t['table_id'];
                    }
                    //$table_id[$i] = $time_range[$i]['table_id'];
                    $start_value = date('Y-m-d H:i:s', strtotime($t['start']));
                    if($t['end'] != "")
                        $end_value = date('Y-m-d H:i:s',strtotime($t['end']));
                    else
                        $end_value = null;
                    if (isset($t['task_description'])) {
                        $description = $t['task_description'];
                    }
                    $date = date("Y-m-d", strtotime($start_value));
                    $diff = 0;
                    if($end_value != null)
                        $diff = strtotime($end_value) - strtotime($start_value);
                    $minutes = round((abs($diff) / 60), 2);
                    $hours = round(abs($diff / (60 * 60)));
                    $array = array('start_time' => $start_value, 'end_time' => $end_value, 'task_description' => $description, 'user_id' => $userid, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $minutes, 'task_date' => $date);
                    if ($table_id != null) {
                        $this->db->where(array('user_id' => $userid, 'task_id' => $data['task_id'], 'id' => $table_id));
                        $query = $this->db->update('time_details', $array);
                    } else {
                        $array = array('user_id' => $userid, 'start_time' => $start_value, 'end_time' => $end_value, 'task_description' => $description, 'user_id' => $userid, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $minutes, 'task_date' => $date);
                        $this->db->set($array);
                        $query = $this->db->insert('time_details', $array);
                    }      
                }
            }
            if (isset($data['deleted_time_range'])) {
                $data['deleted_time_range'] = json_decode($data['deleted_time_range'], true);
                foreach ($data['deleted_time_range'] as $key => $value) {
                    $this->db->where('id', $value);
                    $this->db->where('user_id', $userid);
                    $this->db->where('task_id', $data['task_id']);
                    $this->db->delete('time_details');
                }
            }
            return true;
        } else {
                if (($data['project_module'] == 'Select module') || ($data['project_module'] == '')) {
                    $module_id = 1;
                } else {
                    $module_id = $data['project_module'];
                }
                $this->db->select('*');
                $this->db->from('task AS t');
                $this->db->join('project AS p','t.project_id = p.id');
                $this->db->where('t.task_name', $data['task_name']);
                $this->db->where('p.id', $data['project_id']);
                $task_data = $this->db->get();
                if($task_data->num_rows() <= 0){

                    $array = array('task_name' => $data['task_name'], 'description' => $data['task_desc'], 'project_id' => $data['project_id'], 'module_id' => $module_id, 'created_on' => date('Y:m:d H:i:s'));
                    $this->db->set($array);
                    $query = $this->db->insert('task', $array);
                    if (!$query) {
                        return false;
                    } else {
                        $last_insert_id = $this->db->insert_id();
                        $array = array('user_id' => $userid, 'task_id' => $last_insert_id, 'created_on' => date('Y:m:d H:i:s'));
                        $this->db->set($array);
                        $query = $this->db->insert('task_assignee', $array);
                        if (!$query) {
                            return false;
                        } else {
                            if(isset($data['time_range']))
                            {
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
                                            if($start_time != ''){
                                                $start = $date_value[$i]['date'].' '.date('H:i:s',$start_time);
                                                if($end_itme != '')
                                                    $end = $date_value[$i]['date'].' '.date('H:i:s',$end_itme);
                                                else
                                                    $end = null;
                                            }else{
                                                $start = '0000-00-00 00:00:00';
                                                $end = '0000-00-00 00:00:00';
                                            }
                                            $task_description = "";
                                            if(isset($date_value[$i]['task_description'])){
                                                $task_description = $date_value[$i]['task_description'];
                                            }
                                            $diff = 0;
                                            if($end_itme != '')
                                                $diff = $end_itme - $start_time;
                                            $hours = $diff / ( 60 * 60 );
                                            $minutes = $diff/60; 
                                            $total_mins = ($minutes < 0 ? 0 : abs($minutes));
                                            $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'task_date'=>$date_value[$i]['date'],'start_time'=>$start,'end_time'=>$end,'task_description'=>$task_description,'total_hours'=>$hours,'total_minutes'=>$total_mins,'created_on'=>date('Y:m:d H:i:s'));
                                            $this->db->set($array);
                                            $query = $this->db->insert('time_details',$array);
                                        
                                    }
                                }
                            }
                            return $last_insert_id;
                        }
                    }
                }else{
                    return -1;
                }
        }
    }

    /**
     * Function to convert UTC time to LOCAL time
     *
     * @param $datetime
     *
     * returns converted datetime IF, $datetime exists
     *
     * returns FALSE IF $datetime is empty
     */
    public function convert_date($date)
    {
        if (($this->session->userdata('user_tz') != NULL)) {
            $tz = $this->session->userdata('user_tz'); 
        }
        else
        {
            $tz = 'Asia/Kolkata';
        }
        if ($date && $tz) {
            date_default_timezone_set('UTC'); //set current sytsem to default utc
            $datetime = new DateTime($date);
            $la_time = new DateTimeZone($tz);
            $datetime->setTimezone($la_time);
            return $datetime->format('Y-m-d H:i:s');
        }
        return FALSE;
    }

    /**
     * Function to validate time format
     *
     * @param $input_time
     *
     * returns TRUE/FALSE
     */
    public function validate_time($str){
        if($str == 'Invalid date'){
            return FALSE;
        }
        if (strrchr($str,":")) {
            $dat_time = explode(' ',$str);
            if($dat_time[1])
            {
                list($hh, $mm, $ss) = explode(':', $dat_time[1]);
                if (!is_numeric($hh) || !is_numeric($mm) || !is_numeric($ss)){
                    return FALSE;
                }elseif ((int) $hh > 24 || (int) $mm > 59 || (int) $ss > 59){
                    return FALSE;
                }
                return TRUE;
            }
            else
            {
                return false;
            }
        }else{
            return FALSE;
        }
    }

    /**
     * Function to update user details
     *
     * @params $userid and $data
     *
     * returns TRUE/FALSE
     */
    public function update_user_details($userid,$data){
        $this->db->where(array('id' => $userid));
        $query = $this->db->update('users',$data);
        if ($query) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to get user details
     *
     * @param $userid
     *
     * returns $data IF, user-data exists
     *
     * returns FALSE IF data does not exist
     */
    public function get_user_details($userid){

        $this->db->select('name,phone,profile');
        $this->db->from('users');
        $this->db->where('id',$userid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
            $data['profile_pic'] = base_url().USER_UPLOAD_PATH.$data['profile'];
            return $data;
        }else{
            return false;
        }
    }

    /**
     * Function to get today's running tasks for punchout popup
     *
     * @param void
     *
     * returns TRUE/FALSE
     */
    public function running_tasks_today(){
        $this->db->select('id');
        $this->db->from('time_details');
        $this->db->where('end_time IS NULL');
        $this->db->where('user_id',$this->userid);
        $running_tasks = $this->db->get();
        if($running_tasks->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to check whether user punched out or not
     *
     * @param void
     *
     * returns TRUE/FALSE
     */
    public function check_user_punchout(){
        $this->db->select('id');
        $this->db->from('login_details');
        $this->db->where('end_time IS NOT NULL');
        $this->db->where(array('task_date'=>date('Y-m-d'),'user_id'=>$this->userid));
        $punch_in_check = $this->db->get();
        if($punch_in_check->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to get Activity Chart Data for devices
     * 
     * @params $chart_type,$taskdate,$userid
     * 
     * returns $chart_data
     */
    public function get_activity_device($chart_type,$taskdate,$userid){
        $chart_data = array();
        if($chart_type == "daily"){
            $data = $this->get_daily_activity($userid,$taskdate);
            if(!empty($data)){
                foreach ($data as $d) {
                    $object = new stdClass();
                    //$object->table_id = $d['table_id'];
                    $object->task_id = $d['task_id'];
                    $object->task_name = $d['task_name'];
                    $object->created_on = $d['created_on'];
                    $object->color_code = $d['color_code'];
                    $object->project_id = $d['project_id'];
                    $object->project_name = $d['name'];
                    $object->start_time = $d['start_time'];
                    $object->end_time = $d['end_time'];
                    $object->total_minutes = $d['total_minutes'];
                    $chart_data[] = $object;
                }
                return $chart_data;
            }
        }
        if($chart_type == "weekly"){
            $total_mins=0;
            $hours = array();
            $split_week = explode('~', $taskdate);
            $start_date = isset($split_week[0]) ? date('Y-m-d',strtotime($split_week[0])) : date('Y-m-d');
            $end_date = isset($split_week[1]) ? date('Y-m-d',strtotime($split_week[1])) : date('Y-m-d',strtotime("+1 week"));


            //$date_range = $this->get_dates_from_range($start_date, $end_date); //week days(Sun-Sat) in date format(Y-m-d)

            $data = $this->get_weekly_activity_detail($userid,$start_date,$end_date);
            if(!empty($data)){
                $format_hours = array();
                for($i=0;$i<count($data);$i++) {
                    $object = new stdClass();
                    //$object->table_id = $data[$i]['table_id'];
                    $object->task_id = $data[$i]['task_id'];
                    $object->task_name = $data[$i]['task_name'];
                    $object->created_on = $data[$i]['created_on'];
                    $object->project_id = $data[$i]['project_id'];
                    $object->project_name = $data[$i]['name'];
                    $object->color_code = $data[$i]['color_code'];
                    $object->start_time = $data[$i]['start_time'];
                    $object->end_time = $data[$i]['end_time'];
                    $chart_data[] = $object;
                }    
            }
            return $chart_data;
        }
        if($chart_type == "monthly"){
            $month_array = explode(' ',$taskdate);
            $month_value = $month_array[0];
            $year_value = $month_array[1];
            $first = date($year_value . '-' . $month_value . '-' . '01');
            $last = date($year_value . '-' . $month_value . '-' . 't');
            $data = $this->get_monthly_activity_detail($userid,$first,$last);
            if(!empty($data)){
                foreach ($data as $d) {
                    $object = new stdClass();
                    //$object->table_id = $d['table_id'];
                    $object->task_id = $d['task_id'];
                    $object->task_name = $d['task_name'];
                    $object->created_on = $d['created_on'];
                    $object->project_id = $d['project_id'];
                    $object->project_name = $d['name'];
                    $object->color_code = $d['color_code'];
                    $object->task_date = date('Y-m-d', strtotime($d['task_date']));
                    $object->start_time = $d['start_time'];
                    $object->end_time = $d['end_time'];
                    //$format_hours = sprintf('%02d.%02d',floor($d['t_minutes'] / 60),($d['t_minutes']%60));
                    //$object->hours = $format_hours;
                    $chart_data[] = $object;
                    
                }
            }
            return $chart_data;
        }
    }

    /**
     * Function to get Activity Chart Data for weekly chart
     * 
     * @params $userid and $taskdate
     * 
     * returns $data
     */
    public function get_weekly_activity_detail($userid,$start_date,$end_date){
        $data = array();
        $query = $this->db->query("SELECT t.id AS task_id,t.task_name,t.created_on,p.id as project_id,p.name,p.color_code,d.task_id, d.start_time, d.end_time, d.task_description,d.task_date, SUM(`d`.`total_minutes`) AS `minutes` FROM `task` AS `t` JOIN `project` AS `p` ON `p`.`id` = `t`.`project_id` LEFT JOIN `task_assignee` AS `ta` ON ta.task_id = t.id JOIN `time_details` AS `d` ON d.task_id = t.id WHERE `d`.`task_date` BETWEEN '".$start_date."' AND '".$end_date."' AND d.end_time IS NOT NULL AND d.user_id = '".$userid."' group by d.id");

        if($query->num_rows() > 0){
            $data = $query->result_array();
        }
        return $data;
    }

    /**
     * To validate task-id while loading edit task page
     *
     * Task-id is the id present in the url of loading edit task page
     *
     * @params $task_id and $user_id
     *
     * returns TRUE/FALSE
     */
    public function validate_task_id($task_id,$user_id){
        $task_id_check = $this->db->get_where('task_assignee',array('task_id'=>$task_id,'user_id'=>$user_id));
        if($task_id_check->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to get Activity Chart Data for monthly chart
     * 
     * @params $userid,$start_date,$end_date
     * 
     * returns $data
     */
    public function get_monthly_activity_detail($userid,$start_date,$end_date){
        $data = array();
        $query = $this->db->query("SELECT `d`.`task_date`, `t`.`task_name`,t.created_on,d.task_id,d.task_date,d.start_time, d.end_time,p.name,p.id as project_id, p.color_code,SUM(`d`.`total_minutes`) AS `t_minutes` FROM `time_details` AS `d` join task as t on d.task_id=t.id join project as p on t.project_id=p.id WHERE `d`.`user_id` = ".$userid." AND `d`.`end_time` IS NOT NULL AND `d`.`task_date` BETWEEN '".$start_date."' and '".$end_date."' GROUP BY d.id");
        if ($query->num_rows() > 0) {
                $data = $query->result_array();
        }
        return $data;
    }

    public function login_data($user_data){
        $draw = intval($user_data["draw"]);
        $start = intval($user_data["start"]);
        $length = intval($user_data["length"]);
        $order = $user_data["order"];
        $col = 0;
        $dir = "";
        if(!empty($order))
        {
            foreach($order as $o)
            {
                $col = $o['column'];
                $dir= $o['dir'];
            }
        }

        if($dir != "asc" && $dir != "desc")
        {
            $dir = "desc";
        }
        $valid_columns = array(
            0=>'task_date',
            1=>'start_time',
            2=>'end_time'
        );
        if(!isset($valid_columns[$col]))
        {
            $order = null;
        }
        else
        {
            $order = $valid_columns[$col];
        }
        if($order !=null)
        {
                $this->db->order_by($order, $dir);
        }
        $this->db->select('task_date,start_time,end_time');
        $this->db->from('login_details');
        $this->db->where(array('user_id'=>$this->userid));
        if(!empty($user_data['from']) && !empty($user_data['to'])){
            $this->db->where('task_date BETWEEN "'.$user_data['from'].'" AND "'.$user_data['to'].'"');
        }
        $this->db->limit($length,$start);
        $login_data = $this->db->get();
        if($login_data->num_rows() > 0){
            $total_minutes = 0;
            $login_details = $login_data->result_array();
            $this->load->model('dashboard_model');
            foreach($login_details AS $l_detail){
                if($l_detail['end_time'] != ''){
                    $time_elapsed = strtotime($l_detail['end_time']) - strtotime($l_detail['start_time']);
                    $total_minutes = abs($time_elapsed)/60;
                    $format_time = $this->dashboard_model->format_time($total_minutes);
                }
                $log_data[] = array('login_date'=>$l_detail['task_date'],'login_time'=>$this->convert_date($l_detail['start_time']),'logout_time'=>($l_detail['end_time'])?$this->convert_date($l_detail['end_time']):'--','total_time'=>($total_minutes != 0)?$format_time:'--');
            }
        }
        else{
            $log_data = '';
        }
        return $log_data;
    }

    public function total_data_count($user_data){
        $this->db->select('task_date,start_time,end_time');
        $this->db->from('login_details');
        $this->db->where(array('user_id'=>$this->userid));
        if(!empty($user_data['from']) && !empty($user_data['to'])){
            $this->db->where('task_date BETWEEN "'.$user_data['from'].'" AND "'.$user_data['to'].'"');
        }
        return $this->db->get()->num_rows();

    }

    public function get_tasks_name($userid){
        $this->db->select('t.id,t.task_name');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS ta','ta.task_id = t.id');
        $this->db->where('ta.user_id',$userid);
        $tasks = $this->db->get();
        if($tasks->num_rows() > 0){
            $tasks_list = $tasks->result_array();
        }else{
            $tasks_list = '';
        }
        return $tasks_list;
    }
}
?>
