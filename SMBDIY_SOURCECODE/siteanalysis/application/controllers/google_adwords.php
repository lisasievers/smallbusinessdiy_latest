<?php
require_once("home.php"); // loading home controller

class google_adwords extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(11,$this->module_access))
        redirect('home/login_page', 'location'); 
    }
	
	
	public function index()
    {
        $this->google_adword_scrape();
    }

    public function google_adword_scrape()
    {
        $data['body'] = 'admin/google_adword/adword_scraper';
        // $data['page_title'] = 'Google AdWords Scraper';
        $data['page_title'] = $this->lang->line('google adwords scraper');
        $data['country_name'] = $this->get_country_names();
        $data['language_name'] = $this->get_language_names();
        $this->_viewcontroller($data);
    }
	
	
	
	function google_adwords_data(){
	
		if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $keyword = trim($this->input->post("search_keyword", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('google_adwords_keyword', $keyword);
            $this->session->set_userdata('google_adwords_from_date', $from_date);
            $this->session->set_userdata('google_adwords_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_keyword   = $this->session->userdata('google_adwords_keyword');
        $search_from_date  = $this->session->userdata('google_adwords_from_date');
        $search_to_date = $this->session->userdata('google_adwords_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_keyword)    $where_simple['keyword like ']    = "%".$search_keyword."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(scraped_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(scraped_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "google_adword";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
		
	}



	public function google_adwords_action()
    {

        //************************************************//
        $status=$this->_check_usage($module_id=11,$request=1);
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
  		
  		$keyword=$this->input->post('keyword');
        // $proxy=$this->input->post('proxy_server');		
		$country=$this->input->post('country');
		$language=$this->input->post('language');

        $caption="Keyword: ".$keyword." - Location: ".$country." - Language: ".$language;
		
		if($country=='all') $country="";				
		if($language=='all') $language="";		
        // if($proxy=='no') $proxy="";               

        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/google_adwords/google_adwords_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data[]="Keyword";            
        $write_data[]="Location";            
        $write_data[]="Language";            
        $write_data[]="Title";            
        $write_data[]="Description";            
        $write_data[]="URL";                  
        
        fputcsv($download_path, $write_data);
 
        $str=
            "<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3></caption>
            <tr>
            <td>SL</td>
            <td>Title</td>
            <td>Description</td>
            <td>URL</td>
            </tr>";

        $google_adwords_data=array();
        $google_adwords_data=$this->web_common_report->google_adwords_ad($keyword, $page_number=0, $proxy='',$country,$language);  
        $scraped_at= date("Y-m-d H:i:s");

        $title =$google_adwords_data["title"];
        $description =$google_adwords_data["description"];
        $link =$google_adwords_data["link"];

        $this->session->set_userdata('google_adwords_complete_search',0);
        $this->session->set_userdata('google_adwords_bulk_total_search',count($title));

        $count=0;

        foreach ($title as $key => $value) 
        {
        	if(is_array($description) && array_key_exists($key,$description))
        	$des=$description[$key];
        	else $des="";

        	if(is_array($link) && array_key_exists($key,$link))
        	$url=$link[$key];
        	else $url="";

        	$count++;

        	$write_data=array();
            $write_data[]=$keyword;
            $write_data[]=$country;
            $write_data[]=$language;
            $write_data[]=$value;
            $write_data[]=$des;
            $write_data[]=$url;

        	fputcsv($download_path, $write_data);
        	$str.=
            "<tr>
            <td>".$count."</td>
            <td>".$value."</td>
            <td>".$des."</td>
            <td><a title='visit now' target='_BLANK' href='".addHttp($url)."'>".$url."</a></td>
            </tr>";

            $this->session->set_userdata("google_adwords_complete_search",$count); 
        }


    	$insert_data=array
        (
             'user_id'                          => $this->user_id,
             'scraped_at'                      	=> $scraped_at,
             'keyword'							=> $keyword,
             'location'							=> $country,
             'language'                         => $language,             
             'title'							=> json_encode($title),
             'description'						=> json_encode($description),
             'link'								=> json_encode($link)
        );


        if($this->basic->insert_data('google_adword', $insert_data)){
            //******************************//
            // insert data to useges log table
            $this->_insert_usage_log($module_id=11,$request=1);   
            //******************************//
                
            echo $str;      
        }

    }

  

    public function google_adwords_download()
    {
        $all=$this->input->post("all");
        $table = 'google_adword';
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
        $fp = fopen("download/google_adwords/google_adwords_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
       
        $write_data=array();            
        $write_data[]="Keyword";            
        $write_data[]="Location";            
        $write_data[]="Language";            
        $write_data[]="Title";            
        $write_data[]="Description";            
        $write_data[]="URL";  
        $write_data[]="Scraped at";  
                    
        fputcsv($fp, $write_data);
  
        foreach ($info as  $value) 
        {
         
        	           
            $keyword = $value['keyword'];
            $location= $value['location'];
            $language= $value['language'];
            $title= json_decode($value['title'],true);
            $description= json_decode($value['description'],true);
            $link= json_decode($value['link'],true);
            $scraped_at= $value['scraped_at'];
			
			foreach($title as $key=>$val)
            {                  
                $tit="";
                $des="";
                $url="";

                if(array_key_exists($key, $title) && array_key_exists($key, $description) && array_key_exists($key, $link))
                {
                	$tit=$title[$key];
                	$des=$description[$key];
                	$url=$link[$key];
      
                    $write_data=array(); 
                    $write_data[]=$keyword;            
			        $write_data[]=$location;            
			        $write_data[]=$language;            
			        $write_data[]=$tit;            
			        $write_data[]=$des;            
			        $write_data[]=$url;  
			        $write_data[]=$scraped_at;  
            		fputcsv($fp, $write_data);
                }                    
            }
        }
            
        

        fclose($fp);
        $file_name = "download/google_adwords/google_adwords_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function google_adwords_delete()
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
        $this->db->delete('google_adword');
    }


    
    public function bulk_google_adwords_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('google_adwords_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('google_adwords_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

	
}
?>