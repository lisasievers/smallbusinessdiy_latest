<?php

require_once("home.php"); // loading home controller

class ping extends Home
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
        $this->ping_website();
    }

    public function ping_website()
    {
        $data['body'] = 'admin/ping/website_ping';
        $data['page_title'] = $this->lang->line('website ping');
        $this->_viewcontroller($data);
    }
    

    public function ping_website_data()
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
            $this->session->set_userdata('ping_website_domain_name', $domain_name);
            $this->session->set_userdata('ping_website_from_date', $from_date);
            $this->session->set_userdata('ping_website_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('ping_website_domain_name');
        $search_from_date  = $this->session->userdata('ping_website_from_date');
        $search_to_date = $this->session->userdata('ping_website_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['blog_url_to_ping like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01'){
                $search_from_date = $search_from_date." 00:00:00";
                $where_simple["ping_at >="]= $search_from_date;
            }
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01'){
                $search_to_date = $search_to_date." 23:59:59";
                $where_simple["ping_at <="]=$search_to_date;
            }
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "website_ping";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function ping_website_action()
    {
        $this->load->library('web_common_report');
        $blog_name=$this->input->post('blog_name', true);
        $blog_url=$this->input->post('blog_url', true);
        $blog_url_to_ping=$this->input->post('blog_url_to_ping', true);
        $blog_rss_feed_url=$this->input->post('blog_rss_feed_url', true);
                 
        $all_pink_link=$this->web_common_report->ping_link; 
        $total_url=count($all_pink_link); // no of backlink url       

        $this->session->set_userdata('ping_website_bulk_total_search',$total_url);
        $this->session->set_userdata('ping_website_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/ping/ping_website_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data=array();            
        $write_data[]="Blog Name";            
        $write_data[]="Blog URL";            
        $write_data[]="Blog URL to Ping";            
        $write_data[]="Blog RSS Feed URL";               
        $write_data[]="Ping URL";               
        $write_data[]="Response";               
        $write_data[]="Ping at";               
        
        fputcsv($download_path, $write_data);
        
        $ping_website_complete=0;


        $str=
        "<br/><table class='table table-bordered table-hover table-striped'>".
        "<tr>
        <td>SL</td>
        <td>Blog Name</td>
        <td>Blog URL</td>
        <td>Blog URL to Ping</td>
        <td>Blog RSS Feed URL</td>
        <td>Ping URL</td>
        <td>Response</td>
        </tr>";

        $count=0;

        foreach($all_pink_link as $link)
        {
            $response_array=$this->web_common_report->ping_url($blog_name,$blog_url,$blog_url_to_ping,$blog_rss_feed_url,$link);
            $ping_at= date("Y-m-d H:i:s");
              
            $response="";
            if(is_array($response_array) && array_key_exists('message',$response_array)) 
            $response=$response_array["message"];


            $write_data=array();
            $write_data[]=$blog_name;
            $write_data[]=$blog_url;
            $write_data[]=$blog_url_to_ping;               
            $write_data[]=$blog_rss_feed_url;
            $write_data[]=$response;
            $write_data[]=$ping_at;

        
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'ping_at'           => $ping_at,
                'blog_name'         => $blog_name,
                'blog_url'          => $blog_url,
                'blog_url_to_ping'  => $blog_url_to_ping,
                'blog_rss_feed_url' => $blog_rss_feed_url,
                'ping_url'          => $link,
                'response'          => $response
            );

            $count++;

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$blog_name."</td>
            <td>".$blog_url."</td>
            <td>".$blog_url_to_ping."</td>
            <td>".$blog_rss_feed_url."</td>
            <td>".$link."</td>
            <td>".$response."</td>
            </tr>";
            
            $this->basic->insert_data('website_ping', $insert_data);    
            $ping_website_complete++;
            $this->session->set_userdata("ping_website_complete_search",$ping_website_complete);    
        }  
        $str.="</table>";  


        echo $str;      

    }

  

    public function ping_website_download()
    {
        $all=$this->input->post("all");
        $table = 'website_ping';
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
        $fp = fopen("download/ping/ping_website_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));


        $head=array("Blog Name","Blog URL","Blog URL to Ping","Blog RSS Feed URL","Ping URL","Response","Ping at");
                    
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
            $write_info['blog_name'] = $value['blog_name'];
            $write_info['blog_url'] = $value['blog_url'];
            $write_info['blog_url_to_ping'] = $value['blog_url_to_ping'];
            $write_info['blog_rss_feed_url'] = $value['blog_rss_feed_url'];
            $write_info['ping_url'] = $value['ping_url'];
            $write_info['response'] = $value['response'];
            $write_info['ping_at'] = $value['ping_at'];
            
            fputcsv($fp, $write_info);
        }

        fclose($fp);
        $file_name = "download/ping/ping_website_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function ping_website_delete()
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
        $this->db->delete('website_ping');
    }


    
    public function bulk_ping_website_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('ping_website_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('ping_website_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

  

}
