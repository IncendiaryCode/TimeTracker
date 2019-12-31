<?php
class Dashboard_model extends CI_Model
{
    public function __construct()
    {
        
        $this->load->database();
    }
    //Dashboard model
    public function get_users()
    {
        $this->db->where('type','user');
        $get_users_q = $this->db->get('users');
        $row         = $get_users_q->num_rows();
        return $row;
    }
    public function get_tasks()
    {
        $get_task_q = $this->db->query("SELECT * FROM task");
        $row_task   = $get_task_q->num_rows();
        return $row_task;
    }
    public function get_projects()
    {
        $get_proj_q = $this->db->query("SELECT * FROM project");
        $row_proj   = $get_proj_q->num_rows();
        return $row_proj;
    }

    public function get_top_users(){
        $this->db->select('u.id AS user_id,u.name AS user_name');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('task AS t');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $this->db->join('users AS u','u.id = d.user_id');
        $this->db->group_by('d.user_id');
        $this->db->order_by('d.total_minutes','desc');
        $query = $this->db->get();
        if($query->num_rows() <= 5){
            $users = $query->result_array();
        }else if($query->num_rows() > 5){
            $user = $query->result_array();
            for($i=0;$i<5;$i++){
                $users[] = $user[$i];
            }
        }
        else{
            $users = '';
        }
        return array($query->num_rows(),$users);
    }

    public function get_top_projects(){
        $this->db->select('p.id AS project_id,p.name AS project_name,p.image_name');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $this->db->join('users AS u','u.id = d.user_id');
        $this->db->group_by('p.id');
        $this->db->order_by('d.total_minutes','desc');
        $query = $this->db->get();
        if($query->num_rows() <= 5){
            $users = $query->result_array();
        }else if($query->num_rows() > 5){
            $user = $query->result_array();
            for($i=0;$i<5;$i++){
                $users[] = $user[$i];
            }
        }
        else{
            $users = '';
        }
        return array($query->num_rows(),$users);
    }

    public function dashboard_graph(){
        $month = date('Y-m-d',strtotime($this->input->post('month')));
        $end = date('Y-m-t',strtotime($month));
        $days = array();
        for($i=strtotime($month); $i<=strtotime($end); $i+=86400)
        {
           $days[] = date('Y-m-d', $i);
        }
        $this->db->distinct()->select('a.project_id,p.name AS project_name,p.color_code');
        $this->db->from('project AS p');
        $this->db->join('project_assignee AS a','a.project_id = p.id');
        $this->db->join('task AS t','t.project_id = a.project_id');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $projects = $this->db->get()->result_array();
        $i=0;
        foreach($projects as $p){
            foreach($days as $d){
                $this->db->select_sum('d.total_minutes','t_minutes');
                $this->db->select('IF(d.task_date="'.$d.'",1,0) AS work_date');
                $this->db->from('project AS p');
                $this->db->join('task AS t','t.project_id = p.id');
                $this->db->join('time_details AS d','d.task_id = t.id');
                //$this->db->where('d.task_date BETWEEN "'.$month.'" AND "'.$end.'"');
                $this->db->where(array('p.id'=>$p['project_id'],'d.task_date'=>$d));
                //$this->db->group_by('p.id');
                $query = $this->db->get()->result_array();
                foreach($query as $q){
                    if($q['work_date'] == '1'){
                        $array[$i][] = round(($q['t_minutes']/60),2);
                    }else{
                        $array[$i][] = '0';
                    }
                }
            }
            $data[$i] = array('label'=>$p['project_name'],
                              'backgroundColor'=>(!empty($p['color_code'])) ? $p['color_code'] : '#5a5761',
                              'borderColor'=>(!empty($p['color_code'])) ? $p['color_code'] : '#5a5761',
                              'fill'=>'false',
                              'data'=>$array[$i]
                        );
            $i= $i+1;
        }

        //to send dates in number format
        foreach($days as $day){
            $labels_array = explode('-',$day);
            $labels[] = $labels_array[2];
        }

        $final = array('labels'=>$labels,'datasets'=>$data);
        return $final;
    }

