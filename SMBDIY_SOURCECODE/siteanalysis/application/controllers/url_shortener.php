<?php 
require_once("home.php"); // loading home controller

class Url_shortener extends Home
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
        if($this->session->userdata('user_type') != 'Admin' && !in_array(18,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index()
    {
        $data['body'] = "admin/url_shortener/url_shortener_grid";
        $data['page_title'] = $this->lang->line("url shortener");
        $this->_viewcontroller($data);
    }


    public function url_shortener_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $original_url = trim($this->input->post("original_url", true));
        $short_url = trim($this->input->post("short_url", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('url_shortener_original_url', $original_url);
            $this->session->set_userdata('url_shortener_short_url', $short_url);
            $this->session->set_userdata('url_shortener_from_date', $from_date);
            $this->session->set_userdata('url_shortener_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_original_url   = $this->session->userdata('url_shortener_original_url');
        $search_short_url   = $this->session->userdata('url_shortener_short_url');
        $search_from_date  = $this->session->userdata('url_shortener_from_date');
        $search_to_date = $this->session->userdata('url_shortener_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_original_url)    $where_simple['long_url like '] = "%".$search_original_url."%";
        if ($search_short_url)    $where_simple['short_url like '] = "%".$search_short_url."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["add_date >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["add_date <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "url_shortener";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        
        $i = 0;
        foreach($info as $results){
            $result[$i]['id'] = $results['id'];
            $result[$i]['long_url'] = $results['long_url'];
            $result[$i]['short_url'] = $results['short_url'];
            $result[$i]['add_date'] = $results['add_date'];
            $result[$i]['details'] = "<a class='label label-warning' href='".base_url()."url_shortener/url_analytics/".$results['id']."'><i class='fa fa-binoculars'></i> ".$this->lang->line('details')."</a>";
            $i++;
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($result, $total_result);
    }


    public function url_shortener_action()
    {
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=18,$request=count($url_array));
        if($status=="2") 
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        //************************************************//
        
      
        $this->session->set_userdata('url_shortener_bulk_total_search',count($url_array));
        $this->session->set_userdata('url_shortener_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/url_shortener/short_url_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
          
        /**Write header in csv file***/

        $write_data=array();            
        $write_data[]="Original URL";          
        $write_data[]="Short URL";               
        $write_data[]="Created at";            
                                        
        
        fputcsv($download_path, $write_data);
        
        $short_url_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Original URL</td>
            <td>Short URL</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            $domain_data=array();
            $domain = urlencode($domain);
            $domain_data=$this->short_url_creator($domain);  
            $created_at= date("Y-m-d");
            
            $original_url = $domain_data['longUrl'];
            $short_url = $domain_data['id'];    
            $write_data=array();
     
            $write_data[]=$original_url;
            $write_data[]=$short_url;

            $write_data[]=$created_at;
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'        => $this->user_id,
                'long_url'       => $original_url,
                'short_url'      => $short_url,
                'add_date'       => $created_at
            );

            $count++;

           

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$original_url."</td>
            <td>".$short_url."</td>
            </tr>";
            
            $this->basic->insert_data('url_shortener', $insert_data);    
            $short_url_complete++;
            $this->session->set_userdata("url_shortener_complete_search",$short_url_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=18,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }


    public function short_url_creator($long_url)
    {
        $key="";
        $where['where'] = array("user_id"=>$this->user_id);
        $select = array('google_safety_api');
        $api_key = $this->basic->get_data('config',$where,$select);
        if(!empty($api_key))
            $key = $api_key[0]['google_safety_api'];

        $url="https://www.googleapis.com/urlshortener/v1/url?fields=analytics%2Ccreated%2Cid%2Ckind%2ClongUrl%2Cstatus&key={$key}";
        
        // $long_url="https://codecanyon.net/item/sitespy-complete-visitor-seo-analytics/15641449?s_rank=1";
        $long_url = urldecode($long_url);
        
        $data['longUrl']=$long_url;
        $data=json_encode($data);
        
         $headers = array("Content-type: application/json");
                        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        
        
        $content = curl_exec($ch); // run the whole process
        
        $content= json_decode($content,TRUE);
        
        curl_close($ch);
        return $content;
    }


    public function short_url_download()
    {
        $all=$this->input->post("all");
        $table = 'url_shortener';
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
        $fp = fopen("download/url_shortener/short_url_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        

        $write_data=array();            
        $write_data[]="Original URL";          
        $write_data[]="Short URL";           
        $write_data[]="Created at";   
                    
        fputcsv($fp, $write_data);
        $write_info = array();

        foreach ($info as  $value) 
        {
                $write_data=array();            
                $write_data[]=$value["long_url"];      
                $write_data[]=$value["short_url"];
                $write_data[]=$value["add_date"];   
            
                fputcsv($fp, $write_data);
        }

        fclose($fp);
        $file_name = "download/url_shortener/short_url_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    public function short_url_delete()
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
        $this->db->delete('url_shortener');
    }



    public function url_analytics($id=0)
    {
        if($id==0) exit();
        $where['where'] = array('id'=>$id);
        $short_url = $this->basic->get_data("url_shortener",$where);
        $short_url_encode = urlencode($short_url[0]['short_url']);
        $url_details = $this->url_analytics_content($short_url_encode);
        $data['url_details'] = $url_details;

        $data['body'] = "admin/url_shortener/url_details";
        $data['page_title'] = $this->lang->line("url shortener");
        $this->_viewcontroller($data);

    }


    public function url_analytics_content($short_url_encode)
    {        
        $key="";
        $where['where'] = array("user_id"=>$this->user_id);
        $select = array('google_safety_api');
        $api_key = $this->basic->get_data('config',$where,$select);
        if(!empty($api_key))
            $key = $api_key[0]['google_safety_api'];
        
        $url="https://www.googleapis.com/urlshortener/v1/url?shortUrl={$short_url_encode}&projection=FULL&fields=analytics%2Ccreated%2Cid%2Ckind%2ClongUrl%2Cstatus&key={$key}";
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch); // run the whole process
        
        $content= json_decode($content,TRUE);
        
        curl_close($ch);

        return $content;
    }


    public function bulk_url_short_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('url_shortener_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('url_shortener_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }


    public function url_analytics_page_loader()
    {
        $data['body'] = "admin/url_shortener/url_analytics_page_loader";
        $data['page_title'] = $this->lang->line("url shortener");
        $this->_viewcontroller($data);
    }

    public function url_analytics_result()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $short_url = $this->input->post('short_url',true);
        $short_url_encode = urlencode($short_url);
        $url_details = $this->url_analytics_content($short_url_encode);
        $data['url_details'] = $url_details;

        $data['body'] = "admin/url_shortener/url_details";
        $data['page_title'] = $this->lang->line("url analytics");
        $this->_viewcontroller($data);
    }



}