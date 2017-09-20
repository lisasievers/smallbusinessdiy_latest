<?php

require_once("home.php"); // loading home controller



class who_is extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index()
    {
        $this->who_is_list();
    }

    public function who_is_list(){
        $data['body'] = 'admin/who_is/who_is_list';
        $data['page_title'] = $this->lang->line('whois search');
        $this->_viewcontroller($data);
    }

    public function who_is_list_data(){


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
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(scraped_time ,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(scraped_time ,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "whois_search";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);

    }


     public function who_is_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);        
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);
        $bulk_tracking_code=time();


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
        
      
        $this->session->set_userdata('who_is_search_bulk_total_search',count($url_array));
        $this->session->set_userdata('who_is_search_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/who_is/who_is_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_domain[]="Domain";
        $write_domain[]="Is Registered";
        $write_domain[]="Registrant Email"; 
        $write_domain[]="Tech Email";
        $write_domain[]="Admin Email";
    
        $write_domain[]="Name Servers";
        $write_domain[]="Created At";
        $write_domain[]="Changed At";
        $write_domain[]="Sponsor";
        $write_domain[]="Expires At";
        
        
        $write_domain[]="Registrat URL";
        
        $write_domain[]="Registrant Name";
        $write_domain[]="Registrant Organization";
        $write_domain[]="Registrant Street";
        $write_domain[]="Registrant City";
        $write_domain[]="Registrant State";
        $write_domain[]="Registrant Postal Code";
        $write_domain[]="Registrant Country";
        $write_domain[]="Registrant Phone";
        
        $write_domain[]="Admin Name";
        $write_domain[]="Admin Street";
        $write_domain[]="Admin City";
        $write_domain[]="Admin State";
        $write_domain[]="Admin Postal Code";
        $write_domain[]="Admin Country";
        $write_domain[]="Admin Phone";            
        
        fputcsv($download_path, $write_domain);
        
        $who_is_search_complete=0;
       /* $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];*/

        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";        

        $str.="<td>Domain</td>";
        $str.="<td>Is Registered</td>";
        $str.="<td>Registrant Email</td>";
        $str.="<td>Tech Email</td>";
        $str.="<td>Admin Email</td>";
        $str.="<td>Name Servers</td>";
        $str.="<td>Created At</td>";
        $str.="<td>Changed At</td>";
        $str.="<td>Sponsor</td>";
        $str.="<td>Expires At</td>";
        $str.="<td>Registrat URL</td>";
        $str.="<td>Registrant Name</td>";
        $str.="<td>Registrant Organization</td>";
        $str.="<td>Registrant Street</td>";
        $str.="<td>Registrant City</td>";
        $str.="<td>Registrant State</td>";
        $str.="<td>Registrant Postal Code</td>";
        $str.="<td>Registrant Country</td>";
        $str.="<td>Registrant Phone</td>";
        $str.="<td>Admin Name</td>";
        $str.="<td>Admin Street</td>";
        $str.="<td>Admin City</td>";
        $str.="<td>Admin State</td>";
        $str.="<td>Admin Postal Code</td>";
        $str.="<td>Admin Country</td>";
        $str.="<td>Admin Phone</td>";            
        $str.="</tr>";

        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);
         
           /* $who_is_report =$this->web_common_report->whois_email($domain);*/
            $domain_info=@$this->web_common_report->whois_email($domain);
            if($domain_info['tech_email']=='' && $domain_info['is_registered']=='yes'){
                sleep(5);
                $domain_info=@$this->web_common_report->whois_email($domain);
            }
            
            if($domain_info['tech_email']=='' && $domain_info['is_registered']=='yes'){
                    $f_email=$this->web_common_report->get_email($domain_info['rawdata']);
                    $domain_info['tech_email']=isset($f_email[0]) ? $f_email[0]:"";
                    $domain_info['admin_email']=isset($f_email[1])? $f_email[1]:"";
            }
            
            
            if(is_null($domain_info['tech_email'])){
                $domain_info['tech_email']="";
            }
            
            if(is_null($domain_info['admin_email'])){
                $domain_info['admin_email']="";
            }
            
            
            if(is_null($domain_info['registrar_url'])){
                $domain_info['registrar_url']="";
            }
            
            
            $write_domain=array();
            $write_domain[]=$domain;
            $write_domain[]=$domain_info['is_registered'];
            $write_domain[]=$domain_info['registrant_email'];
            
            $write_domain[]=$domain_info['tech_email'];
            $write_domain[]=$domain_info['admin_email'];
            $write_domain[]=$domain_info['name_servers'];
            $write_domain[]=$domain_info['created_at'];
            $write_domain[]=$domain_info['changed_at'];
            $write_domain[]=$domain_info['sponsor'];
            $write_domain[]=$domain_info['expire_at'];
            
            $write_domain[]=$domain_info['registrar_url'];
            
            $write_domain[]=$domain_info['registrant_name'];
            $write_domain[]=$domain_info['registrant_organization'];
            $write_domain[]=$domain_info['registrant_street'];
            $write_domain[]=$domain_info['registrant_city'];
            $write_domain[]=$domain_info['registrant_state'];
            $write_domain[]=$domain_info['registrant_postal_code'];
            $write_domain[]=$domain_info['registrant_country'];
            $write_domain[]=$domain_info['registrant_phone'];
            
            $write_domain[]=$domain_info['admin_name'];
            $write_domain[]=$domain_info['admin_street'];
            $write_domain[]=$domain_info['admin_city'];
            $write_domain[]=$domain_info['admin_state'];
            $write_domain[]=$domain_info['admin_postal_code'];
            $write_domain[]=$domain_info['admin_country'];
            $write_domain[]=$domain_info['admin_phone'];
            // $write_domain[]=$domain_info[''];
        
        
            fputcsv($download_path, $write_domain);
            
            /** Insert into database ***/
            
            $time=date("Y-m-d H:i:s");
            $insert_data=array(
                                'user_id'           => $this->user_id,
                                'domain_name'       => $domain,
                                'tech_email'        => $domain_info['tech_email'],
                                'admin_email'       => $domain_info['admin_email'],
                                'is_registered'     =>$domain_info['is_registered'],
                                'namve_servers'     =>$domain_info['name_servers'],
                                'created_at'        =>$domain_info['created_at'],
                                'sponsor'           =>$domain_info['sponsor'],
                                'changed_at'        =>$domain_info['changed_at'],
                                'expire_at'         =>$domain_info['expire_at'],
                                'scraped_time'      =>$time,
                                'registrant_email'  =>$domain_info['registrant_email'],
                                'registrant_name'   => $domain_info['registrant_name'],
                                'registrant_organization'=>$domain_info['registrant_organization'],
                                'registrant_street' =>$domain_info['registrant_street'],
                                'registrant_city'   =>$domain_info['registrant_city'],
                                'registrant_state'  =>$domain_info['registrant_state'],
                                'registrant_postal_code'=> $domain_info['registrant_postal_code'],
                                'registrant_country'=>$domain_info['registrant_country'],
                                'registrant_phone'  =>$domain_info['registrant_phone'],
                                'registrar_url'     =>$domain_info['registrar_url'],
                                'admin_name'        =>$domain_info['admin_name'],
                                'admin_street'      =>$domain_info['admin_street'],
                                'admin_city'        =>$domain_info['admin_city'],
                                'admin_state'       =>$domain_info['admin_state'],
                                'admin_postal_code'=> $domain_info['admin_postal_code'],
                                'admin_country'     =>$domain_info['admin_country'],
                                'admin_phone'       =>$domain_info['admin_phone'],
                                'rawdata'           =>$domain_info['rawdata'],
                                'bulk_track_code'   =>$bulk_tracking_code
                                );
            
            $this->basic->insert_data('whois_search', $insert_data);  
            $who_is_search_complete++;
            $this->session->set_userdata("who_is_search_complete_search",$who_is_search_complete);
            $count++;

            $str.= "<tr><td>".$count."</td><td>".$domain."</td>";

            $str.="<td>".$domain_info['is_registered']."</td>";              
            $str.="<td>".$domain_info['registrant_email']."</td>";              
            $str.="<td>".$domain_info['tech_email']."</td>";              
            $str.="<td>".$domain_info['admin_email']."</td>";              
            $str.="<td>".$domain_info['name_servers']."</td>";              
            $str.="<td>".$domain_info['created_at']."</td>";              
            $str.="<td>".$domain_info['changed_at']."</td>";              
            $str.="<td>".$domain_info['sponsor']."</td>";              
            $str.="<td>".$domain_info['expire_at']."</td>";              
            $str.="<td>".$domain_info['registrar_url']."</td>"; 
            $str.="<td>".$domain_info['registrant_name']."</td>";

            $str.="<td>".$domain_info['registrant_organization']."</td>";
            $str.="<td>".$domain_info['registrant_city']."</td>";               
            $str.="<td>".$domain_info['registrant_street']."</td>";                        
                         
            $str.="<td>".$domain_info['registrant_state']."</td>";              
            $str.="<td>".$domain_info['registrant_postal_code']."</td>";              
            $str.="<td>".$domain_info['registrant_country']."</td>";              
            $str.="<td>".$domain_info['registrant_phone']."</td>";

            $str.="<td>".$domain_info['admin_name']."</td>";              
            $str.="<td>".$domain_info['admin_street']."</td>";              
            $str.="<td>".$domain_info['admin_city']."</td>";              
            $str.="<td>".$domain_info['admin_state']."</td>";              
            $str.="<td>".$domain_info['admin_postal_code']."</td>";              
            $str.="<td>".$domain_info['admin_country']."</td>";              
            $str.="<td>".$domain_info['admin_phone']."</td>";             
                        
                         
            $str.="</tr>";       
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=5,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function who_is_download()
    {
        $all=$this->input->post("all");
        $table = 'whois_search';
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
        $fp = fopen("download/who_is/who_is_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $write_domain[]="Domain";
        $write_domain[]="Is Registered";
        $write_domain[]="Registrant Email"; 
        $write_domain[]="Tech Email";
        $write_domain[]="Admin Email";
    
        $write_domain[]="Name Servers";
        $write_domain[]="Created At";
        $write_domain[]="Changed At";
        $write_domain[]="Sponsor";
        $write_domain[]="Expires At";
        
        
        $write_domain[]="Registrat URL";
        
        $write_domain[]="Registrant Name";
        $write_domain[]="Registrant Organization";
        $write_domain[]="Registrant Street";
        $write_domain[]="Registrant City";
        $write_domain[]="Registrant State";
        $write_domain[]="Registrant Postal Code";
        $write_domain[]="Registrant Country";
        $write_domain[]="Registrant Phone";
        
        $write_domain[]="Admin Name";
        $write_domain[]="Admin Street";
        $write_domain[]="Admin City";
        $write_domain[]="Admin State";
        $write_domain[]="Admin Postal Code";
        $write_domain[]="Admin Country";
        $write_domain[]="Admin Phone";
                    
        fputcsv($fp, $write_domain);

        $write_info = array();

        foreach ($info as  $domain_info) 
        {
            $write_info[]=$domain_info['domain_name'];
            $write_info[]=$domain_info['is_registered'];
            $write_info[]=$domain_info['registrant_email'];
            
            $write_info[]=$domain_info['tech_email'];
            $write_info[]=$domain_info['admin_email'];
            $write_info[]=$domain_info['name_servers'];
            $write_info[]=$domain_info['created_at'];
            $write_info[]=$domain_info['changed_at'];
            $write_info[]=$domain_info['sponsor'];
            $write_info[]=$domain_info['expire_at'];
            
            $write_info[]=$domain_info['registrar_url'];
            
            $write_info[]=$domain_info['registrant_name'];
            $write_info[]=$domain_info['registrant_organization'];
            $write_info[]=$domain_info['registrant_street'];
            $write_info[]=$domain_info['registrant_city'];
            $write_info[]=$domain_info['registrant_state'];
            $write_info[]=$domain_info['registrant_postal_code'];
            $write_info[]=$domain_info['registrant_country'];
            $write_info[]=$domain_info['registrant_phone'];
            
            $write_info[]=$domain_info['admin_name'];
            $write_info[]=$domain_info['admin_street'];
            $write_info[]=$domain_info['admin_city'];
            $write_info[]=$domain_info['admin_state'];
            $write_info[]=$domain_info['admin_postal_code'];
            $write_info[]=$domain_info['admin_country'];
            $write_info[]=$domain_info['admin_phone'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/who_is/who_is_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function who_is_delete()
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
        $this->db->delete('whois_search');
    }


    
    public function bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('who_is_search_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('who_is_search_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

  

}