    public function get_task_details($type){

        if($type == 'user'){

            $this->db->select('u.name AS user_name,u.id AS user_id');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('users AS u');
            $this->db->join('time_details AS d','d.user_id = u.id');
            $this->db->where('u.type','user');
            $this->db->group_by('d.user_id');
            $user_names = $this->db->get()->result_array();

            foreach ($user_names as $u) {
                $this->db->select('count(distinct t.id) AS tasks_count');
                $this->db->from('task AS t');
                $this->db->join('time_details AS d','d.task_id = t.id');
                $this->db->where('d.user_id',$u['user_id']);
                $task_count = $this->db->get()->row_array();
                $this->db->select('p.id AS project_id,p.name AS project_name,p.image_name,p.color_code');
                $this->db->from('project AS p');
                $this->db->join('project_assignee AS a','a.project_id = p.id');
                $this->db->where(array('a.user_id'=>$u['user_id']));
                $query = $this->db->get()->result_array();    
                    $details[$u['user_id']] = array(
                        'user_id'=>$u['user_id'],
                        'user_name'=> $u['user_name'],
                        'project'=>$query,
                        'total_minutes'=>$u['t_minutes'],
                        'tasks_count'=>$task_count['tasks_count']
                    );
            }
            return $details;
        }

        else if($type == 'project'){

            $this->db->select('id AS project_id,name AS project_name,image_name,color_code');
            $this->db->from('project');
            $projects = $this->db->get()->result_array();

            foreach ($projects as $project) {
                $this->db->select('count(distinct a.user_id) AS user_count');
                $this->db->select_sum('d.total_minutes','t_minutes');
                $this->db->from('project_assignee AS a');
                $this->db->join('task AS t','t.project_id = a.project_id');
                $this->db->join('time_details AS d','d.task_id = t.id');
                $this->db->where('t.project_id',$project['project_id']);
                $proj_time = $this->db->get()->result_array();

                foreach($proj_time as $p){
                    //get each prject data and assign to main array
                    $this->db->select('u.name AS user_name,u.id AS user_id');
                    $this->db->distinct()->select('u.id');
                    $this->db->from('task AS t');
                    $this->db->join('project_assignee AS a','a.project_id = t.project_id');
                    $this->db->join('users AS u','u.id = a.user_id');
                    $this->db->where('a.project_id',$project['project_id']);
                    $user_details = $this->db->get()->result_array();

                    $final_result[$project['project_id']] = array('project_id'=>$project['project_id'],'project_name'=>$project['project_name'],'project_icon'=>$project['image_name'],'project_color'=>$project['color_code'],'time_used'=>$p['t_minutes'], 'total_users'=>$p['user_count'], 'user_details' =>$user_details );
                }
            }

            return $final_result;
               
        } else if($type == 'task') {
            $get_data = $this->input->get();
           // print_r($get_data);
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search= $this->input->get("search");
            $search = $search['value'];
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
                0=>'t.task_name',
                1=>'t.description',
                2=>'p.name',
                3=>'d.start_time',
                4=>'d.end_time',
                5=>'d.total_minutes',
            );
            //print_r($valid_columns);
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
            
            if(!empty($search))
            {
                $x=0;
                foreach($valid_columns as $sterm)
                {
                    if($x==0)
                    {
                        $this->db->like($sterm,$search);
                    }
                    else
                    {
                        $this->db->or_like($sterm,$search);
                    }
                    $x++;
                }                 
            }
            $this->db->select('t.id AS task_id,t.task_name,p.name AS project_name,p.id AS project_id');
            $this->db->select('t.description,d.start_time,d.end_time,d.total_minutes,d.total_hours');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('task AS t');
            $this->db->join('project AS p','p.id = t.project_id');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->where(array('d.end_time IS NOT NULL'));
            $this->db->group_by('d.task_id');
            $this->db->limit($length,$start);
            $employees = $this->db->get();
            $data = array();
            foreach($employees->result() as $rows)
            {

                $data[]= array(
                    $rows->task_name,
                    $rows->description,
                    $rows->project_name,
                    $rows->start_time,
                    $rows->end_time,
                    $rows->t_minutes
                );     
            }
            return $data;
        }
    }

    //get task details for user snapshot graph
    public function user_graph_data(){
        if($this->input->post('month')){
            if(!empty($this->input->post('month'))){
                $month = $this->input->post('month');
                $start_date = date($month.'-01');
                $end_date = date('Y-m-t',strtotime($month));
                $this->db->select('d.task_date,count(distinct t.id) AS tasks_count');
                $this->db->from('task AS t');
                $this->db->join('time_details AS d','d.task_id = t.id');
                $this->db->where('d.task_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
                $this->db->group_by('d.task_date');
                $data = $this->db->get()->result_array();
            }else{
                $data = '';
            }
        }else if($this->input->post('project_name')){
            if(!empty($this->input->post('project_name'))){
                $project_name = $this->input->post('project_name');
                $get_project_id = $this->db->get_where('project',array('name'=>$project_name));
                $project_id = $get_project_id->row_array()['id'];    
            }else{
                $project_id = '';
            }
            //get usernames assigned to projects
            $this->db->distinct()->select('u.name,u.id');
            $this->db->from('project_assignee AS a');
            $this->db->join('users AS u','u.id = a.user_id');
            if($project_id != ''){
                $this->db->where('a.project_id',$project_id);
            }
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $users = $query->result_array();
                //get task names under those usernames
                foreach($users as $u){
                    $this->db->select('d.total_minutes');
                    $this->db->select_sum('d.total_minutes','t_minutes');   //get total minutes for a particular task
                    $this->db->from('task AS t');
                    $this->db->join('time_details AS d','d.task_id = t.id');
                    $this->db->where(array('d.user_id'=>$u['id']));
                    $this->db->group_by('d.user_id');
                    $tasks = $this->db->get()->result_array();       
                    foreach($tasks as $t){
                        $data[] = array('user_name'=>$u['name'],'time_used'=>$t['t_minutes']); //total minutes for each task
                    }
                }  
            }else{
                $data = '';
            }
        }else{
            $this->db->select('id,name');
            $this->db->from('project');
            $projects = $this->db->get()->result_array();
            $this->db->select('p.name AS project_name,u.id AS user_id,u.name AS user_name');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('project AS p');
            $this->db->join('project_assignee AS a','a.project_id = p.id');
            $this->db->join('time_details AS d','d.user_id = a.user_id');
            $this->db->join('users AS u','u.id = d.user_id');
            $this->db->group_by('u.id');
            $details = $this->db->get();
        
            if($details->num_rows() > 0){
                $project_data = $details->result_array();
                foreach ($project_data as $d) {
                    $data[] = array('user_name'=>$d['user_name'],'time_used'=>$d['t_minutes']);
                }
            }
            else{
                $data = '';
            }   
        }

        return $data;
    }

    //get user chart data (onclick Username)
    public function get_user_chart($type){
        
        if($type == 'user-chart'){
            $user_id = $this->input->post('user_id');
            $date = $this->input->post('date');
            $start = date('Y-m-d',strtotime($date.'-30 days'));

            $this->db->select('d.task_date,count(distinct t.id) AS tasks_count');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('task AS t');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->where('d.task_date BETWEEN "'.$start.'" AND "'.$date.'"');
            $this->db->group_by('d.task_date');
            $this->db->where('d.user_id',$user_id);

        }else if($type == 'project_chart'){
            $project_id = $this->input->post('project_id');
            $project_start_date = $this->db->get_where('project',array('id'=>$project_id));
            $start_date = $project_start_date->row_array()['created_on'];
            $start = date('Y-m-d',strtotime($start_date));

            $this->db->select('d.task_date');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('task AS t');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->join('project AS p','p.id = t.project_id');
            $this->db->where('d.task_date BETWEEN "'.$start.'" AND "'.date('Y-m-d').'"');
            $this->db->where('p.id',$project_id);
            $this->db->group_by('d.task_date');
        }
        
        $result = $this->db->get();
        if($result->num_rows() > 0){
            $data = $result->result_array();
        }else{
            $data = '';
        }
        return $data;
    }

    public function user_task_data($table_type){
        if($table_type == 'user_task'){
            $user_id = $this->input->get('user_id');
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search= $this->input->get("search");
            $search = $search['value'];
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
                0=>'t.task_name',
                1=>'p.name',
                2=>'d.total_minutes'
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
            
            if(!empty($search))
            {
                $x=0;
                foreach($valid_columns as $sterm)
                {
                    if($x==0)
                    {
                        $this->db->like($sterm,$search);
                    }
                    else
                    {
                        $this->db->or_like($sterm,$search);
                    }
                    $x++;
                }                 
            }
            $this->db->select('t.task_name,p.name AS project_name');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('task as t');
            $this->db->join('project as p','p.id = t.project_id');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->where('d.user_id',$user_id);
            $this->db->group_by('t.id');
            $this->db->limit($length,$start);
            $employees = $this->db->get();
            $data = array();
            foreach($employees->result() as $rows)
            {

                $data[]= array(
                    $rows->task_name,
                    $rows->project_name,
                    $rows->t_minutes
                );     
            }

        }else if($table_type == 'project_task'){
            $project_id = $this->input->get('project_id');
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search= $this->input->get("search");
            $search = $search['value'];
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
                0=>'t.task_name',
                1=>'users_count',
                2=>'d.total_minutes'
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
            
            if(!empty($search))
            {
                $x=0;
                foreach($valid_columns as $sterm)
                {
                    if($x==0)
                    {
                        $this->db->like($sterm,$search);
                    }
                    else
                    {
                        $this->db->or_like($sterm,$search);
                    }
                    $x++;
                }                 
            }
            $this->db->select('t.task_name,count(distinct d.user_id) AS users_count');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('task AS t');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->where('t.project_id',$project_id);
            $this->db->group_by('t.id');
            $this->db->limit($length,$start);
            $employees = $this->db->get();
            $data = array();

            foreach($employees->result() as $rows)
            {

                $data[]= array(
                    $rows->task_name,
                    $rows->users_count,
                    $rows->t_minutes
                );     
            }
        }
        return $data;
    }

    public function user_project_data($table_type){
        if($table_type == 'user_project'){
            $user_id = $this->input->get('user_id');
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search= $this->input->get("search");
            $search = $search['value'];
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
                0=>'p.name',
                1=>'tasks_count',
                2=>'d.total_minutes'
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
            
            if(!empty($search))
            {
                $x=0;
                foreach($valid_columns as $sterm)
                {
                    if($x==0)
                    {
                        $this->db->like($sterm,$search);
                    }
                    else
                    {
                        $this->db->or_like($sterm,$search);
                    }
                    $x++;
                }                 
            }
            $this->db->select('p.name AS project_name');
            $this->db->select('count(distinct t.id) AS tasks_count');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('project AS p');
            $this->db->join('task AS t','t.project_id = p.id');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->group_by('p.id');
            $this->db->where('d.user_id',$user_id);
            $this->db->limit($length,$start);
            $employees = $this->db->get();
            $data = array();

            foreach($employees->result() as $rows)
            {

                $data[]= array(
                    $rows->project_name,
                    $rows->tasks_count,
                    $rows->t_minutes
                );     
            }

        }else if($table_type == 'project_user'){
            $project_id = $this->input->get('project_id');
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $order = $this->input->get("order");
            $search= $this->input->get("search");
            $search = $search['value'];
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
                0=>'u.name',
                1=>'tasks_count',
                2=>'d.total_minutes'
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
            
            if(!empty($search))
            {
                $x=0;
                foreach($valid_columns as $sterm)
                {
                    if($x==0)
                    {
                        $this->db->like($sterm,$search);
                    }
                    else
                    {
                        $this->db->or_like($sterm,$search);
                    }
                    $x++;
                }                 
            }
            $this->db->select('u.name AS user_name');
            $this->db->select('count(distinct d.task_id) AS tasks_count');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('project AS p');
            $this->db->join('project_assignee AS a','a.project_id = p.id');
            $this->db->join('time_details AS d','d.user_id = a.user_id');
            $this->db->join('users AS u','u.id = d.user_id');
            $this->db->where(array('u.type'=>'user','p.id'=>$project_id));
            $this->db->group_by('d.user_id');
            $this->db->limit($length,$start);
            $employees = $this->db->get();
            $data = array();

            foreach($employees->result() as $rows)
            {

                $data[]= array(
                    $rows->user_name,
                    $rows->tasks_count,
                    $rows->t_minutes
                );     
            }  
        }
        return $data;
    }

    public function get_project_data($proj_id){
        $this->db->select('p.name AS project_name,p.image_name,p.id AS project_id');
        $this->db->select('count(distinct t.id) AS tasks_count,count(distinct d.user_id) AS users_count');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $this->db->group_by('p.id');
        $this->db->where('p.id',$proj_id);
        $result = $this->db->get();
        if($result->num_rows() > 0){
            $data = $result->result_array();
        }else{
            $data = '';
        }
        return $data;
    }

    public function assign_user($user_id,$project_id){
        $this->db->where(array('user_id'=>$user_id,'project_id'=>$project_id));
        $proj_assigned = $this->db->get('project_assignee');
        if($proj_assigned->num_rows()>0){
            return false;
        }else{
            $array = array('project_id'=>$project_id,'user_id'=>$user_id,'created_on'=>date('Y-m-d H:i:s'));
            $this->db->set($array);
            $assign = $this->db->insert('project_assignee',$array);
            if($assign){
                return true;
            }else{
                return false;
            }
        }
    }
    //get user info
    public function get_user_data(){
        $user_id = $this->input->get('user_id');
        $this->db->select('u.id,u.name AS user_name,u.email,u.profile,u.phone');
        $this->db->select('count(distinct d.task_id) AS tasks_count');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->select('count(distinct p.id) AS project_count');
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $this->db->join('users AS u','u.id = d.user_id');
        $this->db->where('u.id',$user_id);
        $user_data = $this->db->get()->row_array();
        return $user_data;
    }
    //add user model
    public function add_users()
    {
        $array1 = array(
            'name' => $this->input->post('task_name'),
            'email' => $this->input->post('user_email'),
            'password' => $this->input->post('task_pass'),
            'phone' => $this->input->post('contact'),
            'type' => 'user',
            'created_on' => date('Y:m:d H:i:s')
        );
        $this->db->set($array1);
        $query          = $this->db->insert('users', $array1);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function users_exists()
    {
        $this->db->where('email', $this->input->post('user_email'));
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('users_exists', 'User Already Exists.');
            return false;
        }
        else {
            return true;
        }
    }

    public function add_project_module(){
        
        $array = array('project_id'=>$this->input->post(''),'name'=>$this->input->post(''),'meta_data'=>$this->input->post(''),'created_on'=>date('Y-m-d H:i:s'));
        $this->db->set($array);
        $query = $this->db->insert('project_module',$array);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function get_project_name()
    {
        $query  = $this->db->query("SELECT id FROM project");
        $result = $query->result_array();
        foreach($result as $r){
            $this->db->select('p.name AS project_name,p.color_code,p.image_name,p.id');
            $this->db->select_sum('d.total_minutes','t_minutes');   //get total minutes for a particular task
            $this->db->from('project AS p');
            $this->db->join('task AS t','t.project_id = p.id');
            $this->db->join('time_details AS d','d.task_id = t.id');
            $this->db->where(array('t.project_id'=>$r['id']));
            $projects = $this->db->get();
            if($projects->num_rows() > 0){
                $tasks = $projects->result_array();
                foreach($tasks as $t){
                    $project_data[] = $t;
                }
            }else{
                $project_data = false;
            }
        }
        return $project_data;
    }

    public function get_module_name($project_id){
        $p_id = $project_id;
        $query = $this->db->query("SELECT * FROM project_module WHERE project_id = {$p_id} OR project_id = 0");
        $result = $query->result();
        return $result;
    }

    public function get_usernames(){
        $this->db->select('name,id');
        $this->db->from('users');
        $this->db->where('type','user');
        $query = $this->db->get();
        $names = $query->result_array();
        return $names;
    }
    //add task model
    public function assign_tasks()
    {
        $select = $this->input->post('user-name');
        if(!empty($this->input->post('module'))){
            $module_id = $this->input->post('module');
        }
        else{
            $module_id = 1;
        }
        $array   = array(
                    'task_name' => $this->input->post('task_name'),
                    'description' => $this->input->post('description'),
                    'project_id' => $this->input->post('chooseProject'),
                    'module_id' => $module_id,
                    'created_on' => date('Y-m-d H:i:s')
                );
        $this->db->set($array);
        $query = $this->db->insert('task', $array);
        $project_id = $this->db->insert_id();
        if(sizeof($select) > 0){
            for($i=0;$i<sizeof($select);$i++){
                $query  = $this->db->get_where('users', array(
                    'name' => $select[$i]['name']
                ));
                $user_id[$i] = $query->row_array();
                $array  = array(
                        'user_id' => $user_id[$i]['id'],
                        'task_id' => $project_id,
                        'created_on' => date('Y:m:d H:i:s')
                );
                $this->db->set($array);
                $query = $this->db->insert('task_assignee', $array);
            }
        }
        return $project_id;
    }

    //add project model
    public function project_exists()
    {
        $this->db->where('name', $this->input->post('project-name'));
        $query = $this->db->get('project');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('project_exists', 'Project Name Already Exists.');
            return false;
        } //end of if($query->num_rows() > 0)
        else {
            return true;
        }
    }

    public function add_projects($project_icon)
    {
        $userid = $this->session->userdata('userid');
        if(!empty($this->input->post('start-date'))){
            $project_started_on = $this->input->post('start-date');
        }else{
            $project_started_on = date('Y-m-d H:i:s');
        }
        $file_name = isset($project_icon) ? $project_icon : '';
        if($this->input->post('new-module[0][module]')==''){
            $module = '';
        }else{
            $module = $this->input->post('new-module');
        }
        if($this->input->post('assign-name[0][name]') == 'Select User'){
            $users = '';
        }else{
            $users = $this->input->post('assign-name');
        }

        //check if the project is assigning to the user
        if($this->input->post('project_name') == ''){
            //add project into project table
            $array = array(                                 
                'color_code' => $this->input->post('project-color'),
                'image_name' => $file_name['image_name'],
                'name' => $this->input->post('project-name'),
                'created_on' => $project_started_on
            );
            $this->db->set($array);
            $query = $this->db->insert('project', $array);
            $project_id = $this->db->insert_id(); //get last insert id of project table
        }
        else {
            $query = $this->db->get_where('project',array('name'=>$this->input->post('project_name')));
            $project_id = $query->row_array()['id'];
        }

        if($module != ''){   //if module name is entered store 'module' into project_module table
            for($i=0;$i<sizeof($module);$i++) {
                if(!empty($module[$i]['module'])){
                   $array = array('project_id'=>$project_id,'name'=>$module[$i]['module']);
                    $this->db->set($array);
                    $query = $this->db->insert('project_module', $array);
                }
            }
        }
        
        if($users != ''){    //if User name is entered, store the details into project_assignee table
            for($i=0;$i<sizeof($users);$i++){
                $query  = $this->db->get_where('users', array('name' => $users[$i]['name']));
                $user_id[$i] = $query->row_array();
                $array = array('project_id'=>$project_id,'user_id'=>$user_id[$i]['id'],'created_on'=>date('Y-m-d H:i:s'));
                $this->db->set($array);
                $assignee_query = $this->db->insert('project_assignee',$array);
            }
        }
        return true;
    }

    public function delete_user($userid){
        $array = array('time_details','login_details','project_assignee','task_assignee');
        $delete = $this->db->delete($array,array('user_id'=>$usetid));
        $this->db->where('id',$userid);
        $result = $this->db->delete('users');
        if($result->_error_message()){
            return false;
        }else if(!($this->db->affected_rows())){
            return false;
        }else{
            return true;
        }  
    }

    public function delete_project($task_id){
        $array = array('task_assignee','time_details');
        $delete = $this->db->delete($array,array('task_id'=>$task_id));
        $this->db->where('id',$task_id);
        $this->db->delete('task');
        if(!($this->db->affected_rows())){
            return false;
        }else{
            return true;
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

    //update profile model
    public function submit_profile($picture)
    {
        $useremail = $this->session->userdata('email');
        $this->db->where('email', $useremail);
        $query = $this->db->update('users', $picture);
        if (!$query) {
            return false;
        } //!$query
        else {
            $this->db->where('email', $useremail);
            $query2 = $this->db->get('users');
            if ($query2->num_rows() > 0) {
                $user_profile = $query2->row();
                $this->session->set_userdata('user_profile', $user_profile->profile);
                return true;
            } //end of if($query2->num_rows() > 0)
            else {
                return false;
            }
        }
    }

    public function password_exists()
    {
        $email = $this->session->userdata('email');
        $query = $this->db->get_where('users', array(
            'email' => $email,
            'password' => md5($this->input->post('old-pass'))
        ));
        if ($query->num_rows() == 1) {
            return true;
        }
        else {
            $this->form_validation->set_message('password_exists', 'Please enter your old password properly.');
            return false;
        }
    }
    //function to change password
    public function change_password()
    {
        $new_pwd     = $this->input->post('new-pass');
        $confirm_pwd = $this->input->post('confirm-pass');
        if ($new_pwd == $confirm_pwd) {
            if ($this->session->userdata('email')) {
                $email = $this->session->userdata('email');
            }
            else {
                $email = $this->input->post('mail');
            }
            $this->db->set('password', md5($new_pwd));
            $this->db->where('email', $email);
            $query = $this->db->update('users');
            if ($query) {
                return true;
            }
            else {
                return false;
            }
        } //end of if($new_pwd == $confirm_pwd)
        else {
            $this->session->set_flashdata('err_msg', 'Passwords do not match..');
            return false;
        }
    }

    public function login_device($post)
    {
        $username = $post['username'];//$this->input->post('username');
        $password = $post['password'];//$this->input->post('password');
        $query    = $this->db->get_where('users', array(
            'email' => $username,
            'password' => md5($password)
        ));
        if ($query->num_rows() == 1) {
            $row    = $query->row();
                //check for entry with the same login date
                $this->db->where(array(
                    'task_date' => date('Y:m:d'),
                    'user_id' => $row->id
                ));
                $query_check = $this->db->get('login_details');
                if ($query_check->num_rows() > 0) { //multiple logins on the same date
                    $login_data = $query_check->row_array();
                    /*$data       = array(
                        'userid' => $row->id,
                        'email' => $row->email,
                        'logged_in' => TRUE,
                        'user_profile' => $row->profile,
                        'username' => $row->name,
                        'login_time' => $login_data['end_time']
                    );*/
                }
                else { //first login for the day
                    $array = array(
                        'user_id' => $row->id,
                        'task_date' => date('Y:m:d'),
                        'start_time' => date("Y:m:d H:i:s"),
                        'created_on' => date('Y:m:d H:i:s')
                    );
                    $this->db->set($array);
                    $query = $this->db->insert('login_details', $array);
                    /*$data  = array(
                        'userid' => $row->id,
                        'email' => $row->email,
                        'logged_in' => TRUE,
                        'user_profile' => $row->profile,
                        'username' => $row->name,
                        'login_time' => date('Y:m:d H:i:s')
                    );*/
                    //$this->session->set_userdata($data);    
                }
                $data          = array();
                $data['id']    = $row->id;
                $data['type']  = $row->type;
                $data['email'] = $row->email;
                $data['username'] = $row->name;
                $data['profile_pic'] = $row->profile;
                $data['login_time'] = date('Y:m:d H:i:s');
                return $data;
            }
            else {
                //return 'Wrong inputs.';
                return false;
            }
        }
        
    //To login
    public function login_process()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $query    = $this->db->get_where('users', array(
            'email' => $username,
            'password' => md5($password)
        ));
        if ($query->num_rows() == 1) {
            $row    = $query->row();
                //check for entry with the same login date
                $this->db->where(array(
                    'task_date' => date('Y:m:d'),
                    'user_id' => $row->id
                ));
                $query_check = $this->db->get('login_details');
                if ($query_check->num_rows() > 0) { //multiple logins on the same date
                    $login_data = $query_check->row_array();
                    $data       = array(
                        'userid' => $row->id,
                        'email' => $row->email,
                        'logged_in' => TRUE,
                        'user_profile' => $row->profile,
                        'username' => $row->name,
                        'login_time' => $login_data['end_time']
                    );
                    $this->session->set_userdata($data);
                } //$query_check->num_rows() > 0
                else { //first login for the day
                    $array = array(
                        'user_id' => $row->id,
                        'task_date' => date('Y:m:d'),
                        'start_time' => date("Y:m:d H:i:s"),
                        'created_on' => date('Y:m:d H:i:s')
                    );
                    $this->db->set($array);
                    $query = $this->db->insert('login_details', $array);
                    $data  = array(
                        'userid' => $row->id,
                        'email' => $row->email,
                        'logged_in' => TRUE,
                        'user_profile' => $row->profile,
                        'username' => $row->name,
                        'login_time' => date('Y:m:d H:i:s')
                    );
                    $this->session->set_userdata($data);
                }
                return $row->type;
            }
        else {
            $this->form_validation->set_message('Wrong inputs.');
            return false;
        }
    }

    public function send_otp()
    {
        $email = $this->security->xss_clean($this->input->post('email'));
        if (empty($email)) {
            $this->session->set_flashdata('err_msg', 'Please enter Email.');
        } //empty($email)
        else {
            $query = $this->db->get_where('users', array(
                'email' => $email
            ));
            if ($query->num_rows() == 1) {
                $token = substr(mt_rand(), 0, 6);
                $this->db->set('reset_token', $token);
                $this->db->where('email', $email);
                $query = $this->db->update('users');
                if ($query) {
                    $this->load->library('email');
                    //send OTP through mail
                    $to = $email;
                    $this->email->initialize(array(
                    'protocol' => PROTOCOL,
                    'smtp_host' => SMTP_HOST,
                    'smtp_user' => SMTP_USER,
                    'smtp_pass' => SMTP_PASS,
                    'smtp_port' => SMTP_PORT,
                    'charset' => CHARSET,
                    'crlf' => CRLF,
                    'newline' => NEWLINE,
                    'mailtype' => MAILTYPE,
                    'validation' => TRUE
                ));
                    $this->email->from('admin@printgreener.com');
                    $this->email->to($email);
                    $this->email->subject('OTP for login');
                    $this->email->message('Use this OTP:'.$token);
                    //$this->email->send();
                    if(!$this->email->send()){
                        print_r($this->email->print_debugger());
                        return false;
                    }else{
                        return true;
                    }
                    return true;
                } //$query
                else {
                    return false;
                }
            } //$query->num_rows() == 1
            else {
                return false;
            }
        }
    }

    public function check_otp()
    {
        $email = $this->security->xss_clean($this->input->post('email'));
        $otp   = $this->security->xss_clean($this->input->post('otp'));
        $query = $this->db->get_where('users', array(
            'reset_token' => $otp,
            'email' => $email
        ));
        if ($query->num_rows() == 1) {
            $row    = $query->row_array();
            $result = $row['email'];
            return $result;
        } //$query->num_rows() == 1
        else {
            $this->session->set_flashdata('error_msg', 'Enter correct OTP.');
            return false;
        }
    }
}
?>
