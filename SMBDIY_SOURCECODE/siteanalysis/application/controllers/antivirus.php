<?php

require_once("home.php"); // loading home controller

class antivirus extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
 
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        $this->member_validity();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(10,$this->module_access))
        redirect('home/login_page', 'location'); 

    }

    public function index()
    {
        $this->scan();
    }

    public function scan()
    {
        $data['body'] = 'admin/antivirus/antivirus_scan';
        $data['page_title'] = $this->lang->line('malware scan');
        $this->_viewcontroller($data);
    }
    

    public function scan_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('antivirus_scan_domain_name', $domain_name);
            $this->session->set_userdata('antivirus_scan_from_date', $from_date);
            $this->session->set_userdata('antivirus_scan_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('antivirus_scan_domain_name');
        $search_from_date  = $this->session->userdata('antivirus_scan_from_date');
        $search_to_date = $this->session->userdata('antivirus_scan_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["scanned_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["scanned_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "antivirus_scan_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function scan_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
        $is_google=$this->input->post('is_google', true);
        $is_mcafee=$this->input->post('is_mcafee', true);
        $is_avg=$this->input->post('is_avg', true);
        $is_norton=$this->input->post('is_norton', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=10,$request=count($url_array));
        if($status=="2") 
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        //************************************************//
        
      
        $this->session->set_userdata('antivirus_scan_bulk_total_search',count($url_array));
        $this->session->set_userdata('antivirus_scan_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/antivirus/antivirus_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";
        if($is_google==1) $write_data[]="Google Status";
        if($is_mcafee==1) $write_data[]="McAfee_status Status";
        if($is_avg==1) $write_data[]="AVG Status";
        if($is_norton==1) $write_data[]="Norton Status";
        $write_data[]="Scanned at";       
        
        fputcsv($download_path, $write_data);
        
        $antivirus_scan_complete=0;
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td><td>Domain Name</td>";
        if($is_google==1) $str.="<td>Google Status</td>";
        if($is_mcafee==1) $str.="<td>McAfee Status</td>";
        if($is_avg==1)    $str.="<td>AVG Status</td>";
        if($is_norton==1) $str.="<td>Norton Status</td>";      
        $str.="</tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);
            
            $google_status="";
            $macafee_status="";
            $avg_status="";
            $norton_status="";
            
            if($is_google==1) $google_status=$this->web_common_report->google_safety_check($api,$domain);
            if($is_mcafee==1) $macafee_status=$this->web_common_report->macafee_safety_analysis($domain,$proxy="");
            if($is_avg==1)    $avg_status=$this->web_common_report->avg_safety_check($domain,$proxy="");
            if($is_norton==1) $norton_status=$this->web_common_report->norton_safety_check($domain,$proxy="");   
            
            $scanned_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            if($is_google==1) $write_data[]=$google_status;
            if($is_mcafee==1) $write_data[]=$macafee_status;
            if($is_avg==1)    $write_data[]=$avg_status;
            if($is_norton==1) $write_data[]=$norton_status;
            $write_data[]=$scanned_at;
        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'scanned_at'        => $scanned_at
            );
            if($is_google==1) $insert_data["google_status"]=$google_status;
            if($is_mcafee==1) $insert_data["macafee_status"]=$macafee_status;
            if($is_avg==1)    $insert_data["avg_status"]=$avg_status;
            if($is_norton==1) $insert_data["norton_status"]=$norton_status;

            $count++;

            $str.= "<tr><td>".$count."</td><td>".$domain."</td>";
            if($is_google==1) $str.="<td>".$google_status."</td>";      
            if($is_mcafee==1) $str.="<td>".$macafee_status."</td>";      
            if($is_avg==1)    $str.="<td>".$avg_status."</td>";      
            if($is_norton==1) $str.="<td>".$norton_status."</td>";      
            $str.="</tr>";
            
            $this->basic->insert_data('antivirus_scan_info', $insert_data);    
            $antivirus_scan_complete++;
            $this->session->set_userdata("antivirus_scan_complete_search",$antivirus_scan_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=10,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function scan_download()
    {
        $all=$this->input->post("all");
        $table = 'antivirus_scan_info';
        $where=array();
        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }
            $where['where_in'] = array('id' => $id_array);
        }

        $where['where'] = array('user_id'=>$this->user_id);

        $info = $this->basic->get_data($table, $where, $select ='', $join='', $limit='', $start=null, $order_by='id asc');
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/antivirus/antivirus_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $head=array("Doamin","Google Status","McAfee Status","AVG status","Norton Status","Scanned at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
            $write_info['domain_name'] = $value['domain_name'];
            $write_info['google_status'] = $value['google_status'];
            $write_info['macafee_status'] = $value['macafee_status'];
            $write_info['avg_status'] = $value['avg_status'];
            $write_info['norton_status'] = $value['norton_status'];
            $write_info['scanned_at'] = $value['scanned_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/antivirus/antivirus_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function scan_delete()
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
        }
        $this->db->where('user_id', $this->user_id);
        $this->db->delete('antivirus_scan_info');
    }


    
    public function bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('antivirus_scan_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('antivirus_scan_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

    

   

}
