<?php

require_once("home.php"); // loading home controller



class social extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(3,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index()
    {
        $this->social_list();
    }

    public function social_list()
    {
        $data['body'] = 'admin/social/social_network_analysis';
        $data['page_title'] = $this->lang->line("social network analysis");
        $this->_viewcontroller($data);
    }
    

    public function social_list_data()
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
            $this->session->set_userdata('social_analysis_domain_name', $domain_name);
            $this->session->set_userdata('social_analysis_from_date', $from_date);
            $this->session->set_userdata('social_analysis_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('social_analysis_domain_name');
        $search_from_date  = $this->session->userdata('social_analysis_from_date');
        $search_to_date = $this->session->userdata('social_analysis_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["search_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
               $search_to_date = $search_to_date." 23:59:59"; 
                $where_simple["search_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "social_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        // echo $this->db->last_query(); exit();
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function social_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);

      /* if(is_facebook==0 && is_linkedin==0 && is_googleplus==0 && is_xing==0 && is_reddit==0 && is_pinterest==0 && is_buffer==0 && is_stumbleupon==0);*/

        $is_facebook=$this->input->post('is_facebook', true);
        $is_linkedin=$this->input->post('is_linkedin', true);
        $is_googleplus=$this->input->post('is_googleplus', true);
        $is_xing=$this->input->post('is_xing', true);

        $is_reddit=$this->input->post('is_reddit', true);
        $is_pinterest=$this->input->post('is_pinterest', true);
        $is_buffer=$this->input->post('is_buffer', true);
        $is_stumbleupon=$this->input->post('is_stumbleupon', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=3,$request=count($url_array));
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
        
      
        $this->session->set_userdata('social_analysis_bulk_total_search',count($url_array));
        $this->session->set_userdata('social_analysis_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/social/social_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";
        if($is_facebook==1) {
            // $write_data[]="Facebook Like";
            $write_data[]="Facebook Share";
            // $write_data[]="Facebook Comment";
        }
        if($is_googleplus==1) $write_data[]="Google+ Count";
        if($is_linkedin==1) $write_data[]="Linkedin Share";
        if($is_xing==1) $write_data[]="Xing Share"; 

        if($is_reddit==1) {
            $write_data[]="Reddit Score";
            $write_data[]="Reddit Up";
            $write_data[]="Reddit Down";
        }

        if($is_pinterest==1) $write_data[]="Pinterest Pin";
        if($is_buffer==1) $write_data[]="Buffer Share";

        if($is_stumbleupon==1) {
            $write_data[]="StumbleUpon View";
            $write_data[]="StumbleUpon Like";
            $write_data[]="StumbleUpon Comment";
            $write_data[]="StumbleUpon List";
        }


        $write_data[]="Search at";            
        
        fputcsv($download_path, $write_data);
        
        $social_analysis_complete=0;
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

 
        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td><td>Domain Name</td>";
        // if($is_facebook==1) $str.="<td>Facebook Like</td><td>Facebook Share</td><td>Facebook Comment</td>";
        if($is_facebook==1) $str.="<td>Facebook Share</td>";
        if($is_googleplus==1) $str.="<td>Google+ Count</td>";
        if($is_linkedin==1)    $str.="<td>Linkedin Share</td>";
        if($is_xing==1) $str.="<td>Xing Share</td>";
        if($is_reddit==1) $str.="<td>Reddit Score</td><td>Reddit Up</td><td>Reddit Down</td>";
        if($is_pinterest==1)    $str.="<td>Pinterest Pin</td>";
        if($is_buffer==1) $str.="<td>Buffer Share</td>";      
        if($is_stumbleupon==1) $str.="<td>StumbleUpon View</td><td>StumbleUpon Like</td><td>StumbleUpon Comment</td><td>StumbleUpon List</td>";      
        $str.="</tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain_org=$domain;
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);
           
            
            if($is_facebook==1) $facebook_report=$this->web_common_report->fb_like_comment_share(addHttp($domain_org));
            if($is_googleplus==1) $googleplus_report=$this->web_common_report->get_plusones($domain);
            if($is_linkedin==1)    $linkedin_report=$this->web_common_report->linkdin_share($domain);
            if($is_xing==1) $xing_report=$this->web_common_report->xing_share_count($domain); 

            if($is_reddit==1) $reddit_report=$this->web_common_report->reddit_count($domain);   
            if($is_pinterest==1) $pinterest_report=$this->web_common_report->pinterest_pin($domain);   
            if($is_buffer==1) $buffer_report=$this->web_common_report->buffer_share($domain);   
            if($is_stumbleupon==1) $stumbleupon_report=$this->web_common_report->stumbleupon_info($domain);   
            
            $searched_at= date("Y-m-d H:i:s");
                  
            $write_data=array();


            $write_data[]=$domain;

            if($is_facebook==1)
            {
                // foreach ($facebook_report as $value) {
                //    $write_data[] = $value;
                // }
                $write_data[] = $facebook_report["total_share"];                
                
            }

            if($is_googleplus==1){               
                   $write_data[] = $googleplus_report;                
            }

            if($is_linkedin==1){                
                   $write_data[] = $linkedin_report;             
            }

            if($is_xing==1){               
                   $write_data[] = $xing_report;               
            }

             if($is_reddit==1){
                foreach ($reddit_report as $value) {
                   $write_data[] = $value;
                }
            }

            if($is_pinterest==1){
               
                   $write_data[] = $pinterest_report;
             
            }

            if($is_buffer==1){
              
                   $write_data[] = $buffer_report;
              
            }

            if($is_stumbleupon==1){
                foreach ($stumbleupon_report as $value) {
                   $write_data[] = $value;
                }
            }



            $write_data[]=$searched_at;
        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'search_at'        => $searched_at
            );

           
            if($is_facebook ==1){
                $insert_data['fb_like'] = $facebook_report['total_like'];
                $insert_data['fb_share'] = $facebook_report['total_share'];
                $insert_data['fb_comment'] = $facebook_report['total_comment'];
            }

            if($is_googleplus ==1){
                $insert_data['google_plus_count'] = $googleplus_report;
            }

             if($is_linkedin ==1){
                $insert_data['linked_in_share'] = $linkedin_report;
            }

            if($is_xing ==1){
                 $insert_data['xing_share_count'] = $xing_report;
            }

           
             if($is_reddit==1){
                 $insert_data['reddit_score'] = $reddit_report['score'];
                 $insert_data['reddit_up'] = $reddit_report['ups'];
                 $insert_data['reddit_dowon'] = $reddit_report['downs'];
            }

            if($is_pinterest ==1){
                 $insert_data['pinterest_pin'] = $pinterest_report;
            }

            if($is_buffer ==1){
                 $insert_data['buffer_share'] = $buffer_report;
            }

             if($is_stumbleupon==1){
                 $insert_data['stumbleupon_view'] = $stumbleupon_report['total_view'];
                 $insert_data['stumbleupon_like'] = $stumbleupon_report['total_like'];
                 $insert_data['stumbleupon_comment'] = $stumbleupon_report['total_comment'];
                 $insert_data['stumbleupon_list'] = $stumbleupon_report['total_list'];
            }

           

            $count++;

            $str.= "<tr><td>".$count."</td><td>".$domain."</td>";
          

        // if($is_facebook==1) $str.="<td>{$facebook_report['total_like']}</td><td>{$facebook_report['total_share']}</td><td>{$facebook_report['total_comment']}</td>";
        if($is_facebook==1) $str.="<td>{$facebook_report['total_share']}</td>";
        if($is_googleplus==1) $str.="<td>{$googleplus_report}</td>";
        if($is_linkedin==1)    $str.="<td>{$linkedin_report}</td>";
        if($is_xing==1) $str.="<td>{$xing_report}</td>";
        if($is_reddit==1) $str.="<td>{$reddit_report['score']}</td><td>{$reddit_report['downs']}</td><td>{$reddit_report['ups']}</td>";
        if($is_pinterest==1)    $str.="<td>{$pinterest_report}</td>";
        if($is_buffer==1) $str.="<td>{$buffer_report}</td>";      
        if($is_stumbleupon==1) $str.="<td>{$stumbleupon_report['total_view']}</td><td>{$stumbleupon_report['total_like']}</td><td>{$stumbleupon_report['total_comment']}</td><td>{$stumbleupon_report['total_list']}</td>";

        $str.="</tr>";
            
            $this->basic->insert_data('social_info', $insert_data);
            $social_analysis_complete++;
            $this->session->set_userdata("social_analysis_complete_search",$social_analysis_complete);        
        }
        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=3,$request=count($url_array));   
        //******************************//   

        echo $str.="</table>";

    }

  

    public function social_download()
    {
        $all=$this->input->post("all");
        $table = 'social_info';
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
        $fp = fopen("download/social/social_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
 
        $head=array("Domain Name","Facebook Share", "Google+ Count", "Linkedin Share" ,"Xing Share", "Reddit Score" ,"Reddit Up", "Reddit Down", "Pinterest Pin" ,"Buffer Share" ,"StumbleUpon View", "StumbleUpon Like", "StumbleUpon Comment", "StumbleUpon List" ,"Search At");
                    
        fputcsv($fp, $head);
        $write_info = array();

       /* domain_name     user_id     reddit_score    reddit_up   reddit_dowon    linked_in_share     pinterest_pin   buffer_share    fb_like     fb_share    fb_comment  google_plus_count   stumbleupon_view    stumbleupon_like    stumbleupon_comment     stumbleupon_list    xing_share_count    search_at*/

        foreach ($info as  $value) 
        {
            $write_info['domain_name'] = $value['domain_name'];
            // $write_info['fb_like'] = $value['fb_like'];
            $write_info['fb_share'] = $value['fb_share'];
            // $write_info['fb_comment'] = $value['fb_comment'];
            $write_info['google_plus_count'] = $value['google_plus_count'];
            $write_info['linked_in_share'] = $value['linked_in_share'];

            $write_info['xing_share_count'] = $value['xing_share_count'];
            $write_info['reddit_score'] = $value['reddit_score'];
            $write_info['reddit_up'] = $value['reddit_up'];
            $write_info['reddit_dowon'] = $value['reddit_dowon'];
            $write_info['pinterest_pin'] = $value['pinterest_pin'];
            $write_info['buffer_share'] = $value['buffer_share'];

            $write_info['stumbleupon_view'] = $value['stumbleupon_view'];
            $write_info['stumbleupon_like'] = $value['stumbleupon_like'];
            $write_info['stumbleupon_comment'] = $value['stumbleupon_comment'];
            $write_info['stumbleupon_list'] = $value['stumbleupon_list'];
            $write_info['search_at'] = $value['search_at'];
          
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/social/social_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function social_delete()
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
        $this->db->delete('social_info');
    }


    
    public function bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('social_analysis_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('social_analysis_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }  

   

}
