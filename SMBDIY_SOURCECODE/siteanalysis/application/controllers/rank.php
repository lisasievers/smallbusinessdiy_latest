<?php

require_once("home.php"); // loading home controller

class rank extends Home
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

    }

    public function index()
    {
        $this->alexa_rank();
    }

    public function alexa_rank()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/ranking/alexa';
        $data['page_title'] = $this->lang->line("alexa rank");
        $this->_viewcontroller($data);
    }
    

    public function alexa_rank_data()
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
            $this->session->set_userdata('alexa_rank_domain_name', $domain_name);
            $this->session->set_userdata('alexa_rank_from_date', $from_date);
            $this->session->set_userdata('alexa_rank_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('alexa_rank_domain_name');
        $search_from_date  = $this->session->userdata('alexa_rank_from_date');
        $search_to_date = $this->session->userdata('alexa_rank_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["checked_at >="]= $search_from_date;
            } 
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["checked_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "alexa_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function alexa_rank_action()
    {
               
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
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
        
      
        $this->session->set_userdata('alexa_rank_bulk_total_search',count($url_array));
        $this->session->set_userdata('alexa_rank_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/alexa_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";            
        $write_data[]="Reach Rank";            
        $write_data[]="Country";            
        $write_data[]="Country Rank";            
        $write_data[]="Traffic Rank";                
        
        fputcsv($download_path, $write_data);
        
        $alexa_rank_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Reach Rank</td>
            <td>Country</td>
            <td>Country Rank</td>
            <td>Traffic Rank</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $alexa_data=array();
            $alexa_data=$this->web_common_report->get_alexa_rank($domain);  
            $checked_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$alexa_data["reach_rank"];
            $write_data[]=$alexa_data["country"];
            $write_data[]=$alexa_data["country_rank"];
            $write_data[]=$alexa_data["traffic_rank"];

            $reach_rank=$alexa_data["reach_rank"];
            $country=$alexa_data["country"];
            $country_rank=$alexa_data["country_rank"];
            $traffic_rank=$alexa_data["traffic_rank"];
        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'reach_rank'        => $reach_rank,
                'country'           => $country,
                'country_rank'      => $country_rank,
                'traffic_rank'      => $traffic_rank,
                'checked_at'        => $checked_at,
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$reach_rank."</td>
            <td>".$country."</td>
            <td>".$country_rank."</td>
            <td>".$traffic_rank."</td>
            </tr>";
            
            $this->basic->insert_data('alexa_info', $insert_data);
            $alexa_rank_complete++;
            $this->session->set_userdata("alexa_rank_complete_search",$alexa_rank_complete);        
        }
        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//   

        echo $str.="</table>";

    }

  

    public function alexa_rank_download()
    {
        $all=$this->input->post("all");
        $table = 'alexa_info';
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
        $fp = fopen("download/rank/alexa_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $head=array("Doamin","Reach Rank","Country","Country Rank","Traffic Rank","Checked at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['domain_name'] = $value['domain_name'];
            $write_info['google_status'] = $value['reach_rank'];
            $write_info['macafee_status'] = $value['country'];
            $write_info['avg_status'] = $value['country_rank'];
            $write_info['norton_status'] = $value['traffic_rank'];
            $write_info['checked_at'] = $value['checked_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/rank/alexa_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function alexa_rank_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        exit(); 

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
        $this->db->delete('alexa_info');
    }
   
    public function bulk_alexa_rank_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('alexa_rank_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('alexa_rank_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }








    public function alexa_rank_full()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/ranking/alexa_full';
        $data['page_title'] = $this->lang->line("alexa data");
        $this->_viewcontroller($data);
    }
    

    public function alexa_rank_full_data()
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
            $this->session->set_userdata('alexa_rank_full_domain_name', $domain_name);
            $this->session->set_userdata('alexa_rank_full_from_date', $from_date);
            $this->session->set_userdata('alexa_rank_full_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('alexa_rank_full_domain_name');
        $search_from_date  = $this->session->userdata('alexa_rank_full_from_date');
        $search_to_date = $this->session->userdata('alexa_rank_full_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["searched_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["searched_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "alexa_info_full";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function alexa_rank_full_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
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
        
      
        $this->session->set_userdata('alexa_rank_full_bulk_total_search',count($url_array));
        $this->session->set_userdata('alexa_rank_full_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/alexa_full_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";                 
        $write_data[]="Global Rank";                 
        $write_data[]="Traffic Rank Graph Link";                 
        $write_data[]="Country Rank";                 
        $write_data[]="Country";                 
        $write_data[]="Country Ranking Data";             
        $write_data[]="Bounce Rate";                 
        $write_data[]="Page View per Visitor";                 
        $write_data[]="Daily Time";                 
        $write_data[]="Visitor % From Search Engines";                 
        $write_data[]="Search Engine % Graph Link";                 
        $write_data[]="Keyword Data";                               
        $write_data[]="Upstream Data";                                
        $write_data[]="Total Linking In Site";                                
        $write_data[]="Linking In Data";                        
        $write_data[]="Subdomain Data";                          
        
        fputcsv($download_path, $write_data);
        
        $alexa_rank_full_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Status</td>
            <td>".$this->lang->line('more info')."</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $alexa_data_full=array();
            $alexa_data_full=$this->web_common_report->alexa_raw_data($domain);  
            $searched_at= date("Y-m-d H:i:s");

         
            $global_rank =$alexa_data_full["global_rank"];
            $country_rank =$alexa_data_full["country_rank"];
            $country =$alexa_data_full["country"];
            $traffic_rank_graph =$alexa_data_full["traffic_rank_graph"];
            
           
            $country_name =$alexa_data_full["country_name"];
            $country_percent_visitor =$alexa_data_full["country_percent_visitor"];
            $country_in_rank = $alexa_data_full["country_in_rank"];
            $country_data=array();                    
            if(is_array($country_name) && is_array($country_in_rank) && is_array($country_percent_visitor))
            {
                foreach($country_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $country_name) && array_key_exists($key, $country_in_rank) && array_key_exists($key, $country_percent_visitor))
                    $country_data[$key]=$country_name[$key]."( Rank : ".$country_in_rank[$key]." - Visitor : ".$country_percent_visitor[$key]." )";
                }
            }
            $country_data_str=implode(',', $country_data);            
            
            $bounce_rate =$alexa_data_full["bounce_rate"];
            $page_view_per_visitor =$alexa_data_full["page_view_per_visitor"];
            $daily_time_on_the_site =$alexa_data_full["daily_time_on_the_site"];
            $visitor_percent_from_searchengine =$alexa_data_full["visitor_percent_from_searchengine"];
            $search_engine_percentage_graph =$alexa_data_full["search_engine_percentage_graph"];
            
           
            $keyword_name =$alexa_data_full["keyword_name"];
            $keyword_percent_of_search_traffic =$alexa_data_full["keyword_percent_of_search_traffic"];
            $keyword_data=array();                    
            if(is_array($keyword_name) && is_array($keyword_percent_of_search_traffic))
            {
                foreach($keyword_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $keyword_name) && array_key_exists($key, $keyword_percent_of_search_traffic))
                    $keyword_data[$key]=$keyword_name[$key]."( Search Traffic : ".$keyword_percent_of_search_traffic[$key]." )";
                }
            }
            $keyword_data_str=implode(',', $keyword_data);
            
            
            $upstream_site_name =$alexa_data_full["upstream_site_name"];
            $upstream_percent_unique_visits =$alexa_data_full["upstream_percent_unique_visits"];
            $upstream_data=array();                    
            if(is_array($upstream_site_name) && is_array($upstream_percent_unique_visits))
            {
                foreach($upstream_site_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $upstream_site_name) && array_key_exists($key, $upstream_percent_unique_visits))
                    $upstream_data[$key]=$upstream_site_name[$key]."( Unique Visit : ".$upstream_percent_unique_visits[$key]." )";
                }
            }
            $upstream_data_str=implode(',', $upstream_data);
            
            
            $total_site_linking_in =$alexa_data_full["total_site_linking_in"];
            $linking_in_site_name =$alexa_data_full["linking_in_site_name"];
            $linking_in_site_address =$alexa_data_full["linking_in_site_address"];
            $linking_in_data=array();                    
            if(is_array($linking_in_site_name) && is_array($linking_in_site_address))
            {
                foreach($linking_in_site_name as $key=>$val)
                {                  
                   if(array_key_exists($key, $linking_in_site_name) && array_key_exists($key, $linking_in_site_address))
                   $linking_in_data[$key]=$linking_in_site_name[$key]."( ".$linking_in_site_address[$key]." )";
                }
            }
            $linking_in_data_str=implode(',', $linking_in_data);

            
            
            $subdomain_name =$alexa_data_full["subdomain_name"];
            $subdomain_percent_visitors=$alexa_data_full["subdomain_percent_visitors"];
            $subdomain_data=array();                    
            if(is_array($subdomain_name) && is_array($subdomain_percent_visitors))
            {
                foreach($subdomain_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $subdomain_name) && array_key_exists($key, $subdomain_percent_visitors))
                    $subdomain_data[$key]=$subdomain_name[$key]."( Visitor : ".$subdomain_percent_visitors[$key]." )";
                }
            }
            $subdomain_data_str=implode(',', $subdomain_data);

            $status=$alexa_data_full["status"];

                   
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$global_rank;
            $write_data[]=$traffic_rank_graph;
            $write_data[]=$country_rank;
            $write_data[]=$country;
            $write_data[]=$country_data_str;
            $write_data[]=$bounce_rate;
            $write_data[]=$page_view_per_visitor;
            $write_data[]=$daily_time_on_the_site;
            $write_data[]=$visitor_percent_from_searchengine;
            $write_data[]=$search_engine_percentage_graph;
            $write_data[]=$keyword_data_str;
            $write_data[]=$upstream_data_str;
            $write_data[]=$total_site_linking_in;
            $write_data[]=$linking_in_data_str;
            $write_data[]=$subdomain_data_str;
        
            if($status=="success") fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                 'user_id'                          => $this->user_id,
                 'searched_at'                      => $searched_at,
                 'domain_name'                      => $domain,
                 'global_rank'                      => $global_rank,     
                 'country_rank'                     => $country_rank,    
                 'country'                          => $country,     
                 'traffic_rank_graph'               => $traffic_rank_graph,  
                 'country_name'                     => json_encode($country_name),    
                 'country_percent_visitor'          => json_encode($country_percent_visitor),     
                 'country_in_rank'                  => json_encode($country_in_rank), 
                 'bounce_rate'                      => $bounce_rate,     
                 'page_view_per_visitor'            => $page_view_per_visitor,   
                 'daily_time_on_the_site'           => $daily_time_on_the_site,  
                 'visitor_percent_from_searchengine'=> $visitor_percent_from_searchengine,   
                 'search_engine_percentage_graph'   => $search_engine_percentage_graph, 
                 'keyword_name'                     => json_encode($keyword_name),     
                 'keyword_percent_of_search_traffic'=> json_encode($keyword_percent_of_search_traffic),   
                 'upstream_site_name'               => json_encode($upstream_site_name),  
                 'upstream_percent_unique_visits'   => json_encode($upstream_percent_unique_visits),  
                 'total_site_linking_in'            => $total_site_linking_in,   
                 'linking_in_site_name'             => json_encode($linking_in_site_name), 
                 'linking_in_site_address'          => json_encode($linking_in_site_address), 
                 'subdomain_name'                   => json_encode($subdomain_name),  
                 'subdomain_percent_visitors'       => json_encode($subdomain_percent_visitors),
                 'status'                           => $status 

            );

            
            $details_url="";
            if($status=="success")
            {

                $this->basic->insert_data('alexa_info_full', $insert_data);    
                $insert_id=$this->db->insert_id();                
                $details_url="<a target='_BLANK' class='btn btn-primary' href='".site_url().'rank/alexa_details/'.$insert_id."'><i class='fa fa-table'></i>".$this->lang->line('more info')."</a>";
            }

            $count++;

            $str.=
            "<tr>
            <td style='vertical-align:middle !important;'>".$count."</td>
            <td style='vertical-align:middle !important;'>".$domain."</td>
            <td style='vertical-align:middle !important;'>".$status."</td>
            <td>".$details_url."</td>
            </tr>";
            
            $alexa_rank_full_complete++;
            $this->session->set_userdata("alexa_rank_full_complete_search",$alexa_rank_full_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//
        echo $str.="</table>";

    }

    public function alexa_rank_full_download()
    {
        $all=$this->input->post("all");
        $table = 'alexa_info_full';
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
        $fp = fopen("download/rank/alexa_full_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $wite_data=array();
        $write_data[]="Domain";                 
        $write_data[]="Global Rank";                 
        $write_data[]="Traffic Rank Graph Link";                 
        $write_data[]="Country Rank";                 
        $write_data[]="Country";                 
        $write_data[]="Country Ranking Data";             
        $write_data[]="Bounce Rate";                 
        $write_data[]="Page View per Visitor";                 
        $write_data[]="Daily Time";                 
        $write_data[]="Visitor % From Search Engines";                 
        $write_data[]="Search Engine % Graph Link";                 
        $write_data[]="Keyword Data";                               
        $write_data[]="Upstream Data";                                
        $write_data[]="Total Linking In Site";                                
        $write_data[]="Linking In Data";                        
        $write_data[]="Subdomain Data";      
        $write_data[]="Searched at";      
                    
        fputcsv($fp, $write_data);
        $write_data = array();

        foreach ($info as  $alexa_data_full) 
        {
         
            $domain =$alexa_data_full["domain_name"];
            $global_rank =$alexa_data_full["global_rank"];
            $country_rank =$alexa_data_full["country_rank"];
            $country =$alexa_data_full["country"];
            $traffic_rank_graph =$alexa_data_full["traffic_rank_graph"];
           
            $country_name =json_decode($alexa_data_full["country_name"],true);
            $country_percent_visitor =json_decode($alexa_data_full["country_percent_visitor"],true);
            $country_in_rank = json_decode($alexa_data_full["country_in_rank"],true);
            
            $bounce_rate =$alexa_data_full["bounce_rate"];
            $page_view_per_visitor =$alexa_data_full["page_view_per_visitor"];
            $daily_time_on_the_site =$alexa_data_full["daily_time_on_the_site"];
            $visitor_percent_from_searchengine =$alexa_data_full["visitor_percent_from_searchengine"];
            $search_engine_percentage_graph =$alexa_data_full["search_engine_percentage_graph"];            
           
            $keyword_name =json_decode($alexa_data_full["keyword_name"],true);
            $keyword_percent_of_search_traffic =json_decode($alexa_data_full["keyword_percent_of_search_traffic"],true);
            
            $upstream_site_name =json_decode($alexa_data_full["upstream_site_name"],true);
            $upstream_percent_unique_visits =json_decode($alexa_data_full["upstream_percent_unique_visits"],true);
           
            $total_site_linking_in =$alexa_data_full["total_site_linking_in"];
            $linking_in_site_name =json_decode($alexa_data_full["linking_in_site_name"],true);
            $linking_in_site_address =json_decode($alexa_data_full["linking_in_site_address"],true);
                        
            $subdomain_name =json_decode($alexa_data_full["subdomain_name"],true);
            $subdomain_percent_visitors=json_decode($alexa_data_full["subdomain_percent_visitors"],true);
            $searched_at=$alexa_data_full["searched_at"];



            $country_data=array();                    
            if(is_array($country_name) && is_array($country_in_rank) && is_array($country_percent_visitor))
            {
                foreach($country_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $country_name) && array_key_exists($key, $country_in_rank) && array_key_exists($key, $country_percent_visitor))
                    $country_data[$key]=$country_name[$key]."( Rank : ".$country_in_rank[$key]." - Visitor : ".$country_percent_visitor[$key]." )";
                }
            }
            $country_data_str=implode(',', $country_data);
            
            $keyword_data=array();                    
            if(is_array($keyword_name) && is_array($keyword_percent_of_search_traffic))
            {
                foreach($keyword_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $keyword_name) && array_key_exists($key, $keyword_percent_of_search_traffic))
                    $keyword_data[$key]=$keyword_name[$key]."( Search Traffic : ".$keyword_percent_of_search_traffic[$key]." )";
                }
            }
            $keyword_data_str=implode(',', $keyword_data);
            

            $upstream_data=array();                    
            if(is_array($upstream_site_name) && is_array($upstream_percent_unique_visits))
            {
                foreach($upstream_site_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $upstream_site_name) && array_key_exists($key, $upstream_percent_unique_visits))
                    $upstream_data[$key]=$upstream_site_name[$key]."( Unique Visit : ".$upstream_percent_unique_visits[$key]." )";
                }
            }
            $upstream_data_str=implode(',', $upstream_data);

            $linking_in_data=array();                    
            if(is_array($linking_in_site_name) && is_array($linking_in_site_address))
            {
                foreach($linking_in_site_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $linking_in_site_name) && array_key_exists($key, $linking_in_site_address))
                    $linking_in_data[$key]=$linking_in_site_name[$key]."( ".$linking_in_site_address[$key]." )";
                }
            }
            $linking_in_data_str=implode(',', $linking_in_data);

            
            $subdomain_data=array();                    
            if(is_array($subdomain_name) && is_array($subdomain_percent_visitors))
            {
                foreach($subdomain_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $subdomain_name) && array_key_exists($key, $subdomain_percent_visitors))
                    $subdomain_data[$key]=$subdomain_name[$key]."( Visitor : ".$subdomain_percent_visitors[$key]." )";
                }
            }
            $subdomain_data_str=implode(',', $subdomain_data);

            
            $write_data[]=$domain;
            $write_data[]=$global_rank;
            $write_data[]=$traffic_rank_graph;
            $write_data[]=$country_rank;
            $write_data[]=$country;
            $write_data[]=$country_data_str;
            $write_data[]=$bounce_rate;
            $write_data[]=$page_view_per_visitor;
            $write_data[]=$daily_time_on_the_site;
            $write_data[]=$visitor_percent_from_searchengine;
            $write_data[]=$search_engine_percentage_graph;
            $write_data[]=$keyword_data_str;
            $write_data[]=$upstream_data_str;
            $write_data[]=$total_site_linking_in;
            $write_data[]=$linking_in_data_str;
            $write_data[]=$subdomain_data_str;
            $write_data[]=$searched_at;
            
            fputcsv($fp, $write_data);
        }

        fclose($fp);
        $file_name = "download/rank/alexa_full_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    public function alexa_rank_full_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        exit();

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
        $this->db->delete('alexa_info_full');
    }


    public function bulk_alexa_rank_full_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('alexa_rank_full_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('alexa_rank_full_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }


    public function alexa_details($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && (!in_array(4,$this->module_access) && !in_array(2,$this->module_access)))
        redirect('home/login_page', 'location');

        if(!$this->basic->is_exist("alexa_info_full",array("user_id"=>$this->user_id,"id"=>$id)))
        redirect('home/access_forbidden', 'location');   

        $data["alexa_data"]=$this->basic->get_data("alexa_info_full",array("where"=>array("user_id"=>$this->user_id,"id"=>$id)));

        $data['body'] = 'admin/ranking/alexa_details';
        $data['page_title'] = $this->lang->line("alexa data");
        $this->_viewcontroller($data);
    }







    public function dmoz_rank()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'admin/ranking/dmoz';
        $data['page_title'] = $this->lang->line("dmoz check");
        $this->_viewcontroller($data);
    }
    

    public function dmoz_rank_data()
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
            $this->session->set_userdata('dmoz_rank_domain_name', $domain_name);
            $this->session->set_userdata('dmoz_rank_from_date', $from_date);
            $this->session->set_userdata('dmoz_rank_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('dmoz_rank_domain_name');
        $search_from_date  = $this->session->userdata('dmoz_rank_from_date');
        $search_to_date = $this->session->userdata('dmoz_rank_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["checked_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["checked_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "dmoz_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function dmoz_rank_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);


        //************************************************//
        $status=$this->_check_usage($module_id=5,$request=count($url_array));
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
        
      
        $this->session->set_userdata('dmoz_rank_bulk_total_search',count($url_array));
        $this->session->set_userdata('dmoz_rank_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/dmoz_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";            
        $write_data[]="Is Listed";                
        
        fputcsv($download_path, $write_data);
        
        $dmoz_rank_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Is Listed</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $dmoz_data="";
            $dmoz_data=$this->web_common_report->dmoz_check($domain);  
            $checked_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$dmoz_data;

        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'listed_or_not'     => $dmoz_data,
                'checked_at'        => $checked_at
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$dmoz_data."</td>
            </tr>";
            
            $this->basic->insert_data('dmoz_info', $insert_data);
            $dmoz_rank_complete++;
            $this->session->set_userdata("dmoz_rank_complete_search",$dmoz_rank_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=5,$request=count($url_array));   
        //******************************// 
        echo $str.="</table>";

    }

  

    public function dmoz_rank_download()
    {
        $all=$this->input->post("all");
        $table = 'dmoz_info';
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
        $fp = fopen("download/rank/dmoz_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $head=array("Doamin","Is Listed","Checked at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['domain_name']   = $value['domain_name'];
            $write_info['listed_or_not'] = $value['listed_or_not'];
            $write_info['checked_at']    = $value['checked_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/rank/dmoz_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function dmoz_rank_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        exit();

        
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
        $this->db->delete('dmoz_info');
    }


    
    public function bulk_dmoz_rank_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('dmoz_rank_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('dmoz_rank_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }





    public function moz_rank()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'admin/ranking/moz';
        $data['page_title'] = $this->lang->line("moz check");
        $this->_viewcontroller($data);
    }
    

    public function moz_rank_data()
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
            $this->session->set_userdata('moz_rank_domain_name', $domain_name);
            $this->session->set_userdata('moz_rank_from_date', $from_date);
            $this->session->set_userdata('moz_rank_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('moz_rank_domain_name');
        $search_from_date  = $this->session->userdata('moz_rank_from_date');
        $search_to_date = $this->session->userdata('moz_rank_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['url like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["checked_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["checked_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "moz_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function moz_rank_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
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
        
      
        $this->session->set_userdata('moz_rank_bulk_total_search',count($url_array));
        $this->session->set_userdata('moz_rank_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/moz_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
                                             
        
        $write_data[]="URL";            
        $write_data[]="Subdomain Normalized";                
        $write_data[]="Subdomain Raw";                
        $write_data[]="URL Normalized";                
        $write_data[]="URL Raw";                
        $write_data[]="HTTP Status Code";                
        $write_data[]="Domain Authority";                
        $write_data[]="Page Authority";                
        $write_data[]="External Equity Links";                
        $write_data[]="Links";           
        
        fputcsv($download_path, $write_data);
        
        $moz_rank_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>URL</td>
            <td>Subdomain Normalized</td>
            <td>Subdomain Raw</td>
            <td>URL Normalized</td>
            <td>URL Raw</td>
            <td>HTTP Status Code</td>
            <td>Domain Authority</td>
            <td>Page Authority</td>
            <td>External Equity Links</td>
            <td>Links</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/   
			         
           /* 
		   	
			$domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);
			
			*/
			
			
            $moz_data=array();

            $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
            $moz_access_id="";
            $moz_secret_key="";
            if(count($config_data)>0)
            {
                $moz_access_id=$config_data[0]["moz_access_id"];
                $moz_secret_key=$config_data[0]["moz_secret_key"];
            }

            $moz_data=$this->web_common_report->get_moz_info($domain,$moz_access_id, $moz_secret_key);  
            $checked_at= date("Y-m-d H:i:s");
                  
            $mozrank_subdomain_normalized=$moz_data["mozrank_subdomain_normalized"];    
            $mozrank_subdomain_raw=$moz_data["mozrank_subdomain_raw"];  
            $mozrank_url_normalized=$moz_data["mozrank_url_normalized"];    
            $mozrank_url_raw=$moz_data["mozrank_url_raw"];  
            $http_status_code=$moz_data["http_status_code"];    
            $domain_authority=$moz_data["domain_authority"];    
            $page_authority=$moz_data["page_authority"];    
            $external_equity_links=$moz_data["external_equity_links"];  
            $links=$moz_data["links"];             


            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$mozrank_subdomain_normalized;
            $write_data[]=$mozrank_subdomain_raw;
            $write_data[]=$mozrank_url_normalized;
            $write_data[]=$mozrank_url_raw;
            $write_data[]=$http_status_code;
            $write_data[]=$domain_authority;
            $write_data[]=$page_authority;
            $write_data[]=$external_equity_links;
            $write_data[]=$links;

        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'                           => $this->user_id,
                'url'                               => $domain,
                'mozrank_subdomain_normalized'      => $mozrank_subdomain_normalized,
                'mozrank_subdomain_raw'             => $mozrank_subdomain_raw,
                'mozrank_url_normalized'            => $mozrank_url_normalized,
                'mozrank_url_raw'                   => $mozrank_url_raw,
                'http_status_code'                  => $http_status_code,
                'domain_authority'                  => $domain_authority,
                'page_authority'                    => $page_authority,
                'external_equity_links'             => $external_equity_links,
                'links'                             => $links,
                'checked_at'                        => $checked_at
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$mozrank_subdomain_normalized."</td>
            <td>".$mozrank_subdomain_raw."</td>
            <td>".$mozrank_url_normalized."</td>
            <td>".$mozrank_url_raw."</td>
            <td>".$http_status_code."</td>
            <td>".$domain_authority."</td>
            <td>".$page_authority."</td>
            <td>".$external_equity_links."</td>
            <td>".$links."</td>
            </tr>";
            
            $this->basic->insert_data('moz_info', $insert_data);
            $moz_rank_complete++;
            $this->session->set_userdata("moz_rank_complete_search",$moz_rank_complete);      

            // api can process one req per 10 sec
            sleep(10);  
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//
        echo $str.="</table>";

    }

  

    public function moz_rank_download()
    {
        $all=$this->input->post("all");
        $table = 'moz_info';
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
        $fp = fopen("download/rank/moz_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));


        $head=array("URL","Subdomain Normalized","Subdomain Raw","URL Normalized","URL Raw","HTTP Status Code","Domain Authority","Page Authority","External Equity Links","Links","Checked at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['url'] = $value['url'];
            $write_info['mozrank_subdomain_normalized'] = $value['mozrank_subdomain_normalized'];
            $write_info['mozrank_subdomain_raw'] = $value['mozrank_subdomain_raw'];
            $write_info['mozrank_url_normalized'] = $value['mozrank_url_normalized'];
            $write_info['mozrank_url_raw'] = $value['mozrank_url_raw'];
            $write_info['http_status_code'] = $value['http_status_code'];
            $write_info['domain_authority'] = $value['domain_authority'];
            $write_info['page_authority'] = $value['page_authority'];
            $write_info['external_equity_links'] = $value['external_equity_links'];
            $write_info['links'] = $value['links'];
            $write_info['checked_at'] = $value['checked_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/rank/moz_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function moz_rank_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        exit();

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
        $this->db->delete('moz_info');
    }


    
    public function bulk_moz_rank_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('moz_rank_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('moz_rank_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }




    public function google_page_rank()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'admin/ranking/google_page';
        $data['page_title'] = $this->lang->line("google page rank");
        $this->_viewcontroller($data);
    }
    

    public function google_page_rank_data()
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
            $this->session->set_userdata('google_page_rank_domain_name', $domain_name);
            $this->session->set_userdata('google_page_rank_from_date', $from_date);
            $this->session->set_userdata('google_page_rank_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('google_page_rank_domain_name');
        $search_from_date  = $this->session->userdata('google_page_rank_from_date');
        $search_to_date = $this->session->userdata('google_page_rank_to_date');

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
        $table = "search_engine_page_rank";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function google_page_rank_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
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
        
      
        $this->session->set_userdata('google_page_rank_bulk_total_search',count($url_array));
        $this->session->set_userdata('google_page_rank_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/google_page_rank_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";            
        $write_data[]="Google Page Rank";                
        
        fputcsv($download_path, $write_data);
        
        $google_page_rank_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Google Page Rank</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $google_page_rank="";
            $google_page_rank=$this->web_common_report->get_google_page_rank($domain);  
            if($google_page_rank=="") $google_page_rank="0";
            $checked_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$google_page_rank;

        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'google_page_rank'  => $google_page_rank,
                'checked_at'        => $checked_at
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$google_page_rank."</td>
            </tr>";
            
            $this->basic->insert_data('search_engine_page_rank', $insert_data);    
            $google_page_rank_complete++;
            $this->session->set_userdata("google_page_rank_complete_search",$google_page_rank_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function google_page_rank_download()
    {
        $all=$this->input->post("all");
        $table = 'search_engine_page_rank';
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
        $fp = fopen("download/rank/google_page_rank_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $head=array("Doamin","Google Page Rank","Checked at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['domain_name']      = $value['domain_name'];
            $write_info['google_page_rank'] = $value['google_page_rank'];
            $write_info['checked_at']       = $value['checked_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/rank/google_page_rank_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function google_page_rank_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        exit();

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
        $this->db->delete('search_engine_page_rank');
    }


    
    public function bulk_google_page_rank_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('google_page_rank_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('google_page_rank_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }




    public function similar_web()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = 'admin/ranking/similar_web';
        $data['page_title'] = $this->lang->line("similarweb data");
        $this->_viewcontroller($data);
    }
    

    public function similar_web_data()
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
            $this->session->set_userdata('similar_web_domain_name', $domain_name);
            $this->session->set_userdata('similar_web_from_date', $from_date);
            $this->session->set_userdata('similar_web_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('similar_web_domain_name');
        $search_from_date  = $this->session->userdata('similar_web_from_date');
        $search_to_date = $this->session->userdata('similar_web_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["searched_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["searched_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "similar_web_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function similar_web_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
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
        
      
        $this->session->set_userdata('similar_web_bulk_total_search',count($url_array));
        $this->session->set_userdata('similar_web_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/rank/similar_web_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
      
        /**Write header in csv file***/
        $write_data[]="Domain";                 
        $write_data[]="Global Rank";                             
        $write_data[]="Country Rank";                 
        $write_data[]="Country";                          
        $write_data[]="Total Visit";                          
        $write_data[]="Time On Site";                          
        $write_data[]="Page Views";                          
        $write_data[]="Bounce Rate";                          
        $write_data[]="Country Traffic Data";                          
        $write_data[]="Direct Traffic";                          
        $write_data[]="Referral Traffic";                          
        $write_data[]="Search Traffic";                          
        $write_data[]="Social Traffic";                          
        $write_data[]="Mail Traffic";                          
        $write_data[]="Display Traffic";                          
        $write_data[]="Top Referral Site";                          
        $write_data[]="Top Destination Site";                          
        $write_data[]="Organic Search %";                          
        $write_data[]="Paid Search %";                          
        $write_data[]="Top Organic Keyword";                          
        $write_data[]="Top Paid Keyword";                          
        $write_data[]="Social Media Data";                          
        
        fputcsv($download_path, $write_data);
        
        $similar_web_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Status</td>
            <td>".$this->lang->line("more info")."</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $similar_web_data=array();
            $similar_web_data=$this->web_common_report->similar_web_raw_data($domain);  
            $searched_at= date("Y-m-d H:i:s");

         
            $global_rank =$similar_web_data["global_rank"];
            $country_rank =$similar_web_data["country_rank"];
            $country =$similar_web_data["country"];

            $category_rank=$similar_web_data["category_rank"];
			$category=$similar_web_data["category"];
			$total_visit=$similar_web_data["total_visit"];
			$time_on_site=$similar_web_data["time_on_site"];
			$page_views=$similar_web_data["page_views"];
			$bounce=$similar_web_data["bounce"];
			
			$traffic_country=$similar_web_data["traffic_country"];
			$traffic_country_percentage=$similar_web_data["traffic_country_percentage"];

            $traffic_country_data=array();
            $traffic_country_data_str="";
            if(is_array($traffic_country) && is_array($traffic_country_percentage))
            {
                foreach($traffic_country as $key=>$val)
                {                  
                    if(array_key_exists($key, $traffic_country) && array_key_exists($key, $traffic_country_percentage))
                    $traffic_country_data[$key]=$traffic_country[$key]."( Traffic % : ".$traffic_country_percentage[$key]." )";
                }
            }
            $traffic_country_data_str=implode(',', $traffic_country_data);
			
			$direct_traffic=$similar_web_data["direct_traffic"];
			$referral_traffic=$similar_web_data["referral_traffic"];
			$search_traffic=$similar_web_data["search_traffic"];
			$social_traffic=$similar_web_data["social_traffic"];
			$mail_traffic=$similar_web_data["mail_traffic"];
			$display_traffic=$similar_web_data["display_traffic"];
			
			$top_referral_site=$similar_web_data["top_referral_site"];
			$top_referral_site_str=implode('|', $top_referral_site);
			$top_destination_site=$similar_web_data["top_destination_site"];
			$top_destination_site_str=implode('|', $top_destination_site);
			
			$organic_search_percentage=$similar_web_data["organic_search_percentage"];
			$paid_search_percentage=$similar_web_data["paid_search_percentage"];
			
			$top_organic_keyword=$similar_web_data["top_organic_keyword"];
			$top_organic_keyword_str=implode('|', $top_organic_keyword);
			$top_paid_keyword=$similar_web_data["top_paid_keyword"];
			$top_paid_keyword_str=implode('|', $top_paid_keyword);
			
			$social_site_name=$similar_web_data["social_site_name"];
			$social_site_percentage=$similar_web_data["social_site_percentage"];
			$social_site_data=array();
            $social_site_str="";
            if(is_array($social_site_name) && is_array($social_site_percentage))
            {
                foreach($social_site_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $social_site_name) && array_key_exists($key, $social_site_percentage))
                    $social_site_data[$key]=$social_site_name[$key]."( Traffic % : ".$social_site_percentage[$key]." )";
                }
            }
            $social_site_str=implode(',', $social_site_data);            
            
            $status=$similar_web_data["status"];
              
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$global_rank;
            $write_data[]=$country_rank;
            $write_data[]=$country;
            $write_data[]=$total_visit;
            $write_data[]=$time_on_site;
            $write_data[]=$page_views;
            $write_data[]=$bounce;
            $write_data[]=$traffic_country_data_str;
            $write_data[]=$direct_traffic;
            $write_data[]=$referral_traffic;
            $write_data[]=$search_traffic;
            $write_data[]=$social_traffic;
            $write_data[]=$mail_traffic;
            $write_data[]=$display_traffic;
            $write_data[]=$top_referral_site_str;
            $write_data[]=$top_destination_site_str;
            $write_data[]=$organic_search_percentage;
            $write_data[]=$paid_search_percentage;
            $write_data[]=$top_organic_keyword_str;
            $write_data[]=$top_paid_keyword_str;
            $write_data[]=$social_site_str;
                    
            if($status=="success") fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
  
            $insert_data=array
            (
                 'user_id'                          => $this->user_id,
                 'searched_at'                      => $searched_at,
                 'domain_name'                      => $domain,
                 'global_rank'                      => $global_rank,     
                 'country_rank'                     => $country_rank,    
                 'country'                          => $country,
                 'category'							=> $category,
                 'category_rank'					=> $category_rank,
                 'total_visit'						=> $total_visit,
                 'time_on_site'						=> $time_on_site,
                 'page_views'						=> $page_views,
                 'bounce_rate'						=> $bounce,
                 'traffic_country'					=> json_encode($traffic_country),
                 'traffic_country_percentage'		=> json_encode($traffic_country_percentage),
                 'direct_traffic'					=> $direct_traffic,
                 'referral_traffic'					=> $referral_traffic,
                 'search_traffic'					=> $search_traffic,
                 'social_traffic'					=> $social_traffic,
                 'mail_traffic'						=> $mail_traffic,
                 'display_traffic'					=> $display_traffic,
                 'top_referral_site'				=> json_encode($top_referral_site),
                 'top_destination_site'				=> json_encode($top_destination_site),
                 'organic_search_percentage'		=> $organic_search_percentage,
                 'paid_search_percentage'			=> $paid_search_percentage,
                 'top_organic_keyword'				=> json_encode($top_organic_keyword),
                 'top_paid_keyword'					=> json_encode($top_paid_keyword),
                 'social_site_name'					=> json_encode($social_site_name),
                 'social_site_percentage'			=> json_encode($social_site_percentage),
                 'status'                           => $status 
            );
            
            $details_url="";
            if($status=="success")
            {
                $this->basic->insert_data('similar_web_info', $insert_data);    
                $insert_id=$this->db->insert_id();                
                $details_url="<a target='_BLANK' class='btn btn-primary' href='".site_url().'rank/similar_web_details/'.$insert_id."'><i class='fa fa-table'></i> ".$this->lang->line("more info")."</a>";
            }

            $count++;

            $str.=
            "<tr>
            <td style='vertical-align:middle !important;'>".$count."</td>
            <td style='vertical-align:middle !important;'>".$domain."</td>
            <td style='vertical-align:middle !important;'>".$status."</td>
            <td>".$details_url."</td>
            </tr>";
            
            $similar_web_complete++;
            $this->session->set_userdata("similar_web_complete_search",$similar_web_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=4,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

    public function similar_web_download()
    {
        $all=$this->input->post("all");
        $table = 'similar_web_info';
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
        $fp = fopen("download/rank/similar_web_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $wite_data=array();
        $write_data[]="Domain";                 
        $write_data[]="Global Rank";                             
        $write_data[]="Country Rank";                 
        $write_data[]="Country";                          
        $write_data[]="Total Visit";                          
        $write_data[]="Time On Site";                          
        $write_data[]="Page Views";                          
        $write_data[]="Bounce Rate";                          
        $write_data[]="Country Traffic Data";                          
        $write_data[]="Direct Traffic";                          
        $write_data[]="Referral Traffic";                          
        $write_data[]="Search Traffic";                          
        $write_data[]="Social Traffic";                          
        $write_data[]="Mail Traffic";                          
        $write_data[]="Display Traffic";                          
        $write_data[]="Top Referral Site";                          
        $write_data[]="Top Destination Site";                          
        $write_data[]="Organic Search %";                          
        $write_data[]="Paid Search %";                          
        $write_data[]="Top Organic Keyword";                          
        $write_data[]="Top Paid Keyword";                          
        $write_data[]="Social Media Data";        
        $write_data[]="Searched at";      
                    
        fputcsv($fp, $write_data);
        $write_data = array();

        foreach ($info as  $similar_web_data) 
        {         
            $domain =$similar_web_data["domain_name"];
            $global_rank =$similar_web_data["global_rank"];
            $country_rank =$similar_web_data["country_rank"];
            $country =$similar_web_data["country"];

            $category_rank=$similar_web_data["category_rank"];
			$category=$similar_web_data["category"];
			$total_visit=$similar_web_data["total_visit"];
			$time_on_site=$similar_web_data["time_on_site"];
			$page_views=$similar_web_data["page_views"];
			$bounce=$similar_web_data["bounce_rate"];
			
			$traffic_country=json_decode($similar_web_data["traffic_country"],true);
			$traffic_country_percentage=json_decode($similar_web_data["traffic_country_percentage"],true);

            $traffic_country_data=array();
            $traffic_country_data_str="";
            if(is_array($traffic_country) && is_array($traffic_country_percentage))
            {
                foreach($traffic_country as $key=>$val)
                {                  
                    if(array_key_exists($key, $traffic_country) && array_key_exists($key, $traffic_country_percentage))
                    $traffic_country_data[$key]=$traffic_country[$key]."( Traffic % : ".$traffic_country_percentage[$key]." )";
                }
            }
            $traffic_country_data_str=implode(',', $traffic_country_data);
			
			$direct_traffic=$similar_web_data["direct_traffic"];
			$referral_traffic=$similar_web_data["referral_traffic"];
			$search_traffic=$similar_web_data["search_traffic"];
			$social_traffic=$similar_web_data["social_traffic"];
			$mail_traffic=$similar_web_data["mail_traffic"];
			$display_traffic=$similar_web_data["display_traffic"];
			
			$top_referral_site=json_decode($similar_web_data["top_referral_site"],true);
			$top_referral_site_str=implode('|', $top_referral_site);
			$top_destination_site=json_decode($similar_web_data["top_destination_site"],true);
			$top_destination_site_str=implode('|', $top_destination_site);
			
			$organic_search_percentage=$similar_web_data["organic_search_percentage"];
			$paid_search_percentage=$similar_web_data["paid_search_percentage"];
			
			$top_organic_keyword=json_decode($similar_web_data["top_organic_keyword"],true);
			$top_organic_keyword_str=implode('|', $top_organic_keyword);
			$top_paid_keyword=json_decode($similar_web_data["top_paid_keyword"],true);
			$top_paid_keyword_str=implode('|', $top_paid_keyword);
			
			$social_site_name=json_decode($similar_web_data["social_site_name"],true);
			$social_site_percentage=json_decode($similar_web_data["social_site_percentage"],true);
			$social_site_data=array();
            $social_site_str="";
            if(is_array($social_site_name) && is_array($social_site_percentage))
            {
                foreach($social_site_name as $key=>$val)
                {                  
                    if(array_key_exists($key, $social_site_name) && array_key_exists($key, $social_site_percentage))
                    $social_site_data[$key]=$social_site_name[$key]."( Traffic % : ".$social_site_percentage[$key]." )";
                }
            }
            $social_site_str=implode(',', $social_site_data);            
            
            $searched_at=$similar_web_data["searched_at"];
            
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$global_rank;
            $write_data[]=$country_rank;
            $write_data[]=$country;
            $write_data[]=$total_visit;
            $write_data[]=$time_on_site;
            $write_data[]=$page_views;
            $write_data[]=$bounce;
            $write_data[]=$traffic_country_data_str;
            $write_data[]=$direct_traffic;
            $write_data[]=$referral_traffic;
            $write_data[]=$search_traffic;
            $write_data[]=$social_traffic;
            $write_data[]=$mail_traffic;
            $write_data[]=$display_traffic;
            $write_data[]=$top_referral_site_str;
            $write_data[]=$top_destination_site_str;
            $write_data[]=$organic_search_percentage;
            $write_data[]=$paid_search_percentage;
            $write_data[]=$top_organic_keyword_str;
            $write_data[]=$top_paid_keyword_str;
            $write_data[]=$social_site_str;
            $write_data[]=$searched_at;
            
            fputcsv($fp, $write_data);
        }

        fclose($fp);
        $file_name = "download/rank/similar_web_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    public function similar_web_delete()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        exit();

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
        $this->db->delete('similar_web_info');
    }


    public function bulk_similar_web_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('similar_web_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('similar_web_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }


    public function similar_web_details($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(4,$this->module_access))
        redirect('home/login_page', 'location'); 

        if(!$this->basic->is_exist("similar_web_info",array("user_id"=>$this->user_id,"id"=>$id)))
        redirect('home/access_forbidden', 'location');   

        $data["similar_web"]=$this->basic->get_data("similar_web_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$id)));

        $data['body'] = 'admin/ranking/similar_web_details';
        $data['page_title'] = $this->lang->line("similarweb data");
        $this->_viewcontroller($data);
    }

    

   

}
