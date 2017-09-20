<?php


require_once("home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class admin extends Home
{

    public $user_id;    

    
    public function __construct()
    {
        parent::__construct();
      /*  if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');        
        if ($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');
        */
        $this->load->helper('form');
        $this->load->library('upload');
        $this->upload_path = realpath(APPPATH . '../upload');
      //  $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

       // $this->important_feature();
       // $this->periodic_check();

    }


    public function index($base_site="")
    {
        $this->delete_junk_file();
    }




    public function scanAll($myDir)
    {

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $dirTree[$i]['size'] = $file_size;
            $dirTree[$i]['time'] =date("Y-m-d H:i:s",$file_modification_time);

            $i++;

        }

        return $dirTree;
    }


    public function delete_junk_file()
    {
        $path=FCPATH."download";
        $dirTree=$this->scanAll($path);

        $number_of_deleted_files = 0;
        $deleted_file_size = 0;
        $todate = date("Y-m-d");
        $previous_day = date("Y-m-d", strtotime("$todate - 1 Days"));
        $last_time = strtotime($previous_day);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time && $value['file']!='index.html'){
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }
        if($deleted_file_size != 0) $data['total_file_size'] = number_format($deleted_file_size/1024, 2);
        else $data['total_file_size'] = $deleted_file_size;

        $data['total_files'] = $number_of_deleted_files;
        $data['body'] = "admin/junk_file_delete/delete_junk_file";
        $data['page_title'] = $this->lang->line("delete junk files/data");
        $this->_viewcontroller($data);      
        

   }

   public function delete_junk_file_action()
   {
       $path=FCPATH."download";
       $dirTree=$this->scanAll($path);
       $number_of_deleted_files = 0;
       $deleted_file_size = 0;
       $todate = date("Y-m-d");
       $previous_day = date("Y-m-d", strtotime("$todate - 1 Days"));
       $last_time = strtotime($previous_day);
       foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
           if($junk_file_created_time <= $last_time && $value['file']!='index.html'){
                unlink($value['file']);
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
           }
       }
       if($deleted_file_size != 0) $deleted_file_size = number_format($deleted_file_size/1024, 2);
       else $deleted_file_size = $deleted_file_size;
       echo "<p class='text-danger'>".$number_of_deleted_files." ".$this->lang->line('junk files have been deleted successfully')." ( ".$deleted_file_size." KB )</p>";
   }


   public function delete_junk_data()
   {
        $current_date = date("Y-m-d");
        $this->basic->delete_data("page_search_guest",array("searched_at < "=>$current_date));
        $this->db->last_query();
        echo "<p class='text-danger'>".$this->lang->line('unneccessary guest search history have been deleted successfully')."</p>";
   }



   public function recent_check_report()
   {
        $data['body'] = 'admin/recent_check_report/recent_check_report_grid';
        $data['page_title'] = $this->lang->line("site health report");
         $data['username'] = get_cookie('name');
        $this->_viewcontroller($data);
   }

   public function recent_check_report_data()
   {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');    

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));
        $email = trim($this->input->post("email", true));
        $with_or_without = trim($this->input->post("with_or_without", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('single_report_domain_name', $domain_name);
            $this->session->set_userdata('single_report_with_or_without', $with_or_without);
            $this->session->set_userdata('single_report_email', $email);
            $this->session->set_userdata('single_report_from_date', $from_date);
            $this->session->set_userdata('single_report_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('single_report_domain_name');
        $search_with_or_without   = $this->session->userdata('single_report_with_or_without');
        $search_email   = $this->session->userdata('single_report_email');
        $search_from_date  = $this->session->userdata('single_report_from_date');
        $search_to_date = $this->session->userdata('single_report_to_date');

        // creating a blank where_simple array
        $where_simple=array();

        if ($search_with_or_without == "with"){
            $where_simple['email != '] = '';
        }
        if ($search_with_or_without == "without"){
            $where_simple['email = '] = '';
        }
        if ($search_email)    $where_simple['email like '] = "%".$search_email."%";
        if ($search_domain_name)    $where_simple['domain_name like '] = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["searched_at >="]= $search_from_date." 00:00:00";
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["searched_at <="]=$search_to_date." 23:59:59";
            
        }
        //$where_compare["comparision.user_id ="]=$userid;
            $userid = get_cookie('user_id');
            $where_simple["user_id ="]=$userid;

        $where  = array('where'=>$where_simple);
       // print_r($where);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "site_check_report";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $i = 0;
        foreach($info as $results){
            $result[$i]['id'] = $results['id'];
            $result[$i]['domain_name'] = $results['domain_name'];
            $result[$i]['searched_at'] = $results['searched_at'];

            if($results['email'] != ''){
              $emails_array = explode(',', $results['email']);
              $emails = array_unique($emails_array);
              $emails = implode(',', $emails);
              $result[$i]['email'] = "<div style='cursor:pointer;' class='email_list label label-success' data='".$emails."'><i class='fa fa-envelope'></i> ".$this->lang->line("email list")."</div>";
            } else {
              $result[$i]['email'] = $results['email'];
            }
            
            $result[$i]['details'] = "<a target='_blank' class='label label-warning' href='".base_url()."health_check/report/".$results['id']."'><i class='fa fa-file'></i> ".$this->lang->line('report')."</a>";
            $i++;
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($result, $total_result);
   }


   public function recent_check_report_delete()
   {
        $all=$this->input->post("all");

        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }     
            $this->db->where_in('id', $id_array);
        } else $this->db->where("id >",0);

        $this->db->delete('site_check_report');

        if($all==0){
            $this->db->where_in('base_site',$id_array);
            $this->db->or_where_in('competutor_site',$id_array);
        } else $this->db->where("id >",0);
        $this->db->delete('comparision');
   }


   public function comparative_check_report()
   {
        $data['body'] = 'admin/comparative_check_report/comparative_check_report_grid';
        $data['page_title'] = $this->lang->line("comparitive health report");
         $data['username'] = get_cookie('name');
        $this->_viewcontroller($data);
   }


   public function comparative_check_report_data()
   {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');    

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));
        $compare_domain_name = trim($this->input->post("compare_domain_name", true));
        $email = trim($this->input->post("email", true));
        $with_or_without = trim($this->input->post("with_or_without", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('competutor_report_domain_name', $domain_name);
            $this->session->set_userdata('competutor_report_competutor_domain_name', $compare_domain_name);
            $this->session->set_userdata('competutor_report_with_or_without', $with_or_without);
            $this->session->set_userdata('competutor_report_email', $email);
            $this->session->set_userdata('competutor_report_from_date', $from_date);
            $this->session->set_userdata('competutor_report_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('competutor_report_domain_name');
        $search_competutor_domain_name   = $this->session->userdata('competutor_report_competutor_domain_name');
        $search_with_or_without   = $this->session->userdata('competutor_report_with_or_without');
        $search_email   = $this->session->userdata('competutor_report_email');
        $search_from_date  = $this->session->userdata('competutor_report_from_date');
        $search_to_date = $this->session->userdata('competutor_report_to_date');

        // creating a blank where_simple array
        $where_simple=array();

        if ($search_with_or_without == "with"){
            $where_simple['comparision.email != '] = '';
        }
        if ($search_with_or_without == "without"){
            $where_simple['comparision.email = '] = '';
        }
        if ($search_email)    $where_simple['comparision.email like '] = "%".$search_email."%";

        if ($search_domain_name)    $where_simple['base_site_table.domain_name like '] = "%".$search_domain_name."%";
        
        if ($search_competutor_domain_name)    $where_simple['competutor_site_table.domain_name like '] = "%".$search_competutor_domain_name."%";
        
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["searched_at >="]= $search_from_date." 00:00:00";
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["searched_at <="]=$search_to_date." 23:59:59";
            
        }
        $userid = get_cookie('user_id');
        $where_simple["comparision.user_id ="]=$userid;
        $where  = array('where'=>$where_simple);
        $join=array('site_check_report as base_site_table'=>"base_site_table.id=comparision.base_site,left",'site_check_report as competutor_site_table'=>"competutor_site_table.id=comparision.competutor_site,left");
        $select=array("base_site_table.domain_name as base_domain","competutor_site_table.domain_name as competutor_domain","comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.email","comparision.id as id");
            
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "comparision";
        $info = $this->basic->get_data($table, $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $i = 0;
        foreach($info as $results){
            $result[$i]['id'] = $results['id'];
            $result[$i]['base_domain'] = $results['base_domain'];
            $result[$i]['competutor_domain'] = $results['competutor_domain'];
            $result[$i]['searched_at'] = $results['searched_at'];

            if($results['email'] != ''){
              $emails_array = explode(',', $results['email']);
              $emails = array_unique($emails_array);
              $emails = implode(',', $emails);
              $result[$i]['email'] = "<div style='cursor:pointer;' class='email_list label label-success' data='".$emails."'><i class='fa fa-envelope'></i> ".$this->lang->line("email list")."</div>";
            } else {
              $result[$i]['email'] = $results['email'];
            }
            
            $result[$i]['details'] = "<a target='_blank' class='label label-warning' href='".base_url()."health_check/comparison_report/".$results['id']."'><i class='fa fa-file'></i> ".$this->lang->line('report')."</a>";
            $i++;
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count="comparision.id", $join);
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($result, $total_result);
   }


   public function comparative_check_report_delete()
   {
        $all=$this->input->post("all");

        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }

            $where['where_in'] = array('id'=>$id_array);
            $site_check_table_ids = $this->basic->get_data('comparision',$where);
            $site_check_delete_ids = array();
            foreach($site_check_table_ids as $ids_to_delete){
                array_push($site_check_delete_ids, $ids_to_delete['base_site']);
                array_push($site_check_delete_ids, $ids_to_delete['competutor_site']);
            } 

            $this->db->where_in('id', $id_array);
        } else $this->db->where("id >",0);

        $this->db->delete('comparision');

        if($all==0){
            $this->db->where_in('id',$site_check_delete_ids);
        } else $this->db->where("id >",0);
        $this->db->delete('site_check_report');
   }

   

}
