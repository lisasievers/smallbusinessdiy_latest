<?php

require_once("home.php"); // loading home controller

class backlink extends Home
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
        if($this->session->userdata('user_type') != 'Admin' && !in_array(9,$this->module_access))
        redirect('home/login_page', 'location'); 
    }

    public function index()
    {
        $this->backlink_gererator();
    }

    public function backlink_gererator()
    {
        $data['body'] = 'admin/backlink/generator';
        $data['page_title'] = $this->lang->line('backlink generator');
        $this->_viewcontroller($data);
    }
    

    public function backlink_gererator_data()
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
            $this->session->set_userdata('backlink_gererator_domain_name', $domain_name);
            $this->session->set_userdata('backlink_gererator_from_date', $from_date);
            $this->session->set_userdata('backlink_gererator_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('backlink_gererator_domain_name');
        $search_from_date  = $this->session->userdata('backlink_gererator_from_date');
        $search_to_date = $this->session->userdata('backlink_gererator_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["generated_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["generated_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "backlink_generator";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function backlink_gererator_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);
        
        $back_link_url=$this->web_common_report->back_link_url; 
        $total_url=count($back_link_url); // no of backlink url
        $out_of=ceil($total_url*count($url_array));

        $this->session->set_userdata('backlink_gererator_bulk_total_search',$out_of);
        $this->session->set_userdata('backlink_gererator_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/backlink/backlink_gererator_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Backlink URL";            
        $write_data[]="Domain";            
        $write_data[]="Response Code";            
        $write_data[]="Status";               
        
        fputcsv($download_path, $write_data);
        
        $backlink_gererator_complete=0;

       
      

        $str="";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $backlink_gererator_data=array();
            $backlink_gererator_data=$this->web_common_report->make_backlink($domain);

             $count=0;
             $str.=
            "<br/><table class='table table-bordered table-hover table-striped'><caption class='text-center' style='font-size:17px;font-weight:bold;'>".$domain."</caption>".
            "<tr>
            <td>SL</td>
            <td>Backlink URL</td>
            <td>Response Code</td>
            <td>Status</td>
            </tr>";

            foreach($back_link_url as $link)
            {
                $link=str_replace("[url]",$domain,$link);
                $response=$this->web_common_report->make_backlink($link);
                $generated_at= date("Y-m-d H:i:s");
                  
                if($response=="200") $status="successful";
                else $status="failed";

                $write_data=array();
                $write_data[]=$link;
                $write_data[]=$domain;
                $write_data[]=$response;               
                $write_data[]=$status;

            
                fputcsv($download_path, $write_data);
                
                /** Insert into database ***/
       
                $insert_data=array
                (
                    'user_id'           => $this->user_id,
                    'domain_name'       => $domain,
                    'url'               => $link,
                    'response_code'     => $response,
                    'status'            => $status,
                    'generated_at'      => $generated_at
                );

                $count++;

                $str.=
                "<tr>
                <td>".$count."</td>
                <td>".$link."</td>
                <td>".$response."</td>
                <td>".$status."</td>
                </tr>";
                
                $this->basic->insert_data('backlink_generator', $insert_data);    
                $backlink_gererator_complete++;
                $this->session->set_userdata("backlink_gererator_complete_search",$backlink_gererator_complete);    
            }  
            $str.="</table>";  
        }

        echo $str;      

    }

  

    public function backlink_gererator_download()
    {
        $all=$this->input->post("all");
        $table = 'backlink_generator';
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
// echo $this->db->last_query(); exit();
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/backlink/backlink_gererator_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $head=array("Backlink URL","Doamin","Rresponse_code","status","Generated at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['url'] = $value['url'];
            $write_info['domain_name'] = $value['domain_name'];
            $write_info['response_code'] = $value['response_code'];
            $write_info['status'] = $value['status'];
            $write_info['generated_at'] = $value['generated_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/backlink/backlink_gererator_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function backlink_gererator_delete()
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
        $this->db->delete('backlink_generator');
    }


    
    public function bulk_backlink_gererator_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('backlink_gererator_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('backlink_gererator_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }








     public function backlink_search()
    {
        $data['body'] = 'admin/backlink/backlink_search';
        $data['page_title'] = $this->lang->line('google backlink search');
        $this->_viewcontroller($data);
    }
    

    public function backlink_search_data()
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
            $this->session->set_userdata('backlink_search_domain_name', $domain_name);
            $this->session->set_userdata('backlink_search_from_date', $from_date);
            $this->session->set_userdata('backlink_search_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('backlink_search_domain_name');
        $search_from_date  = $this->session->userdata('backlink_search_from_date');
        $search_to_date = $this->session->userdata('backlink_search_to_date');

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
                $where_simple["Date_Format(searched_at,'%Y-%m-%d') <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "backlink_search";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function backlink_search_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=9,$request=count($url_array));
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
        
      
        $this->session->set_userdata('backlink_search_bulk_total_search',count($url_array));
        $this->session->set_userdata('backlink_search_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/backlink/backlink_search_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Domain";            
        $write_data[]="Backlink Count";                
        
        fputcsv($download_path, $write_data);
        
        $backlink_search_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>Backlink Count</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $backlink_count="";
            $backlink_count=$this->web_common_report->GoogleBL($domain);  
            $searched_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
            $write_data[]=$domain;
            $write_data[]=$backlink_count;
        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'backlink_count'    => $backlink_count,
                'searched_at'       => $searched_at
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$backlink_count."</td>
            </tr>";
            
            $this->basic->insert_data('backlink_search', $insert_data);    
            $backlink_search_complete++;
            $this->session->set_userdata("backlink_search_complete_search",$backlink_search_complete);     
			sleep(8);
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=9,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function backlink_search_download()
    {
        $all=$this->input->post("all");
        $table = 'backlink_search';
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
        $fp = fopen("download/backlink/backlink_search_{$this->user_id}_{$download_id}.csv", "w");
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // unicode compatible csv
        $head=array("Doamin","Backlink Count","Searched at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['domain_name'] = $value['domain_name'];
            $write_info['backlink_count'] = $value['backlink_count'];
            $write_info['searched_at'] = $value['searched_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/backlink/backlink_search_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function backlink_search_delete()
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
        $this->db->delete('backlink_search');
    }


    
    public function bulk_backlink_search_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('backlink_search_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('backlink_search_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

  

}
