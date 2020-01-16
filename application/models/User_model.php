<?php
class User_model extends CI_Model {
    public function __construct() {
        //$this->load->library('email');
        $this->load->database();
        $this->load->library('session');
        $userid = $this->session->userdata('userid');
    }

    //To start login timer
    public function start_login_timer(){
        $array = array('user_id'=>$this->session->userdata('userid'),
                        'task_date'=>date('Y-m-d'),
                        'start_time'=>date('Y-m-d H:i:s',strtotime($this->input->post('start-login-time'))),
                        'created_on'=>date('Y-m-d H:i:s')
                    );

        $this->db->where(array('task_date' => date('Y:m:d'),'user_id' => $this->session->userdata('userid')));
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) {
            $result = $query_check->row_array();
            $this->db->where(array('user_id'=>$result['user_id'],'task_date'=>$result['task_date']));
            $query = $this->db->update('login_details', $array);
            if($query){
                return true;
            } else {
                return false;
            }
        }else{
            $this->db->set($array);
            $query = $this->db->insert('login_details', $array);
            if($query){
                return true;
            }else{
                return false;
            }
        }
    }
    //fetch running tasks into user dashboard page
    public function task_status() {
        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where(array('d.user_id' => $userid, 'd.total_minutes' => '0'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $task_status = $query->result_array();
        } else {
            $task_status = '';
        }
        $this->db->where(array('user_id' => $userid, 'task_date' => date('Y:m:d')));
        $query = $this->db->get('login_details');
        if ($query->num_rows() > 0) {
            $login_status = $query->row_array();
            return array('task_status' => $task_status, 'login_status' => $login_status);
        }
    }
    //load all tasks of the user into user dashboard
    public function get_task_details($sort_type, $date) {
        $userid = $this->session->userdata('userid');
        $this->db->select('p.name,d.start_time,p.image_name,t.task_name,d.task_id');
        $this->db->select("SUM(IF(d.total_minutes=0,1,0)) AS running_task", FALSE); //get running tasks of the user
        $this->db->select('IF(t.complete_task=1,1,0) AS completed', FALSE); //get completed tasks of the user
        $this->db->from('task AS t');
        $this->db->join('time_details AS d', 'd.task_id = t.id', 'LEFT');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m', 'm.id = t.module_id');
        if ($date == '') {
            $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
            $this->db->where('d.user_id', $userid);
            $this->db->group_by('d.task_id');
        } else {
            if ($sort_type == 'daily_chart') {
                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where('d.task_date', $date);
                $this->db->where('d.end_time IS NOT NULL');
                $this->db->where('d.user_id', $userid);
                $this->db->group_by('d.id');
                $this->db->order_by('d.start_time');
            } else if ($sort_type == 'weekly_chart') {
                $year_value = explode('-', $date); //format: 2019-W23
                $week_value = $year_value[1]; //W23
                $week = explode('W', $week_value); //format: W23
                $getdate = $this->get_start_and_end_date($week[1], $year_value[0]); //start and end date for 23rd week and year 2019
                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where(array('d.end_time IS NOT NULL', 'd.user_id' => $userid));
                $this->db->where('d.task_date BETWEEN "' . date('Y-m-d', strtotime($getdate[0])) . '" and "' . date('Y-m-d', strtotime($getdate[1])) . '"');
                $this->db->group_by('d.task_date');
                $this->db->order_by('d.start_time');
            } else {
                //for monthly chart
                $year_start = date('Y-m-d', strtotime(date($date . '-01-01')));
                $year_end = date('Y-m-d', strtotime(date($date . '-12-31')));
                $this->db->select_sum('d.total_minutes', 't_minutes'); //get total minutes for a particular task
                $this->db->where(array('d.end_time IS NOT NULL', 'd.user_id' => $userid));
                $this->db->where('d.task_date BETWEEN "' . $year_start . '" and "' . $year_end . '"');
                $this->db->group_by('d.task_date');
                $this->db->order_by('d.start_time');
            }
        }
        if ($sort_type == 'name') {
            $this->db->order_by("t.task_name", "asc"); //sort by task name
            
        } else if ($sort_type == 'date') {
            $this->db->order_by("d.task_date", "asc"); //sort by task date
            
        } else if ($sort_type == 'task') {
            $this->db->order_by("t.id", "asc"); //sort by task id
            
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $dataa = $query->result_array();
            foreach ($dataa as $d) {
                $data[] = array('image_name' => ($d['image_name'] != NULL) ? (base_url() . UPLOAD_PATH . $d['image_name']) : NULL, 'project' => $d['name'], 'task_name' => $d['task_name'], 'running_task' => $d['running_task'], 'completed' => $d['completed'], 'start_time' => $d['start_time'], 't_minutes' => $d['t_minutes'], 'id' => $d['task_id']);
            }
        } else {
            $data = NULL;
        }
        return $data;
    }
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
    public function get_user_task_count($userid) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS a', 'a.task_id = t.id');
        $this->db->where(array('a.user_id' => $userid));
        $query = $this->db->get();
        $task_count = $query->result_array();
        return $task_count[0]['count'];

    }

    //get task details to edit task
    public function get_task_info($id) {
        $userid = $this->session->userdata('userid');
        $taskid = $id;
        $this->db->select('d.id,p.name,p.id AS project_id,d.task_date,t.task_name,d.task_description,d.start_time,d.end_time,t.description,d.task_id');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS ta', 't.id = ta.task_id');
        $this->db->join('time_details AS d', 't.id = d.task_id', 'left');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->where(array('t.id' => $taskid, 'd.user_id' => $userid));
        $this->db->order_by('d.id');
        $query = $this->db->get();
        $data = $query->result_array();
        foreach($data as $d){

            $details[] = array('table_id'=>$d['id'],'task_name'=>$d['task_name'],'name'=>$d['name'],'project_id'=>$d['project_id'],'task_date'=>$d['task_date'],'description'=>$d['description'],'task_id'=>$d['task_id'],'start_time'=>date('H:i:s',strtotime($d['start_time'])),'end_time'=>($d['end_time'])?date('H:i:s',strtotime($d['end_time'])):'','task_description'=>$d['task_description']);
        }
        $this->db->select("SUM(IF(d.total_minutes=0,1,0)) AS running_task", FALSE);
        $this->db->from('time_details AS d');
        $this->db->where(array('d.task_id' => $taskid, 'd.user_id' => $userid));
        $task_status = $this->db->get();
        $status = $task_status->row_array();
        $task_data = array($details, $status);
        return $task_data;
    }
    //running task data to stop.
    public function running_task_data() {
        $this->db->select('d.task_id,d.start_time,t.task_name,t.description');
        //$this->db->select('count(IF(d.total_minutes=0,1,0)) AS running_task_count');
        $this->db->from('time_details AS d');
        $this->db->join('task AS t', 't.id = d.task_id');
        $this->db->where('d.total_minutes','0');
        $data = $this->db->get();
        if($data->num_rows() > 0){
            $tasks = $data->result_array();
        }else{
            $tasks = '';
        }
        return $tasks;
    }
    //Function to Start Timer...
    public function start_timer($data) {
        $start = (isset($data['start_time']))?$data['start_time']:date('Y:m:d H:i:s');
        $array2 = array('task_id' => $data['task_id'], 'user_id' => $data['userid'], 'task_date' => date('Y:m:d'), 'start_time' => $start, 'total_hours' => '0', 'total_minutes' => '0', 'created_on' => date('Y:m:d H:i:s'));
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
    //Function to Stop Timer
    public function stop_timer($req_data) {
        $this->db->select('*');
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
            } else {
                $update_time = $data['task_date'] . " " . date('H:i:s');
            }
            $diff = (strtotime($update_time) - strtotime($data['start_time']));
            $t_minutes = (($diff / 60) < 1) ? ceil(abs($diff / 60)) : abs($diff / 60);
            $hours = round(abs($diff / (60 * 60)));
            $this->db->where(array('id' => $data['id']));
            $query2 = $this->db->update('time_details', array('task_description' => $req_data['task_desc'], 'end_time' => $update_time, 'total_hours' => $hours, 'total_minutes' => $t_minutes, 'modified_on' => date('Y:m:d H:i:s')));
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
    
    //Activity Chart Data
    public function get_activity($chart_type, $date) {
        //get task activities
        $userid = $this->session->userdata('userid');
        $taskdate = $date;
        if ($chart_type == "daily_chart") {
            $this->db->select('*,d.id AS table_id');
            //$this->db->select('*,count(t.task_name) as tasks');
            $this->db->from('time_details AS d');
            $this->db->join('task AS t', 't.id = d.task_id');
            $this->db->where('d.end_time IS NOT NULL'); //tasks that are not running
            $this->db->where(array('d.user_id' => $userid, 'd.task_date' => $taskdate));
            $this->db->group_by('d.id');
            $query = $this->db->get();
            $data = $query->result_array();
            if ($query->num_rows() > 0) {
                $data = $query->result_array();
                foreach ($data as $d) {
                    $table_id[] = $d['table_id'];
                    $task_id[] = $d['task_id'];
                    $task_name[] = $d['task_name'];
                }

                /*$tasks = array_count_values($task_id);
                foreach ($tasks as $key => $count) {
                    if ($count >= 1) {
                        $task_ids[] = $key;
                    }
                }*/
                
                foreach ($data as $t) {
                    $this->db->select('d.start_time,d.end_time,d.total_minutes');
                    $this->db->from('time_details AS d');
                    $this->db->join('task AS t', 't.id = d.task_id');
                    $this->db->where('d.end_time IS NOT NULL'); //tasks that are not running
                    $this->db->where(array('d.user_id' => $userid, 'd.id' => $t['table_id'], 'd.task_date' => $taskdate));
                    $query = $this->db->get();
                    $timing[] = $query->row_array();
                }
                $chart_data = array('daily_chart', "status" => TRUE,
                //"labels"=> $week_days,
                "data" => array($task_id, $timing, $task_name));
            
            } else {
                $chart_data = array('daily_chart', 'status' => FALSE, 'data' => "No activity in this date.");
            }
            return $chart_data;
        }
        if ($chart_type == "weekly_chart") {
            $date = $this->input->get('date');
            $year_value = explode('-', $date); //format: 2019-W23
            $week_value = $year_value[1]; //W23
            $week = explode('W', $week_value); //format: W23
            $getdate = $this->get_start_and_end_date($week[1], $year_value[0]); //start and end date for 23rd week and year 2019
            $this->db->select('*');
            $this->db->select_sum('total_minutes', 'minutes');
            $this->db->from('time_details');
            $this->db->where(array('user_id' => $userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "' . date('Y-m-d', strtotime($getdate[0])) . '" and "' . date('Y-m-d', strtotime($getdate[1])) . '"');
            $this->db->group_by('task_date');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = $query->result_array();
                foreach ($data as $d) {
                    $day = date('D', strtotime($d['task_date']));
                    $minutes = $d['minutes'];
                    $to_hours[] = round(($minutes / 60), 2); //get total_minutes interms of hour;
                    $week_days[] = $day;
                }
                $week = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                $chart_data = array('weekly_chart', "status" => TRUE, "labels" => $week_days, "data" => $to_hours);
                //print_r($chart_data);
                return $chart_data;
            } else {
                $status = "No activity in this week.";
                return $status;
            }
        }
        if ($chart_type == "monthly_chart") {
            $year_start = date('Y-m-d', strtotime(date($taskdate . '-01-01')));
            $year_end = date('Y-m-d', strtotime(date($taskdate . '-12-31')));
            $this->db->select('*');
            $this->db->select_sum('total_minutes', 't_minutes');
            $this->db->from('time_details');
            $this->db->where(array('user_id' => $userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "' . $year_start . '" and "' . $year_end . '"');
            $this->db->group_by('task_date');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = $query->result_array();
                foreach ($data as $d) {
                    $values[] = array(date('Y-m-d', strtotime($d['task_date'])), round(($d['t_minutes'] / 60), 2));
                }
                $chart_data = array('monthy_chart', "status" => TRUE,
                /*date with working hours in standard format*/
                "data" => $values);
            } else {
                $chart_data = array('monthy_chart', "status" => FALSE, "data" => '0');
            }
            return $chart_data;
        }
    }
    //to make time intervals in daily chart
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
    //get start date and end date from the week input
    public function get_start_and_end_date($week, $year) {
        //print_r((new DateTime())->setISODate($year,$week,6)->format('Y-m-d'));
        /*return [
        (date("Y-m-d", strtotime("{$year}-W{$week}-1"))), //start date
        (date("Y-m-d", strtotime("{$year}-W{$week}-6"))) //end date
        ];*/
        return [
        (new DateTime())->setISODate($year, $week, 0)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 6)->format('Y-m-d') //end date
        ];
    }
    //update profile model
    public function submit_profile($picture) {
        $useremail = $this->session->userdata('email');
        $this->db->where('email', $useremail);
        $query = $this->db->update('users', $picture);
        if (!$query) {
            return false;
        } else {
            $this->db->where('email', $useremail);
            $query2 = $this->db->get('users');
            if ($query2->num_rows() > 0) {
                $row = $query2->row();
                $this->session->set_userdata('user_profile', $row->profile);
                return true;
            } else {
                return false;
            }
        }
    }
    //get project name for the add task page
    public function get_project_name() {
        $userid = $this->session->userdata('userid');
        $this->db->select('*');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS ps', 'ps.project_id = p.id');
        $this->db->where(array('ps.user_id' => $userid));
        //$query = $this->db->query("SELECT p.* FROM project AS p JOIN project_assignee AS ps ON p.id=ps.project_id WHERE ps.user_id =".$userid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        } else {
            $result = '';
        }
        return $result;
    }
    //get project module into add task page
    public function get_module_name($project_id) {
        $p_id = $project_id;
        $query = $this->db->query("SELECT * FROM project_module WHERE project_id = {$p_id} OR project_id = 0");
        //
        $result = $query->result();
        return $result;
    }
    public function task_exists() {
        $userid = $this->session->userdata('userid');
        $task_name = $this->input->post('task_name');
        $this->db->select('task_name');
        $this->db->from('task');
        $this->db->where(array('task.task_name' => $task_name, 'task.project_id' => $this->input->post('project')));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('task_exists', 'Task name exists.');
            return true;
        } else {
            return false;
        }
    }
    //add task model
    public function add_tasks($data) {
        //date("H:i", strtotime("1:30 PM"));
        $userid = $data['userid'];
        
        if ($data['action'] == 'edit') {
            if (($data['project_module'] == 'Select module') || ($data['project_module'] == '')) {
                $module_id = 1;
            } else {
                $module_id = $data['project_module'];
            }

            $array = array('task_name' => $data['task_name'], 'description' => $data['task_desc'], 'modified_on' => date('Y:m:d H:i:s'), 'project_id' => $data['project_id'], 'module_id' => $module_id, 'modified_on' => date('Y:m:d H:i:s'));
            $this->db->where(array('id' => $data['task_id']));
            $query = $this->db->update('task', $array);
            $time_range = $data['timings'];
            if (!is_array($time_range)) {
                $time_range = json_decode($time_range, true);
            }
            //print_r($this->input->post());exit;
            if (sizeof($time_range) > 0) {
                foreach($time_range as $t) {
                    
                    $table_id = null;
                    if (isset($t['table_id'])) {
                        $table_id = $t['table_id'];
                    }
                        if(($t['start']) == '' || $t['start'] == null)
                            $start = $t['date'] . ' ' . '00:00:00';
                        else{
                            $start_time = strtotime($t['start']);
                            $start = $t['date'] . ' ' . date('H:i:s', $start_time);
                        }
                        if($t['end'] == '' || ($t['end'] == null) || ($t['end'] == ' ') || empty($t['end'])){
                            $end = null;
                        }
                        else{
                            $end_time = strtotime($t['end']);
                            $end = $t['date'].' '.date('H:i:s',$end_time);
                        }
                        
                    $description = "";
                    if (isset($t['task_description'])) {
                        $description = $t['task_description'];
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
                    $array = array('start_time' => $start, 'end_time' => $end, 'task_description' => $description, 'user_id' => $userid, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $total_mins, 'task_date' => $t['date'], 'modified_on' => date('Y:m:d H:i:s'));
                    $this->db->where(array('user_id' => $userid, 'task_id' => $data['task_id'], 'id' => $table_id));
                    $query = $this->db->update('time_details', $array);      
                }
            }
            if(isset($data['add_timings'])){
                $timings = $data['add_timings'];
                if (!is_array($timings)) {
                    $timings = json_decode($timings, true);
                }
                if (sizeof($timings) > 0) {
                    foreach($timings as $time) {
                        if(($time['start']) == '' || $time['start'] == null)
                            $start = $time['date'] . ' ' . '00:00:00';
                        else{
                            $start_time = strtotime($time['start']);
                            $start = $time['date'] . ' ' . date('H:i:s', $start_time);
                        }
                        if($time['end'] == '' || ($time['end'] == null) || ($time['end'] == ' ') || empty($time['end'])){
                            $end = null;
                        }
                        else{
                            $end_time = strtotime($time['end']);
                            $end = $time['date'].' '.date('H:i:s',$end_time);
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
                        $array = array('user_id' => $userid, 'start_time' => $start, 'end_time' => $end, 'task_description' => $description, 'task_id' => $data['task_id'], 'total_hours' => $hours, 'total_minutes' => $total_mins, 'task_date' => $time['date'], 'created_on' => date('Y:m:d H:i:s'));
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
                        if(sizeof($date_value) >= 1)
                        {
                            for ($i = (sizeof($date_value)-1);$i >= 0;$i--)
                            {
                                if(($date_value[$i]['start']) == '' || ($date_value[$i]['start'] == null))
                                    $start = $date_value[$i]['date'] . ' ' . '00:00:00';
                                else{
                                    $start_time = strtotime($date_value[$i]['start']);
                                    $start = $date_value[$i]['date'] . ' ' . date('H:i:s', $start_time);
                                    if($date_value[$i]['end'] == '' || ($date_value[$i]['end'] == null) || ($date_value[$i]['end'] == ' ') || empty($date_value[$i]['end'])){
                                        $end = null;
                                    }
                                    else{
                                        $end_time = strtotime($date_value[$i]['end']);
                                        $end = $date_value[$i]['date'].' '.date('H:i:s',$end_time);
                                    }
                                }
                                /*$start_time = strtotime($date_value[$i]['start']);
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
                                }*/
                                $task_description = "";
                                if (isset($date_value[$i]['task_description'])) {
                                    $task_description = $date_value[$i]['task_description'];
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
                                $array = array('user_id'=>$userid,'task_id'=>$last_insert_id,'task_date'=>$date_value[$i]['date'],'start_time'=>$start,'end_time'=>$end,'task_description'=>$task_description,'total_hours'=>$hours,'total_minutes'=>$total_mins,'created_on'=>date('Y:m:d H:i:s'));
                                $this->db->set($array);
                                $query = $this->db->insert('time_details',$array);      
                            }
                        }
                    }
                    return $last_insert_id;
                }
            }
            
        }
    }
    //Check for the Old Password
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
    //function to change password
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
    //fetch user profile data to profile page
    public function my_profile($userid) {
        $this->db->select('*');
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
            $final = array('name' => $u['name'], 'email' => $u['email'], 'phone' => $u['phone'], 'profile' => $u['profile'], 'total_time' => round(($u['total_time'] / 60), 2), 't_minutes' => round(($time['t_minutes'] / 60), 2));
        } else {
            $final = '';
        }
        return $final;
    }

    //get chart data for user profile
    public function user_chart_data($year) {
        $userid = $this->session->userdata('userid');
        $year_start = date('Y-1-1',strtotime(date($year.'-01-01')));
        $year_end = date('Y-12-t',strtotime($year.'-12-31'));
        $this->db->select('*');
        $this->db->select_sum('total_minutes', 't_minutes');
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
            $start = date('Y-m-d', strtotime(date($month . '-01')));
            $end = date('Y-m-d', strtotime(date($month . '-31')));
            $this->db->select('*');
            $this->db->select_sum('total_minutes', 't_minutes');
            $this->db->from('time_details');
            $this->db->where(array('user_id' => $userid));
            $this->db->where('end_time IS NOT NULL');
            $this->db->where('task_date BETWEEN "' . $start . '" and "' . $end . '"');
            $query = $this->db->get();
            $data = $query->result_array();
            foreach ($data as $d) {
                $values[] = round(($d['t_minutes'] / 60), 2);
            }
        }
        return $values;
          
    }
    public function update_logout_time($userid) {
        //check for entry with the same login date
        $this->db->where(array('task_date' => date('Y:m:d'), 'user_id' => $userid, 'end_time IS NULL'));
        $this->db->order_by("id", "desc");
        $query_check = $this->db->get('login_details');
        if ($query_check->num_rows() > 0) {
            $data = $query_check->row_array();
            $array = array('end_time' => date('Y:m:d H:i:s'), 'modified_on' => date('Y:m:d H:i:s'));
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
        }
        return $data;
    }
    public function get_default_module() {
        $this->db->select('id,name');
        $this->db->from('project_module');
        $this->db->where(array('project_id' => 0));
        $query = $this->db->get();
        $default_mod = $query->result_array();
        return $default_mod;
    }
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
    public function total_login_details($userid) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('login_details');
        $this->db->where(array('user_id' => $userid));
        $query = $this->db->get();
        $login_count = $query->result_array();
        return $login_count[0]['count'];
    }
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
    public function check_email($email) {
        $query = $this->db->get_where('users', array('email' => $email));
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }

    }
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
}
?>
