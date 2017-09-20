<?php

require_once("home.php"); // loading home controller

class search_engine_index extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location'); 
    }

    public function index()
    {
        $this->search_engine_index();
    }

    public function search_engine_index()
    {
        $data['body'] = 'admin/search_engine_index/search_engine_index';
        $data['page_title'] = $this->lang->line("search engine index");
        $this->_viewcontroller($data);
    }
    

    public function search_engine_index_data()
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
            $this->session->set_userdata('search_engine_index_domain_name', $domain_name);
            $this->session->set_userdata('search_engine_index_from_date', $from_date);
            $this->session->set_userdata('search_engine_index_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('search_engine_index_domain_name');
        $search_from_date  = $this->session->userdata('search_engine_index_from_date');
        $search_to_date = $this->session->userdata('search_engine_index_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(checked_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(checked_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "search_engine_index";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function search_engine_index_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
        $is_google=$this->input->post('is_google', true);
        $is_bing=$this->input->post('is_bing', true);
        $is_yahoo=$this->input->post('is_yahoo', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=4,$request=count($url_array));
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
        
      
        $this->session->set_userdata('search_engine_index_bulk_total_search',count($url_array));
        $this->session->set_userdata('search_engine_index_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/search_engine_index/search_engine_index_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";
        if($is_google==1) $write_data[]="Google Index";
        if($is_bing==1) $write_data[]="Bing Index";
        if($is_yahoo==1) $write_data[]="Yahoo Index";
        $write_data[]="Checked at";            
        
        fputcsv($download_path, $write_data);
        
        $search_engine_index_complete=0;
     
        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td><td>Domain Name</td>";
        if($is_google==1)   $str.="<td>Google Status</td>";
        if($is_bing==1)     $str.="<td>Bing Index</td>";
        if($is_yahoo==1)    $str.="<td>Yahoo Index</td>";     
        $str.="</tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $google_index="";
            $bing_index="";
            $yahoo_index="";
            
            if($is_google==1)   $google_index   =   $this->web_common_report->GoogleIP($domain);
            if($is_bing==1)     $bing_index     =   $this->web_common_report->bing_index($domain,$proxy="");
            if($is_yahoo==1)    $yahoo_index    =   $this->web_common_report->yahoo_index($domain,$proxy="");
            
            $checked_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            if($is_google==1)   $write_data[]=$google_index;
            if($is_bing==1)     $write_data[]=$bing_index;
            if($is_yahoo==1)    $write_data[]=$yahoo_index;
            $write_data[]=$checked_at;
        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'checked_at'        => $checked_at
            );
            if($is_google==1)   $insert_data["google_index"]=$google_index;
            if($is_bing==1)     $insert_data["bing_index"]=$bing_index;
            if($is_yahoo==1)    $insert_data["yahoo_index"]=$yahoo_index;

            $count++;

            $str.= "<tr><td>".$count."</td><td>".$domain."</td>";
            if($is_google==1)   $str.="<td>".$google_index."</td>";      
            if($is_bing==1)     $str.="<td>".$bing_index."</td>";      
            if($is_yahoo==1)    $str.="<td>".$yahoo_index."</td>";       
            $str.="</tr>";
            
            $this->basic->insert_data('search_engine_index', $insert_data);    
            $search_engine_index_complete++;
            $this->session->set_userdata("search_engine_index_complete_search",$search_engine_index_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function search_engine_index_download()
    {
        $all=$this->input->post("all");
        $table = 'search_engine_index';
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
        $fp = fopen("download/search_engine_index/search_engine_index_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $head=array("Doamin","Google Index","Bing Index","Yahoo Index","Scanned at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
            $write_info['domain_name']  = $value['domain_name'];
            $write_info['google_index'] = $value['google_index'];
            $write_info['bing_index']   = $value['bing_index'];
            $write_info['yahoo_index']  = $value['yahoo_index'];
            $write_info['checked_at']   = $value['checked_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/search_engine_index/search_engine_index_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function search_engine_index_delete()
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
        $this->db->delete('search_engine_index');
    }


    
    public function bulk_search_engine_index_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('search_engine_index_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('search_engine_index_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

    

   

}
