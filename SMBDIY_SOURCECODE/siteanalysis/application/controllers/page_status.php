<?php

require_once("home.php"); // loading home controller



class page_status extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) {
            redirect('home/login_page', 'location');
        }
        $this->load->library('upload');
        $this->load->library('web_common_report');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(7,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index()
    {
        $this->page_status_list();
    }

    public function page_status_list(){
        $data['body'] = 'admin/page_status/page_status_list';
        $data['page_title'] = $this->lang->line('page status check');
        $this->_viewcontroller($data);
    }

   public function page_status_list_data(){

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
            $this->session->set_userdata('page_status_domain_name', $domain_name);
            $this->session->set_userdata('page_status_from_date', $from_date);
            $this->session->set_userdata('page_status_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('page_status_domain_name');
        $search_from_date  = $this->session->userdata('page_status_from_date');
        $search_to_date = $this->session->userdata('page_status_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['url like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(check_date ,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(check_date ,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "page_status";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);

   }

   public function page_status_action(){

        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);      
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);


        //************************************************//
        $status=$this->_check_usage($module_id=7,$request=count($url_array));
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
        
      
        $this->session->set_userdata('page_status_bulk_total_search',count($url_array));
        $this->session->set_userdata('page_status_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/page_status/page_status_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_domain[]="URL";
        $write_domain[]="HTTP Code";
        $write_domain[]="Status";
        $write_domain[]="Total Time (sec)";
        $write_domain[]="Name Lookup Time (sec)";
        $write_domain[]="Connect Time (sec)";
        $write_domain[]="Download Speed Time";
        $write_domain[]="Check Status Date";           
        
        fputcsv($download_path, $write_domain);

        $http_codes = array( 100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK',
         201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content',
         205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 300 => 'Multiple Choices',
         301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy',
         306 => 'Switch Proxy', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized',
         402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed',
         406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict',410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large',414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable',417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked',
         424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 449 => 'Retry With',
         450 => 'Blocked by Windows Parental Controls', 500 => 'Internal Server Error', 501 => 'Not Implemented',
         502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout',
         505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage',
         509 => 'Bandwidth Limit Exceeded', 510 => 'Not Extended',
         0 => 'Not Registered' );
        
        $page_status_complete=0;
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";
        $str.="<td>URL</td><td>HTTP Code</td><td>Status</td><td>Total Time (sec)</td><td>Name Lookup Time (sec)</td><td>Connect Time (sec)</td><td>Download Speed Time</td><td>Check Status Date</td>";             
        $str.="</tr>";

        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $time=date("Y-m-d H:i:s");
            $domain_info= $this->web_common_report->page_status_check($domain);
            $write_domain=array();
            $write_domain[]=$domain;
            $write_domain[]=$domain_info['http_code'];
            $write_domain[]=$http_codes[$domain_info['http_code']];
            $write_domain[]=$domain_info['total_time'];
            $write_domain[]=$domain_info['namelookup_time'];
            $write_domain[]=$domain_info['connect_time'];
            $write_domain[]=$domain_info['speed_download'];
            $write_domain[]=$time;
            fputcsv($download_path, $write_domain);
           
            $insert_data=array(
                                'url'        => $domain,
                                'user_id'    => $this->user_id,
                                'http_code'    => $domain_info['http_code'],
                                'status'    => $http_codes[$domain_info['http_code']],
                                'total_time'    => $domain_info['total_time'],
                                'namelookup_time'    =>$domain_info['namelookup_time'],
                                'connect_time' =>$domain_info['connect_time'],
                                'speed_download'    =>$domain_info['speed_download'],
                                'check_date'        => $time
                                );
            
            $this->basic->insert_data('page_status', $insert_data);    
            $page_status_complete++;
            $this->session->set_userdata("page_status_complete_search",$page_status_complete); 
            $count++;
            $str.= "<tr><td>".$count."</td><td>".$domain."</td>";
            $str.="<td>".$domain_info['http_code']."</td>";              
            $str.="<td>".$http_codes[$domain_info['http_code']]."</td>";              
            $str.="<td>".$domain_info['total_time']."</td>";              
            $str.="<td>".$domain_info['namelookup_time']."</td>";              
            $str.="<td>".$domain_info['connect_time']."</td>";              
            $str.="<td>".$domain_info['speed_download']."</td>";              
            $str.="<td>".$time."</td>";              
            $str.="</tr>";       
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=7,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";
   }

    public function page_status_download()
    {
        $all=$this->input->post("all");
        $table = 'page_status';
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
        $fp = fopen("download/page_status/page_status_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $head=array("Domain Name","HTTP Code","Status","Total Time","Name Lookup Time","Connect Time","Download Speed","Check At");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
            $write_info['url'] = $value['url'];
            $write_info['http_code'] = $value['http_code'];
            $write_info['status'] = $value['status'];
            $write_info['total_time'] = $value['total_time'];
            $write_info['namelookup_time'] = $value['namelookup_time'];
            $write_info['connect_time'] = $value['connect_time'];
            $write_info['speed_download'] = $value['speed_download'];
            $write_info['check_date'] = $value['check_date'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/page_status/page_status_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function page_status_delete()
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
        $this->db->delete('page_status');
    }


    
    public function bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('page_status_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('page_status_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

}
