<?php
class Dashboard_model extends CI_Model
{
    /**
     * To load the database.
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function __construct()
    {
        
        $this->load->database();
        $this->load->helper('url');
    }

    /**
     * Function to fetch total number of users into admin dashboard page
     * 
     * @param void
     * 
     * @returns $result
     * 
     */
    public function get_users()
    {
        $this->db->where('type','user');
        $get_users_q = $this->db->get('users');
        $row         = $get_users_q->num_rows();
        return $row;
    }

    /**
     * Function to fetch total number of tasks into admin dashboard page
     * 
     * @param void
     * 
     * @returns $result
     * 
     */
    public function get_tasks()
    {
        $get_task_q = $this->db->query("SELECT id FROM task");
        $row_task   = $get_task_q->num_rows();
        return $row_task;
    }

    /**
     * Function to fetch total number of projects into admin dashboard page
     * 
     * @param void
     * 
     * @returns $result
     * 
     */
    public function get_projects()
    {
        $get_proj_q = $this->db->query("SELECT id FROM project");
        $row_proj   = $get_proj_q->num_rows();
        return $row_proj;
    }

    /**
     * Function to fetch top users data into admin dashboard page
     * 
     * @param void
     * 
     * @returns array(count of top users,user details)
     * 
     */
    public function get_top_users(){
        $final_data = array();
        $this->db->select('u.id AS user_id,u.name AS user_name,u.profile');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('time_details AS d');
        $this->db->join('users AS u','u.id = d.user_id');
        $this->db->group_by('u.id');
        $this->db->order_by('t_minutes','desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if($query->num_rows() >0){
            $users = $query->result_array();
            $final_data = array($query->num_rows(),$users);
        }else{
            $final_data = '';
        }
        return $final_data;
    }

    /**
     * Function to fetch top project data into admin dashboard page
     * 
     * @param void
     * 
     * @returns array(count of top projects,project details)
     * 
     */
    public function get_top_projects(){
        $this->db->select('p.id AS project_id,p.name AS project_name,p.image_name');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id');
        $this->db->join('time_details AS d','d.task_id = t.id');
        $this->db->group_by('p.id');
        $this->db->order_by('t_minutes','desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if($query->num_rows() > 0 ){
            $projects = $query->result_array();
        }else{
            $projects = '';
        }
        return array($query->num_rows(),$projects);
    }

    /**
     * Function to fetch graph data into admin dashboard page
     * 
     * @param void
     * 
     * @returns array(days as labels,project details)
     * 
     */
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
                        //$array[$i][] = round(($q['t_minutes']/60),2);
                        $array[$i][] = sprintf('%02d.%02d',floor($q['t_minutes']/60),($q['t_minutes']%60));
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

