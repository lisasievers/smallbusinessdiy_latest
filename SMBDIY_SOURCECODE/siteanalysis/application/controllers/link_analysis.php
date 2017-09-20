<?php

require_once("home.php"); // loading home controller

class link_analysis extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(7,$this->module_access))
        redirect('home/login_page', 'location'); 
    }

    public function index()
    {
        $this->link_analysis();
    }

    public function link_analysis()
    {
        $data['body'] = 'admin/link_analysis/link_analysis';
        $data['page_title'] = $this->lang->line("link analyzer");
        $this->_viewcontroller($data);
    }
    

    public function link_analysis_data()
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
            $this->session->set_userdata('link_analysis_domain_name', $domain_name);
            $this->session->set_userdata('link_analysis_from_date', $from_date);
            $this->session->set_userdata('link_analysis_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('link_analysis_domain_name');
        $search_from_date  = $this->session->userdata('link_analysis_from_date');
        $search_to_date = $this->session->userdata('link_analysis_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['url like '] = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "link_analysis";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function link_analysis_action()
    {

        //************************************************//
        $status=$this->_check_usage($module_id=7,$request=1);
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

        $this->load->library('web_common_report');
        $url=$this->input->post('urls', true);
         
      
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/link/link_analysis_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        
        /**Write header in csv file***/      
        
        $link_analysis_complete=0;
  

        $link_analysis_data=array();
        $link_analysis_data=$this->web_common_report->link_statistics($url);  
        $searched_at= date("Y-m-d H:i:s");

        $total_external_link=$link_analysis_data["external_link_count"];
        $total_internal_link=$link_analysis_data["internal_link_count"];
        $total_nofollow=$link_analysis_data["nofollow_count"];
        $total_dofollow=$link_analysis_data["do_follow_count"];
        $total_link=$total_external_link+$total_internal_link;

        $this->session->set_userdata('link_analysis_complete_search',0);
        $this->session->set_userdata('link_analysis_bulk_total_search',$total_link);

        $write_data=array();            
        $write_data[]="URL";            
        $write_data[]=$url;            
        $write_data[]="Total Link";            
        $write_data[]=$total_link;            
        $write_data[]="External Link Count";            
        $write_data[]=$total_external_link;            
        $write_data[]="Internal Link Count";            
        $write_data[]=$total_internal_link;            
        $write_data[]="DoFollow Count";                
        $write_data[]=$total_dofollow;            
        $write_data[]="NoFollow Count";                
        $write_data[]=$total_nofollow;   

        fputcsv($download_path, $write_data);


        $str=
        "<table class='table table-bordered table-hover table-striped'>
        <caption><h3 class='text-center'>Link Analyzer</h3></caption>
        <tr>
        <td>Link</td>
        <td>Count</td>
        </tr>
        <tr>
        <td>Total Links</td>
        <td><span class='badge' style='background:#367FA9;'>".$total_link."</span></td>
        </tr>
        <tr>
        <td>External Links</td>
        <td><span class='badge' style='background:#367FA9;'>".$total_external_link."</span></td>
        </tr>
        <tr>
        <td>Internal Links</td>
        <td><span class='badge' style='background:#367FA9;'>".$total_internal_link."</span></td>
        </tr>
        <tr>
        <td>DoFollow Links</td>
        <td><span class='badge' style='background:#4CAF50;'>".$total_dofollow."</span></td>
        </tr>
        <tr>
        <td>NoFollow Links</td>
        <td><span class='badge' style='background:#D9534F;'>".$total_nofollow."</span></td>
        </tr></table>";


        $write_data=array();            
        $write_data[]="Links"; 
        $write_data[]="Type"; 
        $write_data[]="DoFollow/NoFollow"; 
        fputcsv($download_path, $write_data);

        $str.=
        "<br/><table class='table table-bordered table-hover table-striped'>
        <caption><h3 class='text-center'>Internal Links</h3></caption>
        <tr>
        <td>SL</td>
        <td>URL</td>
        <td>DoFollow/NoFollow</td>
        </tr>
        </tr>";
        if(count($link_analysis_data["internal_link"])==0)  
        $str.="<tr><td colspan='3'>No data found!</td></tr></tr>";

        $count=0;
        foreach ($link_analysis_data["internal_link"] as $key => $value) 
        {
            $count++;
            $write_data=array();            
            $write_data[]=$value["link"]; 
            $write_data[]="Internal"; 
            $write_data[]=$value["type"]; 
            fputcsv($download_path, $write_data); 

            if($value["type"]=="dofollow")
            $dofollow_nofollow="<span class='label label-success'>".$value['type']."</span>";
            else $dofollow_nofollow="<span class='label label-danger'>".$value['type']."</span>";

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$value["link"]."</td>
            <td>".$dofollow_nofollow."</td>
            </tr>
            </tr>";

            $link_analysis_complete++;
            $this->session->set_userdata("link_analysis_complete_search",$link_analysis_complete);   
        }
        $str.="</table>";
        

        $str.=
        "<br/><table class='table table-bordered table-hover table-striped'>
        <caption><h3 class='text-center'>External Links</h3></caption>
        <tr>
        <td>SL</td>
        <td>URL</td>
        <td>DoFollow/NoFollow</td>
        </tr>
        </tr>";
        if(count($link_analysis_data["external_link"])==0)  
        $str.="<tr><td colspan='3'>No data found!</td></tr></tr>";

        $count=0;
        foreach ($link_analysis_data["external_link"] as $key => $value) 
        {
            $count++;
            $write_data=array();            
            $write_data[]=$value["link"]; 
            $write_data[]="External"; 
            $write_data[]=$value["type"]; 
            fputcsv($download_path, $write_data); 

            if($value["type"]=="dofollow")
            $dofollow_nofollow="<span class='label label-success'>".$value['type']."</span>";
            else $dofollow_nofollow="<span class='label label-danger'>".$value['type']."</span>";

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$value["link"]."</td>
            <td>".$dofollow_nofollow."</td>
            </tr>
            </tr>";

            $link_analysis_complete++;
            $this->session->set_userdata("link_analysis_complete_search",$link_analysis_complete);  
        }
        $str.="</table>";


        /** Insert into database ***/

        $insert_data=array
        (
            'user_id'                           => $this->user_id,
            'searched_at'                       => $searched_at,
            'url'                               => $url,
            'external_link_count'               => $total_external_link,
            'internal_link_count'               => $total_internal_link,
            'nofollow_count'                    => $total_nofollow,
            'do_follow_count'                   => $total_dofollow,
            'external_link'                     => json_encode($link_analysis_data["external_link"]),
            'internal_link'                     => json_encode($link_analysis_data["internal_link"]),
            'searched_at'                       => $searched_at
        );

               
        if($this->basic->insert_data('link_analysis', $insert_data)){
            //******************************//
            // insert data to useges log table
            $this->_insert_usage_log($module_id=7,$request=1);   
            //******************************//
            
            echo $str;     
        }
       

    }

  

    public function link_analysis_download()
    {
        $all=$this->input->post("all");
        $table = 'link_analysis';
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
        $fp = fopen("download/link/link_analysis_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        $write_data=array();            
        $write_data[]="URL"; 
        $write_data[]="Links"; 
        $write_data[]="Type"; 
        $write_data[]="DoFollow/NoFollow"; 
        fputcsv($fp, $write_data);


        foreach ($info as $row) 
        {
            
            $internal_json_data=json_decode($row["internal_link"],true);
            foreach ($internal_json_data as $key => $value) 
            {              
                $write_data=array();            
                $write_data[]=$row["url"]; 
                $write_data[]=$value["link"]; 
                $write_data[]="Internal"; 
                $write_data[]=$value["type"]; 
                fputcsv($fp, $write_data);  
            }

            $external_json_data=json_decode($row["external_link"],true);
            foreach ($external_json_data as $key => $value) 
            {                
                $write_data=array();            
                $write_data[]=$row["url"]; 
                $write_data[]=$value["link"]; 
                $write_data[]="External"; 
                $write_data[]=$value["type"]; 
                fputcsv($fp, $write_data);                
            }       
                
        }

        fclose($fp);
        $file_name = "download/link/link_analysis_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function link_analysis_delete()
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
        $this->db->delete('link_analysis');
    }

   
    public function bulk_link_analysis_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('link_analysis_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('link_analysis_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

 

   

}