    /**
     * Function to fetch task/project/user data
     * 
     * @param $type
     * 
     * @returns $details
     * 
     */
    public function get_task_details($type){

        if($type == 'user'){
            //load user information into user_snapshot page

                //get all the project details along with time taken by the user on each project
            $sql = $this->db->query("SELECT GROUP_CONCAT(CONCAT_WS('~',p.name,p.id,p.image_name,p.color_code,p.t_minutes)) AS proj, `u`.`name` AS `user_name`, `u`.`id` AS `user_id`, SUM(t_minutes) AS `tt_minutes` FROM users AS u LEFT JOIN (SELECT ps.name,ps.id,ps.image_name,ps.color_code,t.id AS task_id,SUM(`d`.`total_minutes`) AS `t_minutes`,pa.user_id FROM `project` AS `ps` JOIN project_assignee AS pa ON pa.project_id =ps.id LEFT JOIN `task` AS `t` ON `t`.`project_id` = `pa`.`project_id` LEFT JOIN `time_details` AS `d` ON (d.task_id = t.id AND d.user_id = pa.user_id) GROUP BY pa.project_id,pa.user_id) AS p ON p.user_id = u.id WHERE u.type = 'user' GROUP BY u.id");
            $sql_result = $sql->result_array();
            /*echo "<pre>";
            print_r($sql_result);*/
            foreach($sql_result AS $result){
                $data = array();
                if (!empty($result['proj'])) {
                    $projects = explode(',',$result['proj']);
                    foreach ($projects as $p_value) {
                        $proj = explode('~',$p_value);
                        if (is_array($proj)) {
                            $time_format = isset($proj[4]) ? $this->format_time($proj[4]) : 0;
                            $data[] = array('project_name'=>$proj[0],'project_id'=>$proj[1],'image_name'=>$proj[2],'color_code'=>$proj[3],'project_time'=>$time_format);
                        }
                    }
                } else {
                    $data = '';
                }
                $total_time_format = $this->format_time($result['tt_minutes']);
                    $details[] = array(
                        'user_id'=>$result['user_id'],
                        'user_name'=> $result['user_name'],
                        'project'=>$data,
                        'total_minutes'=>isset($total_time_format)?$total_time_format:'0'
                    );
            }
            return $details;
        }

        else if($type == 'project'){ //load projectdetails into project_snapshot page
            $this->db->select('p.id AS project_id,p.name AS project_name,p.image_name,p.color_code');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('project AS p');
            $this->db->join('task AS t','t.project_id = p.id','LEFT');
            $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
            $this->db->group_by('p.id');
            $projects = $this->db->get()->result_array();
            foreach ($projects as $project) {
                $total_time_format = $this->format_time($project['t_minutes']);

                $this->db->select('count(distinct a.user_id) AS user_count');
                $this->db->from('project AS p');
                $this->db->join('task AS t','t.project_id = p.id','LEFT');
                $this->db->join('project_assignee AS a','a.project_id = p.id');
                $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
                $this->db->where('a.project_id',$project['project_id']);
                $proj_time = $this->db->get()->result_array();

                foreach($proj_time as $p){
                    //get each prject data and assign to main array
                    $this->db->select('u.name AS user_name,u.id AS user_id');
                    $this->db->distinct()->select('u.id');
                    $this->db->from('task AS t');
                    $this->db->join('project AS p','p.id = t.project_id','RIGHT');
                    $this->db->join('project_assignee AS a','a.project_id = p.id');
                    $this->db->join('users AS u','u.id = a.user_id');
                    $this->db->where('p.id',$project['project_id']);
                    $user_details = $this->db->get()->result_array();

                    $final_result[$project['project_id']] = array('project_id'=>$project['project_id'],'project_name'=>$project['project_name'],'project_icon'=>$project['image_name'],'project_color'=>$project['color_code'],'time_used'=>$total_time_format, 'total_users'=>$p['user_count'], 'user_details' =>$user_details );
                }
            }

            return $final_result;
               
        } else if($type == 'task') {//load task details into datatable in task_snapshot page
            $post_data = $this->input->post();
            $data = array();
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            $order = $this->input->post("order");
            $search= $this->input->post("search");
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
                1=>'description',
                2=>'p.name',
                3=>'u.name',
                4=>'start_time',
                5=>'end_time',
                6=>'t_minutes',
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
                $this->db->like('t.task_name',$search);
                $this->db->or_like('IF(d.task_description != "",d.task_description,t.description)',$search);
                $this->db->or_like('p.name',$search);
                $this->db->or_like('u.name',$search);
            }
            $this->db->select('d.id AS table_id, t.id AS task_id, t.task_name,IF(d.task_description != "",d.task_description,t.description) AS description,p.name AS project_name, p.id AS project_id,u.id AS user_id, u.name AS user_name,d.start_time,d.end_time');
          $this->db->select_sum('d.total_minutes','t_minutes');
          $this->db->from('project AS p');
          $this->db->join('task AS t','t.project_id = p.id');
          $this->db->join('task_assignee AS ta','ta.task_id = t.id','LEFT');
           $this->db->join('time_details AS d','(d.task_id = t.id AND d.user_id = ta.user_id)','LEFT');
           $this->db->join('users AS u','u.id = ta.user_id','LEFT');
            
            if(!empty($post_data['project_id'])){
                $this->db->where('p.id',$post_data['project_id']);
            }
            if(!empty($post_data['user_id'])){
                $this->db->where('u.id',$post_data['user_id']);
                //$where_condition['u.id'] = $post_data['user_id'];
            }
            if(!empty($post_data['start_date']) && !empty($post_data['end_date'])){
                $this->db->where('d.task_date BETWEEN "'.$post_data['start_date'].'" AND "'.$post_data['end_date'].'"');
            }
            $this->db->group_by('ta.id,d.id');
            $this->db->limit($length,$start);
            $tasks_data = $this->db->get()->result();
            $this->load->model('user_model');
            $total_time_used = 0;
            foreach($tasks_data as $rows)
            {
               // $total_time_used = $total_time_used + $rows->t_minutes;
                $total_time_format = $this->format_time($rows->t_minutes);
                $username = '<a href=../admin/load_userdetails_page?user_id='.$rows->user_id.'">'.$rows->user_name.'</a>';
                $data['final_data'][]= array(
                    $rows->task_name,
                    ($rows->description)?$rows->description:'--',
                    $rows->project_name,
                    isset($rows->start_time)?$this->user_model->convert_date($rows->start_time):'--',
                    isset($rows->end_time)?$this->user_model->convert_date($rows->end_time):'--',
                    isset($rows->t_minutes)?$total_time_format:'--',
                    $rows->table_id,
                    $rows->project_id,
                    $user_name = !empty($username) ? $username : '--',
                    $rows->task_id
               );
            }
            //$data['total_time_used'] = $this->format_time($total_time_used);
            return $data;
        }
    }


    /**
     * Function to get task details for user snapshot graph
     * 
     * @param void
     * 
     * @returns $data
     * 
     */
    public function user_graph_data(){
        $data = array();
        $task_count = array();
        if($this->input->post('month')){
            if(!empty($this->input->post('month'))){
                $start_date = date('Y-m-01',strtotime($this->input->post('month')));
                $end_date = date('Y-m-t',strtotime($start_date));
                $this->db->select('d.task_date,count(distinct t.id) AS tasks_count');
                $this->db->from('task AS t');
                $this->db->join('time_details AS d','d.task_id = t.id');
                $this->db->where('d.task_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
                $this->db->group_by('d.task_date');
                $graph_data = $this->db->get();
                if($graph_data->num_rows() > 0){
                    $t_data = $graph_data->result_array();
                    $this->load->model('user_model');
                    $date_range = $this->user_model->get_dates_from_range($start_date,$end_date);
                    for ($j=0;$j<sizeof($date_range);$j++) {
                        $task_count[$j] = '';
                        foreach($t_data AS $d){
                            if($date_range[$j] == $d['task_date']){
                                $task_count[$j] = $d['tasks_count'];
                            }
                        }
                        $data[$j] = array('task_date'=>$date_range[$j],'tasks_count'=>$task_count[$j]);
                    }
                }else{
                    $data = '';
                }
            }
        }else if($this->input->post('project_name')){
            if(!empty($this->input->post('project_name'))){
                //get project from project name
                $project_name = $this->input->post('project_name');
                $get_project_id = $this->db->get_where('project',array('name'=>$project_name));
                $project_id = $get_project_id->row_array()['id'];

                $query = $this->db->query("SELECT `u`.`name`, `u`.`id`,td.t_minutes  FROM `users` AS `u` LEFT JOIN project_assignee AS pa ON pa.user_id= u.id LEFT JOIN (SELECT SUM(`d`.`total_minutes`) AS `t_minutes`,d.user_id FROM `time_details` AS `d`  JOIN `task` AS `t` ON `t`.`id` = `d`.`task_id` WHERE `t`.`project_id` = {$project_id} GROUP BY d.user_id) AS td ON td.user_id = u.id WHERE `u`.`type` = 'user' AND pa.project_id = {$project_id} GROUP BY `u`.`id`");
                if($query->num_rows() > 0){
                    $users = $query->result_array();
                    foreach($users as $u){
                        $data[] = array('user_name'=>$u['name'],'time_used'=>$u['t_minutes']);
                    }
                }else{
                    $data = '';
                }
            }else{
                $data['error'] = "Project id is empty.";
            }
        }else{
            $this->db->select('u.id AS user_id,u.name AS user_name');
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->from('users AS u');
            $this->db->join('time_details AS d','d.user_id = u.id','LEFT');
            $this->db->where('u.type','user');
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

    /**
     * Function to get user chart data (onclick Username/Project name)
     * 
     * @param $type
     * 
     * @returns $data
     * 
     */
    public function get_user_chart($type){
        
        if($type == 'user-chart'){ //load user chart data into user details page
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

        }else if($type == 'project_chart'){ //load project chart data into project details page
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

    /**
     * Function to get task datatable data
     * 
     * @param $table_type
     * 
     * @returns $data
     * 
     */
    public function user_task_data($table_type){
        if($table_type == 'user_task'){ //load datatable data into userdetails page
            $user_id = $this->input->post('user_id');
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            $order = $this->input->post("order");
            $search= $this->input->post("search");
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
                1=>'project_name',
                2=>'t_minutes'
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
                $this->db->like('t.task_name',$search);
            }

            $user_tasks_query = $this->db->query("SELECT `t`.`id` AS `task_id`, `t`.`task_name`, `p`.`name` AS project_name,t_minutes FROM `project` as `p` JOIN `task` AS `t` ON `t`.`project_id` = `p`.`id` JOIN `task_assignee` AS `ta` ON `ta`.`task_id` = `t`.`id` LEFT JOIN (SELECT d.task_id,SUM(d.total_minutes) AS t_minutes FROM time_details AS d WHERE d.user_id = {$user_id} GROUP BY d.task_id) AS td ON td.task_id = ta.task_id WHERE t.task_name LIKE '%{$search}%' AND ta.user_id = {$user_id} GROUP BY `t`.`id` ORDER BY {$order} {$dir} LIMIT {$length} OFFSET {$start}");
            $user_tasks = $user_tasks_query->result();
            $data = array();
            foreach($user_tasks as $rows)
            {
                $total_time_format = $this->format_time($rows->t_minutes);
                $data[]= array($rows->task_name,$rows->project_name,$total_time_format);
            }
            return $data;
        }else if($table_type == 'project_task'){ //load datatable data into projectdetails page
            $project_id = $this->input->post('project_id');
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            $order = $this->input->post("order");
            $search= $this->input->post("search");
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

            /*if($dir != "asc" && $dir != "desc")
            {
                $dir = "desc";
            }*/
            $valid_columns = array(
                0=>'t_name',
                1=>'u_count',
                2=>'t_minutes'
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
                $this->db->like('t.task_name',$search);                 
            }

            $data = array();
                $tasks_query = $this->db->query("SELECT us.t_name, COUNT(DISTINCT us.u_id) AS u_count , SUM(td.total_minutes) AS t_minutes FROM time_details AS td RIGHT JOIN (SELECT u.id AS u_id, t.id AS task_id, t.task_name AS t_name FROM task AS t LEFT JOIN task_assignee AS ta ON t.id =ta.task_id LEFT JOIN users AS u ON ta.user_id=u.id WHERE t.project_id = {$project_id} ) AS us ON (td.task_id = us.task_id AND td.user_id = us.u_id ) WHERE t_name LIKE '%{$search}%' GROUP BY us.task_id ORDER BY {$order} {$dir} LIMIT {$length} OFFSET {$start}");
                $total_time = $tasks_query->result_array();
                foreach($total_time AS $t_data){
                    if($t_data['t_minutes'] != NULL)
                        $time_taken = $this->format_time($t_data['t_minutes']);
                    $data[]= array($t_data['t_name'],($t_data['u_count'])?($t_data['u_count']):'0',($t_data['t_minutes'] != NULL)?$time_taken:'--');
                }
                
            return $data;
        }
    }

    /**
     * Function to get project datatable data
     * 
     * @param $table_type
     * 
     * @returns $data
     * 
     */
    public function user_project_data($table_type){
        if($table_type == 'user_project'){ //load datatable data into user details page

            $user_id = $this->input->post('user_id');
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            $order = $this->input->post("order");
            $search= $this->input->post("search");
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
                2=>'t_minutes'
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
                $this->db->like('p.name',$search);                 
            }
            $data = array();
            $timings_query = $this->db->query("SELECT p.name,tt.tasks_count,tt.t_minutes FROM `project` AS `p` JOIN project_assignee AS pa ON (pa.project_id = p.id AND pa.user_id = {$user_id}) LEFT JOIN (SELECT td.t_minutes,count(ta.task_id) AS tasks_count,t.project_id,ta.task_id,ta.user_id FROM `task` AS `t` JOIN `task_assignee` AS `ta` ON ta.task_id = t.id LEFT JOIN(SELECT d.task_id,SUM(d.total_minutes) AS t_minutes FROM task AS tas JOIN time_details AS d ON d.task_id = tas.id WHERE d.user_id = {$user_id} AND tas.project_id IN (SELECT a.project_id FROM project_assignee AS a GROUP BY a.project_id) GROUP BY tas.project_id) AS td ON td.task_id = ta.task_id WHERE ta.user_id = {$user_id} AND t.project_id IN (SELECT a.project_id FROM project_assignee AS a GROUP BY a.project_id)GROUP BY t.project_id) AS tt ON tt.project_id = p.id WHERE p.name LIKE '%{$search}%' GROUP BY p.id ORDER BY {$order} {$dir} LIMIT {$length} OFFSET {$start}");
            $timings = $timings_query->result();
            foreach($timings AS $proj_time){
                $time_taken_format = $this->format_time($proj_time->t_minutes);

                $data[] = array(
                    $proj_time->name,
                    ($proj_time->tasks_count)?$proj_time->tasks_count:0,
                    $time_taken_format
                );
            }
            return $data;
        }else if($table_type == 'project_user'){ //load datatable data into project details page
            $data = array();
            $project_id = $this->input->post('project_id');
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            $order = $this->input->post("order");
            $search= $this->input->post("search");
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
            /*if($dir != "asc" && $dir != "desc")
            {
                $dir = "desc";
            }*/
            $valid_columns = array(
                0=>'u_name',
                1=>'t_count',
                2=>'t_minutes'
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
                $this->db->like('u_name',$search);
            }

                $task_details_query = $this->db->query("SELECT us.u_id, us.u_name, COUNT(DISTINCT us.task_id) AS t_count , SUM(td.total_minutes) AS t_minutes FROM time_details AS td RIGHT JOIN (SELECT u.id AS u_id, t.id AS task_id, u.name AS u_name FROM users AS u LEFT JOIN project_assignee AS pa ON u.id = pa.user_id LEFT JOIN task_assignee AS ta ON pa.user_id = ta.user_id   LEFT JOIN task AS t ON (ta.task_id=t.id AND t.project_id = {$project_id} ) WHERE pa.project_id = {$project_id} ) AS us ON (td.task_id = us.task_id AND td.user_id = us.u_id )  WHERE u_name LIKE '%{$search}%' GROUP BY us.u_id ORDER BY {$order} {$dir} LIMIT {$length} OFFSET {$start}");
                $task = $task_details_query->result_array();
                foreach($task AS $t_data){
                    $time_taken = $this->format_time($t_data['t_minutes']);
                    $data[] = array($t_data['u_name'],($t_data['t_count'])?($t_data['t_count']):'0',$time_taken);
                }
            return $data;
        }
    }

    /**
     * Function to get project data into project details page
     * 
     * @param $project_id
     * 
     * @returns $data
     * 
     */
    public function get_project_data($proj_id)
    {
        $this->db->select('count(distinct a.user_id) AS users_count');
        $this->db->from('project_assignee AS a');
        $this->db->where('a.project_id',$proj_id);
        $result = $this->db->get();
        if($result->num_rows() > 0){
            $usr = $result->row_array()['users_count'];
            $this->db->select_sum('d.total_minutes','t_minutes');
            $this->db->select('count(distinct t.id) AS tasks_count');
            $this->db->select('p.name AS project_name,p.image_name,p.id AS project_id');
            $this->db->from('task AS t');
            $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
            $this->db->join('project AS p','p.id = t.project_id');
            
            $this->db->where('p.id',$proj_id);
            $res = $this->db->get();
            if($res->num_rows() > 0){
                $count = $res->result_array();
                foreach($count as $c){
                    $time_taken = $this->format_time($c['t_minutes']);
                    $data = array('project_name'=>$c['project_name'],'project_id'=>$c['project_id'],'users'=>$usr,'tasks'=>$c['tasks_count'],'image_name'=>$c['image_name'],'total_minutes'=>$time_taken);
                }
            }else{
                $data = '';
            } 
        }else{
            $data = '';
        }
        return $data;
    }

    /**
     * Function to assign user to the project (in project details page)
     * 
     * @params $project_id and $user_id
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function assign_user($user_id,$project_id){
        $this->db->where(array('user_id'=>$user_id,'project_id'=>$project_id));
        $proj_assigned = $this->db->get('project_assignee');
        if($proj_assigned->num_rows()>0){
            $result_data['message'] = "This user has already assigned to the Project.";
            $result_data['status'] = false;
        }else{
            $array = array('project_id'=>$project_id,'user_id'=>$user_id,'created_on'=>date('Y-m-d H:i:s'));
            $this->db->set($array);
            $assign = $this->db->insert('project_assignee',$array);
            if($assign){ //send email to the user after assigning project
                $project = $this->db->get_where('project',array('id'=>$project_id));
                $project_name = $project->row_array()['name'];

                $user = $this->db->get_where('users',array('id'=>$user_id));
                $user_email = $user->row_array()['email'];
                $user_name = $user->row_array()['name'];
                $this->load->library('email');
                //send mail to the user
                $to = $user_email;
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
                $this->email->to($user_email);
                $this->email->subject('TimeTracker Notification: New project');
                $this->email->message('Hi '.$user_name.', you are assigned to the project: '.$project_name);
                //$this->email->send();
                if(!$this->email->send()){
                    $result_data['message'] = $this->email->print_debugger();
                    $result_data['status'] = false;
                }else{
                    $result_data['message'] = "User assigned.";
                    $result_data['status'] = true;
                }
            }else{
                $result_data['message'] = "Could not assign User to the Project.";
                $result_data['status'] = false;
            }
        }
        return $result_data;
    }

    /**
     * Function to get user details (in user details page)
     * 
     * @param void
     * 
     * @returns $user_data
     * 
     */
    public function get_user_data(){
        $user_id = $this->input->get('user_id');
        $this->db->select('u.id,u.name AS user_name,u.email,u.profile,u.phone');
        $this->db->select_sum('d.total_minutes','t_minutes');
        $this->db->select('count(distinct t.id) AS tasks_count');
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id');
        $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
        $this->db->join('users AS u','u.id = d.user_id');
        $this->db->where('u.id',$user_id);
        $user_details = $this->db->get()->row_array();

        $this->db->select('count(distinct a.project_id) AS project_count');
        $this->db->from('project_assignee AS a');
        $this->db->where('a.user_id',$user_id);
        $project_count = $this->db->get()->row_array();

        $user_data = array('id'=>$user_details['id'],'profile'=>$user_details['profile'],'user_name'=>$user_details['user_name'],'email'=>$user_details['email'],'phone'=>$user_details['phone'],'t_minutes'=>$user_details['t_minutes'],'tasks_count'=>$user_details['tasks_count'],'project_count'=>$project_count['project_count']);

        $user_data['t_minutes'] = $this->format_time($user_data['t_minutes']);
        return $user_data;
    }

    /**
     * Function to add new users
     * 
     * @param void
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function add_users()
    {
        $array1 = array(
            'name' => $this->input->post('task_name'),
            'email' => $this->input->post('user_email'),
            'password' => $this->input->post('task_pass'),
            'phone' => $this->input->post('contact'),
            'type' => 'user',
            'profile' => 'images.png',
            'created_on' => date('Y-m-d H:i:s')
        );
        $this->db->set($array1);
        $query          = $this->db->insert('users', $array1);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to check whether user exists while adding a new user
     * 
     * @param void
     * 
     * @returns TRUE/FALSE
     * 
     */
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

    /**
     * Function to add project module
     * 
     * @param void
     * 
     * @returns TRUE/FALSE
     * 
     */
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

    /**
     * Function to get list of all project details
     * 
     * @param void
     * 
     * @returns $project_data
     * 
     */
    public function get_project_name()
    {
        $projects = array();
        $query  = $this->db->query("SELECT p.name AS project_name,p.color_code,p.image_name,p.id FROM project AS p");
        if($query->num_rows() > 0)
            $projects = $query->result_array();
        else
            $projects = '';
        return $projects;
    }

    /**
     * Function to get project module
     * 
     * @param $project_id
     * 
     * @returns $result
     * 
     */
    public function get_module_name($project_id){
        $p_id = $project_id;
        $query = $this->db->query("SELECT id,name FROM project_module WHERE project_id = {$p_id} OR project_id = 0");
        $result = $query->result();
        return $result;
    }

    /**
     * Function to get list of users into add task and/or add project page
     * 
     * @param void
     * 
     * @returns $names
     * 
     */
    public function get_usernames(){
        $this->db->select('name,id,profile,email');
        $this->db->from('users');
        $this->db->where('type','user');
        $query = $this->db->get();
        $names = $query->result_array();
        return $names;
    }

    /**
     * Function to assign tasks
     * 
     * @param void
     * 
     * @returns $project_id
     * 
     */
    public function assign_tasks()
    {
        $select = $this->input->post('user-name');
        if(!empty($this->input->post('module'))){
            $module_id = $this->input->post('module');
        }else{
            $check_module = $this->db->get_where('project_module',array('id'=>1));
            if($check_module->num_rows() == 1){
                $module_id = $check_module->row_array()['id'];
            }else{
                $module_data = array('id'=>1,'project_id'=>$this->input->post('chooseProject'),'name'=>'General','meta_data'=>'Default for any task if not select any module.','created_on'=>date('Y-m-d H:i:s'));
                $this->db->set($module_data);
                $insert_module_id = $this->db->insert('project_module',$module_data);
                $module_id = $this->db->insert_id();
            }
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
        $task_id = $this->db->insert_id();
        if(sizeof($select) > 0){
            for($i=0;$i<sizeof($select);$i++){
                $query  = $this->db->get_where('users', array(
                    'name' => $select[$i]['name']
                ));
                $user_id[$i] = $query->row_array();

                //assign user to the task
                $array  = array(
                        'user_id' => $user_id[$i]['id'],
                        'task_id' => $task_id,
                        'created_on' => date('Y-m-d H:i:s')
                );
                $this->db->set($array);
                $assign_task = $this->db->insert('task_assignee', $array);

                //check whether user is assigned to the project
                $this->db->where(array('user_id'=>$user_id[$i]['id'],'project_id'=>$this->input->post('chooseProject')));
                $assignee_check = $this->db->get('project_assignee');
                if($assignee_check->num_rows() == 0){
                    //assign user to project
                    $assign_project_user = array('user_id'=> $user_id[$i]['id'], 'project_id'=> $this->input->post('chooseProject'), 'created_on' => date('Y-m-d H:i:s'));
                    $this->db->set($assign_project_user);
                    $assign_project = $this->db->insert('project_assignee', $assign_project_user);
                }
            }
        }
        return $task_id;
    }

    /**
     * Function to check whether project exists while adding new project
     * 
     * @param void
     * 
     * @returns TRUE/FALSE
     * 
     */
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

    /**
     * Function to add project
     * 
     * @param project_icon
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function add_projects($project_icon)
    {
        $userid = $this->session->userdata('userid');
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
            $array = array('color_code' => $this->input->post('project-color'),'image_name' => $project_icon,'name' => $this->input->post('project-name'), 'created_on' => date('Y-m-d H:i:s'));
            $this->db->set($array);
            $query = $this->db->insert('project',$array);
            $project_id = $this->db->insert_id(); //get last insert id of project table
        }
        else {
            $query = $this->db->get_where('project',array('name'=>$this->input->post('project_name')));
            $project_id = $query->row_array()['id'];
        }

        if($module != ''){   //if module name is entered store 'module' into project_module table
            for($i=0;$i<sizeof($module);$i++) {
                if(!empty($module[$i]['module'])){
                   $array = array('project_id'=>$project_id,'name'=>$module[$i]['module'],'created_on'=>date('Y-m-d H:i:s'));
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

    /**
     * Function to delete user
     * 
     * @param $userid
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function delete_user($userid){
        $array = array('time_details','login_details','project_assignee','task_assignee');
        $delete = $this->db->delete($array,array('user_id'=>$userid));
        $this->db->where('id',$userid);
        $result = $this->db->delete('users');
        if(!($this->db->affected_rows())){
            return false;
        }else{
            return true;
        }  
    }

    /**
     * Function to delete task
     * 
     * @param $task_id
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function delete_task($task_id){
        //$array = array('task_assignee','time_details');
        //$delete = $this->db->delete($array,array('task_id'=>$task_id));
        $this->db->where('id',$task_id);
        $this->db->delete('time_details');
        if(!($this->db->affected_rows())){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Function to load admin profile
     * 
     * @param void
     * 
     * @returns TRUE/FALSE
     * 
     */
    public function my_profile(){
        $userid = $this->session->userdata('userid');
        $this->db->where('id',$userid);
        $query = $this->db->get('users');
        if($query->num_rows() == 1){
            return $query->row_array();
        }
    }

    /**
     * Function to update profile picture
     * 
     * @param $picture
     * 
     * returns TRUE/FALSE
     */
    public function submit_profile($picture)
    {
        $useremail = $this->session->userdata('email');
        $this->db->where('email', $useremail);
        $query = $this->db->update('users', array('profile'=>$picture,'modified_on'=>date('Y-m-d H:i:s')));
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

    /**
     * Function to Check for the Old Password
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
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
    
    /**
     * Function to change password
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
    public function change_password()
    {
        $new_pwd     = $this->input->post('psw11');
        $confirm_pwd = $this->input->post('psw22');
        if ($new_pwd == $confirm_pwd) {
            if ($this->session->userdata('email')) {
                $email = $this->session->userdata('email');
            }
            else {
                $email = $this->security->xss_clean($this->input->post('mail'));
            }
            $data = array('password'=>md5($new_pwd));
            $this->db->where('email', $email);
            $query = $this->db->update('users',$data);
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

    /**
     * (API)Login function
     * 
     * @param $post
     * 
     * returns FALSE, if not an user
     * return user data
     */
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
                /*$this->db->where(array(
                    'task_date' => date('Y:m:d'),
                    'user_id' => $row->id
                ));
                $query_check = $this->db->get('login_details');
                if ($query_check->num_rows() > 0) { //multiple logins on the same date
                    $login_data = $query_check->row_array();
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
                }*/
                $data          = array();
                $data['id']    = $row->id;
                $data['type']  = $row->type;
                $data['email'] = $row->email;
                $data['username'] = $row->name;
                $data['phone'] = $row->phone;
                $data['profile_pic'] = base_url().USER_UPLOAD_PATH.$row->profile;
                $data['login_time'] = date('Y-m-d H:i:s');
                return $data;
            }
            else {
                //return 'Wrong inputs.';
                return false;
            }
        }
        
    /**
     * Login function
     * 
     * @param void
     * 
     * returns FALSE, if not an user
     * return user type
     */
    public function login_process()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->db->where('email',$username);
        $this->db->where('password',md5($password));
        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            $row    = $query->row();
            $data = array(
                'userid' => $row->id,
                'email' => $row->email,
                'logged_in' => TRUE,
                'user_profile' => $row->profile,
                'username' => $row->name,
                'user_type' => $row->type,
                'user_tz' => ($this->input->post('time-zone'))?$this->input->post('time-zone'):'Asia/Kolkata'
            );
            $this->session->sess_expiration = '86400';// expires in 4 hours
            $this->session->sess_expire_on_close = FALSE;
            $this->session->set_userdata($data);

            return $row->type;
        } else {
            $this->form_validation->set_message('Wrong inputs.');
            return false;
        }
    }

    /**
     * Send OTP function
     * 
     * @param void
     * 
     * returns TRUE/FALSE
     */
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
                    $this->email->from('admin1@printgreener.com');
                    $this->email->to($email);
                    $this->email->subject('TimeTracker login!');
                    $this->email->set_newline("\r\n");
                    $this->email->message('Use this OTP for TimeTracker login:'.$token);
                    //$this->email->send();
                    if(!$this->email->send()){
                        print_r($this->email->print_debugger());
                        $result = 'failed';
                    }else{
                        $result = 'success';   
                    }
                    //return true;
                } //$query
                else {
                    $result = 'failed';    
                }
            } //$query->num_rows() == 1
            else {
                $result = 'error';
            }
            return $result;
        }
    }

    /**
     * Check OTP function
     * 
     * @param void
     * 
     * returns FALSE, if OTP is not there or is not matching the entered OTP 
     * returns $email
     */
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

    /**
     * Function to convert minutes into hours and minutes
     * 
     * @param $minutes
     * 
     * returns formatted time in hours and minutes(%dh %dm)
     */
    public function format_time($minutes){
        $hours = floor($minutes / 60);
        $minutes = ($minutes % 60);
        if(($minutes < 1) && ($hours < 1))
            $time_taken = sprintf('--');
        else if($minutes < 1)
            $time_taken = sprintf('%02dh', $hours);
        else if($hours < 1)
            $time_taken = sprintf('%02dm', $minutes);
        else
            $time_taken = sprintf('%02dh %02dm', $hours, $minutes);

        return $time_taken;
    }

    /**
     * Function to get data count without filter for task snapshot datatable
     * 
     * @param void
     * 
     * @returns $count
     * 
     */
    public function original_task_data($type,$id){
        if($type == 'task'){
            $post_data = $this->input->post();
            $search= $this->input->post("search");
            $search = $search['value'];
            $this->db->select('d.id,t.task_name,p.name,u.name,IF(d.task_description != "",d.task_description,t.description) AS description');
            //$records = $this->db->get('time_details')->row();
            $this->db->from('project AS p');
            $this->db->join('task AS t','t.project_id = p.id');
            $this->db->join('task_assignee AS ta','ta.task_id = t.id','LEFT');
            $this->db->join('time_details AS d','(d.task_id = ta.task_id AND d.user_id = ta.user_id)','LEFT');
            $this->db->join('users AS u','u.id = ta.user_id','LEFT');
            //get all the filter data
            if (!empty($search)) {
               $this->db->like('t.task_name',$search);
               $this->db->or_like('IF(d.task_description != "",d.task_description,t.description)',$search);
               $this->db->or_like('p.name',$search);
               $this->db->or_like('u.name',$search);
            }
            if(!empty($post_data['project_id'])){
                $this->db->where('p.id',$post_data['project_id']);
            }
            if(!empty($post_data['user_id'])){
                $this->db->where('u.id',$post_data['user_id']);
            }
            if(!empty($post_data['start_date']) && !empty($post_data['end_date'])){
                $this->db->where('d.task_date BETWEEN "'.$post_data['start_date'].'" AND "'.$post_data['end_date'].'"');
            }
            $this->db->group_by('ta.id,d.id');
            $records = $this->db->get()->num_rows();
            return $records;
        }else if($type == 'project_user'){
            $search= $this->input->post("search");
            $search = $search['value'];
            $where = "WHERE `a`.`project_id` = {$id}";
            if(!empty($search))
            {
                $where = "WHERE `a`.`project_id` = {$id} AND u.name LIKE '%{$search}%'";
            }
            $project_user_data = $this->db->query("SELECT count(u.id) AS user_data_count FROM `project_assignee` AS `a` JOIN `users` AS `u` ON `u`.`id` = `a`.`user_id` ".$where);
            $project_data = $project_user_data->row();
            return $project_data->user_data_count;
        }else if($type == 'project_task'){
            $search= $this->input->post("search");
            $search = $search['value'];
            $where = "WHERE `t`.`project_id` = {$id}";
            if(!empty($search))
            {
                $where = "WHERE `t`.`project_id` = {$id} AND t.task_name LIKE '%{$search}%'";
            }
            $project_tasks_data = $this->db->query("SELECT count(t.id) AS task_data_count FROM `task` AS `t` ".$where);
            $tasks_data = $project_tasks_data->row();
            return $tasks_data->task_data_count;
        }else if($type == 'user_task'){

            $search= $this->input->post("search");
            $search = $search['value'];
            $where = "WHERE `ta`.`user_id` = ".$id;
            if(!empty($search))
            {
                $where = "WHERE `ta`.`user_id` = {$id} AND t.task_name LIKE '%{$search}%'";
            }
            $user_tasks = $this->db->query("SELECT count(t.id) AS task_count FROM `task` AS `t` LEFT JOIN `task_assignee` AS `ta` ON `ta`.`task_id` = `t`.`id` ".$where);
            $tasks = $user_tasks->row();
            return $tasks->task_count;
        }else if($type == 'user_project'){

            $search= $this->input->post("search");
            $search = $search['value'];
            $where = "WHERE `a`.`user_id` = {$id}";
            if(!empty($search))
            {
                $where = "WHERE `a`.`user_id` = {$id} AND p.name LIKE '%{$search}%'";
            }
            $proj_count = $this->db->query("SELECT count(a.project_id) AS project_count FROM `project_assignee` AS `a` JOIN project AS p ON p.id = a.project_id ".$where);
            $projects = $proj_count->row();
            return $projects->project_count;
        }
    }

    /**
     * Function to check whether the project-id exists in db
     * 
     * @param $project_id
     * 
     * returns TRUE/FALSE
     */
    public function check_project_id($project_id){
        $project = $this->db->get_where('project', array('id'=>$project_id));
        if ($project->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to fetch project,module and assigned users' data to edit project page
     * 
     * @param $project_id
     * 
     * returns $project_data
     */
    public function load_edit_project_data($project_id){
        $proj_data = array();
        $project_data = $this->db->get_where('project',array('id'=>$project_id));
        if ($project_data->num_rows() > 0) {
            $proj_list = $project_data->row_array();
            $proj_data['project'] = array('project_id'=>$proj_list['id'],'project_name'=>$proj_list['name'],'project_color'=>$proj_list['color_code'],'project_image'=>$proj_list['image_name']);

            //get users who are assigned to the project
            $this->db->select('u.name,u.id,u.profile,u.email');
            $this->db->from('users AS u');
            $this->db->join('project_assignee AS a','a.user_id = u.id');
            $this->db->where('a.project_id',$project_id);
            $users_query = $this->db->get();
            if ($users_query->num_rows() > 0) {
                $users = $users_query->result_array();
                foreach($users AS $user){
                    $proj_data['users'][] = array('user_id'=>$user['id'],'user_name'=>$user['name'],'user_email'=>$user['email'],'profile_photo'=>$user['profile']);
                }
            } else {
                $proj_data['users'] = '';
            }

            //get modules under the project
            $module_data = $this->db->get_where('project_module', array('project_id'=>$project_id));
            if($module_data->num_rows() > 0) {
                foreach($module_data->result_array() AS $module){
                    $proj_data['module'][] = array('module_id'=>$module['id'],'module_name'=>$module['name']);
                }
            } else {
                $proj_data['module'][] = array('module_id'=>1,'module_name'=>'General');
            }
        } else {
            $proj_data['project'] = '';
        }
        return $proj_data;
    }

    /**
     * Function to fetch graph data to project_snapshot.php
     * 
     * @param void
     * 
     * returns $project_data
     */
    public function get_project_graph_data(){
        $this->db->select('p.name AS project_name,p.color_code,p.image_name,p.id');
        $this->db->select_sum('d.total_minutes','t_minutes');//get total minutes for a particular project
        $this->db->from('project AS p');
        $this->db->join('task AS t','t.project_id = p.id','LEFT');
        $this->db->join('time_details AS d','d.task_id = t.id','LEFT');
        $this->db->where_in(array('t.project_id'=>("SELECT id FROM project"))); //graph data for all projects
        $this->db->group_by('p.id');
        $projects = $this->db->get();
        $project_data = array();
        $proj_data = array();
        if($projects->num_rows() > 0){
            $proj_data = $projects->result_array();
            foreach($proj_data as $p_data){
                $project_data[] = $p_data;
            }
        }else{
            $project_data = '';
        }
        return $project_data;
    }

    /**
     * Function to edit project
     * 
     * @param $project_data
     * 
     * returns TRUE/FALSE
     */
    public function edit_project($proj_data){
        $get_project_image = $this->db->get_where('project',array('id'=>$proj_data['project_id']));
        if($get_project_image->num_rows() > 0){
            $project_logo = $get_project_image->row()->image_name;
        }else{
            $project_logo = 'default.png';
        }
        $update_values = array();
        $update_values = array('name'=>$proj_data['project-name'],'image_name'=>($proj_data['project_icon'])?$proj_data['project_icon']:$project_logo,'color_code'=>$proj_data['project-color'],'modified_on'=>date('Y-m-d H:i:s'));
        $this->db->where('id',$proj_data['project_id']);
        $this->db->set($update_values);
        $update_project = $this->db->update('project',$update_values); //edit "project" table
        if($update_project){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to check whether module name exists
     * 
     * @param $module_data
     * 
     * returns TRUE/FALSE
     */
    public function module_exists($module_data){
        if(isset($module_data['module_id'])){//if module id is sent(edit module case),check whether other modules have the same name under the selected project-id
            $this->db->where(array('project_id'=>$module_data['project_id'],'name'=>$module_data['module_name']));
            $this->db->where('id != ', $module_data['module_id']);
            $check_module = $this->db->get('project_module');
        }else{ //(add new module case)check whether module name already exists under the selected project-id
            $check_module = $this->db->get_where('project_module',array('name'=>$module_data['module_name'],'project_id'=>$module_data['project_id']));
        }
        if($check_module->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to add/edit module
     * 
     * @param $module_data
     * 
     * returns TRUE/FALSE
     */
    public function add_module_data($module_data){
        $mod_values = array();
        if(isset($module_data['module_id'])){
            /** Edit module case **/
            $mod_values = array('project_id'=>$module_data['project_id'],'name'=>$module_data['module_name'],'meta_data'=>'','modified_on'=>date('Y-m-d H:i:s'));
            $this->db->where('id',$module_data['module_id']);
            $this->db->set($mod_values);
            $module = $this->db->update('project_module',$mod_values);
        }else{
            /** Add module case **/
            $mod_values = array('project_id'=>$module_data['project_id'],'name'=>$module_data['module_name'],'meta_data'=>'','created_on'=>date('Y-m-d H:i:s'));
            $this->db->set($mod_values);
            $module = $this->db->insert('project_module',$mod_values);
        }
        if($module){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to validate module-id
     * 
     * @param $module_data
     * 
     * returns TRUE/FALSE
     */
    public function valid_module_id($module_data){
        $validate_id = $this->db->get_where('project_module',array('id'=>$module_data['module_id'],'project_id'=>$module_data['project_id']));
        if($validate_id->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to delete module
     * 
     * @param $module_data
     * 
     * returns TRUE/FALSE
     */
    public function delete_module($module_data){
        $get_task_id = $this->db->get_where('task',array('module_id'=>$module_data['module_id'],'project_id'=>$module_data['project_id']));
        if($get_task_id->num_rows() > 0){//if tasks exist for the chosen module
            $get_tasks = $get_task_id->result_array();
            $tables = array('time_details','task_assignee');
            foreach($get_tasks AS $task){
                $task_id = $task['id'];
                $this->db->delete($tables,array('task_id'=>$task_id)); //delete entries from tim_details and task_assingee table

                $delete_task_row = $this->db->delete('task',array('id'=>$task_id)); //delete tasks from task table
            }

            //finally, delete module from project_module table
            $delete_module = $this->db->query("DELETE FROM `project_module` WHERE `id` = {$module_data['module_id']}");
            if($delete_module){
                return true;
            }else{
                return false;
            }
        }else{ //if there is no task under the chosen module, delete the module from project_module table
            $this->db->where(array('id'=>$module_data['module_id'],'project_id'=>$module_data['project_id']));
            $this->db->delete('project_module');
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Function to check whether user is assigned to project
     * 
     * @param $user_data
     * 
     * returns TRUE/FALSE
     */
    public function check_user($user_data){
        $user_exists = $this->db->get_where('project_assignee',array('user_id'=>$user_data['user_id'],'project_id'=>$user_data['project_id']));
        if($user_exists->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to un-assign user from project
     * 
     * @param $user_data
     * 
     * returns TRUE/FALSE
     */
    public function remove_user_from_project($user_data){
        $tables = array('time_details','task_assignee');
        $get_tasks = $this->db->get_where('task',array('project_id'=>$user_data['project_id']));
        if($get_tasks->num_rows() > 0){//if tasks exist under the chosen project
            $tasks = $get_tasks->result_array();
            foreach($tasks AS $t_id){
                $this->db->delete($tables,array('user_id'=>$user_data['user_id'],'task_id'=>$t_id['id']));//delete entries from time_details,task_assignee tables
            }

            //delete user data from project_assignee table
            $this->db->delete('project_assignee',array('user_id'=>$user_data['user_id'],'project_id'=>$user_data['project_id']));
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
        //if there is no task under the given prject, delete user data from project_assingee table
        $this->db->delete('project_assignee',array('user_id'=>$user_data['user_id'],'project_id'=>$user_data['project_id']));
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function task_name_exists($task_data){
        $check_task = $this->db->get_where('task',array('task_name'=>$task_data['task_name'],'project_id'=>$task_data['chooseProject']));
        if($check_task->num_rows() > 0){
            $this->form_validation->set_message('task_exists', 'This Task Already Exists.');
            return true;
        }else{
            return false;
        }
    }

    public function get_task_data($task_id){
      $details = array();
        //check whether the task id is valid
        $this->db->select('p.name,p.id AS project_id,m.id AS module_id,m.name AS module_name,t.task_name,t.description,t.id AS task_id,ta.user_id,u.name AS user_name');
        $this->db->from('task AS t');
        $this->db->join('task_assignee AS ta','ta.task_id = t.id');
        $this->db->join('project AS p', 'p.id = t.project_id');
        $this->db->join('project_module AS m', 'm.id = t.module_id');
        $this->db->join('users','u.id = ta.user_id');
        $this->db->where(array('t.id'=>$task_id));
        $task_data = $this->db->get()->row_array();//get task,project and module info

        //check whether there is a timeline data for the task
        $check_timeline = $this->db->get_where('time_details',array('task_id'=>$task_id));
        if($check_timeline->num_rows() > 0){//if there is a timeline data
            $this->db->select('id,task_date,start_time,end_time,task_description');
            $this->db->from('time_details');
            $this->db->where(array('task_id' => $task_id));
            $this->db->order_by('task_date,start_time','desc');
            $query = $this->db->get();
            $data = $query->result_array();
            $this->load->model('user_model');
            foreach($data as $d){
                $details['timeline_data'][] = array('table_id'=>$d['id'],'task_date'=>($d['task_date'])?$d['task_date']:date('Y-m-d'),'start_time'=>isset($d['start_time'])?$this->user_model->convert_date($d['start_time']):'','end_time'=>isset($d['end_time'])?$this->user_model->convert_date($d['end_time']):'','task_description'=>($d['task_description'])?$d['task_description']:null);
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
}
?>
