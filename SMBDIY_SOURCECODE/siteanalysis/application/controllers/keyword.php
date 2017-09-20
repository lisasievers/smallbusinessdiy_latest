<?php
require_once("home.php"); // loading home controller

class keyword extends Home
{

    public $user_id;    
    public $download_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
        
        $this->load->library('Web_common_report');
        $this->user_id=$this->session->userdata('user_id');
        $this->download_id=$this->session->userdata('download_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(8,$this->module_access))
        redirect('home/login_page', 'location'); 
    }
	
	
	public function index()
    {
        $this->keyword_position();
    }

    public function keyword_position()
    {
        $data['body'] = 'admin/keyword/keyword_position';
        $data['page_title'] = $this->lang->line("keyword position analysis");
        $data['country_name'] = $this->get_country_names();
        $data['language_name'] = $this->get_language_names();
        $this->_viewcontroller($data);
    }
	
	
	
	function keyword_position_data(){
	
		if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain = trim($this->input->post("search_domain", true));
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
            $this->session->set_userdata('keyword_position_domain', $domain);
            $this->session->set_userdata('keyword_position_keyword', $keyword);
            $this->session->set_userdata('keyword_position_from_date', $from_date);
            $this->session->set_userdata('keyword_position_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain   = $this->session->userdata('keyword_position_domain');
        $search_keyword   = $this->session->userdata('keyword_position_keyword');
        $search_from_date  = $this->session->userdata('keyword_position_from_date');
        $search_to_date = $this->session->userdata('keyword_position_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain)     $where_simple['domain_name like ']    = "%".$search_domain."%";
        if ($search_keyword)    $where_simple['keyword like ']    = "%".$search_keyword."%";
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
        $table = "keyword_position";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
		
	}



	public function keyword_position_action()
    {
        //************************************************//
        $status=$this->_check_usage($module_id=8,$request=1);
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
  		
  		$domain=$this->input->post('domain_name');
  		$keyword=$this->input->post('keyword');        	
		$country=$this->input->post('country');
		$language=$this->input->post('language');
		$is_google=$this->input->post('is_google', true);
		$is_bing=$this->input->post('is_bing', true);
		$is_yahoo=$this->input->post('is_yahoo', true);

        $caption="Domain: ".$domain." - Keyword: ".$keyword." - Location: ".$country." - Language: ".$language;
		
		if($country=='all') $country="";				
		if($language=='all') $language="";		


        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/keyword_position/keyword_position_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data=array();            
        $write_data[]="Search Engine";            
        $write_data[]="Domain";            
        $write_data[]="Keyword";            
        $write_data[]="Location";            
        $write_data[]="Language";           
        $write_data[]="Top Site";                  
        $write_data[]="Top URL";                  
        
        fputcsv($download_path, $write_data);
 
       
        $no_process=0;
        $no_process=$is_google+$is_bing+$is_yahoo;
        $this->session->set_userdata('keyword_position_complete_search',0);
        $this->session->set_userdata('keyword_position_bulk_total_search',$no_process);
        $keyword_position_complete_search=0;

        $searched_at= date("Y-m-d H:i:s");

        $keyword_position_google_data=array();
        $keyword_position_bing_data=array();        
        $keyword_position_yahoo_data=array();        
        if($is_google==1)	
        {
        	$keyword_position_google_data=$this->web_common_report->keyword_position_google($keyword, $page_number=0, $proxy="",$country,$language,$domain);  
        	$keyword_position_complete_search++;
            $this->session->set_userdata('keyword_position_complete_search',$keyword_position_complete_search);
    	}
        if($is_bing==1)		
        {
        	$keyword_position_bing_data=$this->web_common_report->keyword_position_bing($keyword, $page_number=0, $proxy="",$country,$language,$domain);  
        	$keyword_position_complete_search++;
            $this->session->set_userdata('keyword_position_complete_search',$keyword_position_complete_search);
    	}
        if($is_yahoo==1)	
        {
        	$keyword_position_yahoo_data=$this->web_common_report->keyword_position_yahoo($keyword, $page_number=0, $proxy="",$country,$language,$domain);  
        	$keyword_position_complete_search++;
            $this->session->set_userdata('keyword_position_complete_search',$keyword_position_complete_search);
    	}
        $google_postition="";
        $bing_postition="";
        $yahoo_postition="";

        if($is_google==1)	$google_postition=$keyword_position_google_data["status"];
        if($is_bing==1) 	$bing_postition=$keyword_position_bing_data["status"];
        if($is_yahoo==1) 	$yahoo_postition=$keyword_position_yahoo_data["status"];
       	 
       

        $str="";

        if($is_google==1 && $google_postition!="caught_0_dolphin" && $google_postition!="")
        {
        	$str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Google Position: ".$google_postition."</h2></caption>
            <tr>
            <td>SL</td>
            <td>Google Top Site</td>
            <td>Google Top URL</td>
            </tr>";

	        $count1=0;

	        foreach ($keyword_position_google_data["top_site"]["domain"] as $key => $value) 
	        {
	        	if(is_array($keyword_position_google_data["top_site"]["link"]) && array_key_exists($key,$keyword_position_google_data["top_site"]["link"]))
	        	{
	        		$site=$keyword_position_google_data["top_site"]["domain"][$key];
	        		$url_array = array();
	        		$url_array = explode('&', $keyword_position_google_data["top_site"]["link"][$key]);
	        		$url=$url_array[0];
	        	}
	        	else
	        	{
	        		$site="";
	        		$url="";
	        	}

	        	$count1++;

	        	$write_data=array();
	        	$write_data[]="Google";            
		        $write_data[]=$domain;            
		        $write_data[]=$keyword;            
		        $write_data[]=$country;            
		        $write_data[]=$language;          
		        $write_data[]=$site;                  
		        $write_data[]=$url;  

	        	fputcsv($download_path, $write_data);
	        	$str.=
	            "<tr>
	            <td>".$count1."</td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($site)."'>".$site."</a></td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($url)."'>".$url."</a></td>
	            </tr>";
	             
	        }
	        $str.="</table>";


        }


        if($is_bing==1 && $bing_postition!="caught_0_dolphin" && $bing_postition!="")
        {
        	$str.="<br/><table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Bing Position: ".$bing_postition."</h2></caption>
            <tr>
            <td>SL</td>
            <td>Bing Top Site</td>
            <td>Bing Top URL</td>
            </tr>";

	        $count2=0;

	        foreach ($keyword_position_bing_data["top_site"]["domain"] as $key => $value) 
	        {
	        	if(is_array($keyword_position_bing_data["top_site"]["link"]) && array_key_exists($key,$keyword_position_bing_data["top_site"]["link"]))
	        	{
	        		$site=$keyword_position_bing_data["top_site"]["domain"][$key];
	        		$url=$keyword_position_bing_data["top_site"]["link"][$key];
	        	}
	        	else
	        	{
	        		$site="";
	        		$url="";
	        	}

	        	$count2++;

	        	$write_data=array();
	        	$write_data[]="Bing";            
		        $write_data[]=$domain;            
		        $write_data[]=$keyword;            
		        $write_data[]=$country;            
		        $write_data[]=$language;            
		        $write_data[]=$site;                  
		        $write_data[]=$url;  

	        	fputcsv($download_path, $write_data);
	        	$str.=
	            "<tr>
	            <td>".$count2."</td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($site)."'>".$site."</a></td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($url)."'>".$url."</a></td>
	            </tr>";
	           
	        }
	        $str.="</table>";


        }


        if($is_yahoo==1 && $yahoo_postition!="caught_0_dolphin" && $yahoo_postition!="")
        {
        	$str.="<br/><table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Yahoo Position: ".$yahoo_postition."</h2></caption>
            <tr>
            <td>SL</td>
            <td>Yahoo Top Site</td>
            <td>Yahoo Top URL</td>
            </tr>";

	        $count3=0;

	        foreach ($keyword_position_yahoo_data["top_site"]["domain"] as $key => $value) 
	        {
	        	if(is_array($keyword_position_yahoo_data["top_site"]["link"]) && array_key_exists($key,$keyword_position_yahoo_data["top_site"]["link"]))
	        	{
	        		$site=$keyword_position_yahoo_data["top_site"]["domain"][$key];
	        		$url=$keyword_position_yahoo_data["top_site"]["link"][$key];
	        	}
	        	else
	        	{
	        		$site="";
	        		$url="";
	        	}

	        	$count3++;

	        	$write_data=array();
	        	$write_data[]="Yahoo";            
		        $write_data[]=$domain;            
		        $write_data[]=$keyword;            
		        $write_data[]=$country;            
		        $write_data[]=$language;         
		        $write_data[]=$site;                  
		        $write_data[]=$url;  

	        	fputcsv($download_path, $write_data);
	        	$str.=
	            "<tr>
	            <td>".$count3."</td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($site)."'>".$site."</a></td>
	            <td><a title='visit now' target='_BLANK' href='".addHttp($url)."'>".$url."</a></td>
	            </tr>";
	            
	        }
	        $str.="</table>";


        }


    	$insert_data=array
        (
             'user_id'                          => $this->user_id,
             'searched_at'                     	=> $searched_at,
             'domain_name'						=> $domain,
             'keyword'							=> $keyword,
             'location'							=> $country,
             'language'							=> $language         
        );

        if($is_google==1 && $google_postition!="caught_0_dolphin" && $google_postition!="")
        {
        	 $insert_data['google_position']= $google_postition;
             $insert_data['google_top_site_url']= json_encode($keyword_position_google_data["top_site"]);
        }

        if($is_bing==1 && $bing_postition!="caught_0_dolphin" && $bing_postition!="")
        {
        	 $insert_data['bing_position']= $bing_postition;
             $insert_data['bing_top_site_url']= json_encode($keyword_position_bing_data["top_site"]);
        }

        if($is_yahoo==1 && $yahoo_postition!="caught_0_dolphin" && $yahoo_postition!="")
        {
        	 $insert_data['yahoo_position']= $yahoo_postition;
             $insert_data['yahoo_top_site_url']= json_encode($keyword_position_yahoo_data["top_site"]);
        }


        if($google_postition=="caught_0_dolphin" && $bing_postition=="caught_0_dolphin" && $yahoo_postition=="caught_0_dolphin")
        {
        	$insert_data=array();
        	$str="<h4 class='text-center'>Operation stopped. Seach engine has detected robotic operation. Use proxy or try after taking some rest.</h4>";
        }

    	if(!empty($insert_data)){

        	$this->basic->insert_data('keyword_position', $insert_data);

            //******************************//
            // insert data to useges log table
            $this->_insert_usage_log($module_id=8,$request=1);   
            //******************************//  
          
            echo $str;      
        }

    }

  

    public function keyword_position_download()
    {
        $all=$this->input->post("all");
        $table = 'keyword_position';
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
        $fp = fopen("download/keyword_position/keyword_position_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
       
        $write_data=array();            
        $write_data[]="Search Engine";            
        $write_data[]="Domain";            
        $write_data[]="Keyword";            
        $write_data[]="Location";            
        $write_data[]="Language";         
        $write_data[]="Keyword Position";            
        $write_data[]="Top Site";                  
        $write_data[]="Top URL";  
        $write_data[]="Searched at";  
                    
        fputcsv($fp, $write_data);

        $write_data=array();   
        foreach ($info as  $value) 
        {         
        	           
            $keyword = $value['keyword'];
            $domain = $value['domain_name'];
            $location= $value['location'];
            $language= $value['language'];
            $google_position= $value['google_position'];
            $bing_position= $value['bing_position'];
            $yahoo_position= $value['yahoo_position'];
            $searched_at= $value['searched_at'];

            $google_top_site_url= json_decode($value['google_top_site_url'],true);
            $bing_top_site_url= json_decode($value['bing_top_site_url'],true);
            $yahoo_top_site_url= json_decode($value['yahoo_top_site_url'],true);


            $google_top_site=array();
            if(array_key_exists("domain", $google_top_site_url))
            $google_top_site=$google_top_site_url["domain"];

        	$google_top_link=array();
            if(array_key_exists("link", $google_top_site_url))
            $google_top_link=$google_top_site_url["link"];

			if(count($google_top_site)>0 && count($google_top_link)>0)
			{
				foreach( $google_top_site as $key=>$val)
	            {  
	               
	                $write_data=array(); 
			        $write_data[]="Google";            
			        $write_data[]=$domain;            
			        $write_data[]=$keyword;            
			        $write_data[]=$location;            
			        $write_data[]=$language;                 
			        $write_data[]=$google_position;                   
			        $write_data[]=$val;  
			        $write_data[]=$google_top_link[$key];  
			        $write_data[]=$searched_at;                  
	        		fputcsv($fp, $write_data);                               
	            }
			}



			$bing_top_site=array();
            if(array_key_exists("domain", $bing_top_site_url))
            $bing_top_site=$bing_top_site_url["domain"];

        	$bing_top_link=array();
            if(array_key_exists("link", $bing_top_site_url))
            $bing_top_link=$bing_top_site_url["link"];

			if(count($bing_top_site)>0 && count($bing_top_link)>0)
			{
				foreach( $bing_top_site as $key=>$val)
	            {  
	                	
	                $write_data=array();
	                $write_data[]="Bing";            
			        $write_data[]=$domain;            
			        $write_data[]=$keyword;            
			        $write_data[]=$location;            
			        $write_data[]=$language;                 
			        $write_data[]=$google_position;                   
			        $write_data[]=$val;  
			        $write_data[]=$google_top_link[$key];  
			        $write_data[]=$searched_at;            
	        		fputcsv($fp, $write_data);                               
	            }
			}

			$yahoo_top_site=array();
            if(array_key_exists("domain", $yahoo_top_site_url))
            $yahoo_top_site=$yahoo_top_site_url["domain"];

        	$yahoo_top_link=array();
            if(array_key_exists("link", $yahoo_top_site_url))
            $yahoo_top_link=$yahoo_top_site_url["link"];

			if(count($yahoo_top_site)>0 && count($yahoo_top_link)>0)
			{
				foreach( $yahoo_top_site as $key=>$val)
	            {  
			        
	                $write_data=array();
	                $write_data[]="Yahoo";            
			        $write_data[]=$domain;            
			        $write_data[]=$keyword;            
			        $write_data[]=$location;            
			        $write_data[]=$language;                  
			        $write_data[]=$google_position;                   
			        $write_data[]=$val;  
			        $write_data[]=$google_top_link[$key];  
			        $write_data[]=$searched_at;            
	        		fputcsv($fp, $write_data);                               
	            }
			}
			
        }
            
        fclose($fp);
        $file_name = "download/keyword_position/keyword_position_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function keyword_position_delete()
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
        $this->db->delete('keyword_position');
    }


    
    public function bulk_keyword_position_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('keyword_position_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('keyword_position_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }



    public function google_correlated_keyword()
    {
        $data['body'] = 'admin/keyword/correlated_keyword';
        $data['page_title'] = $this->lang->line("correlated keywords");        
        $data['country_name'] = $this->get_country_names();
        $this->_viewcontroller($data);
    }


    public function google_correlated_keyword_action()
    {

        //************************************************//
        $status=$this->_check_usage($module_id=8,$request=1);
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

        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $this->load->library('Web_common_report');
        $keyword=$this->input->post("keyword");
        $country=$this->input->post("country");

        $google_correlated_keyword_data=$this->web_common_report->google_correlated_trending_keyword($keyword,$country,$proxy=""); 

        $correlation=array();
        $cor_keyword=array();

        if(array_key_exists("correlation", $google_correlated_keyword_data))
        $correlation=$google_correlated_keyword_data["correlation"];

        if(array_key_exists("cor_keyword", $google_correlated_keyword_data))
        $cor_keyword=$google_correlated_keyword_data["cor_keyword"];

        $str="<table class='table table-bordered table-striped'><caption><h3 class='text-center'>Keyword: ".$keyword." | Country: ".$country."</h3></caption><tr><th>SL</th><th>Correlation</th><th>Correlated Keyword</th></tr>";
        $count=0;

        $fp = fopen("download/keyword_position/correlated_keyword_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        $write_data=array();
        $write_data[]="Keyword";  
        $write_data[]="Correlated Keyword";  
        $write_data[]="Correlation";                 
        fputcsv($fp, $write_data);

        foreach ($correlation as $key => $value) 
        {
           $count++;
           $cor_val="";
           if(array_key_exists($key, $cor_keyword)) $cor_val=$cor_keyword[$key];
           $str.= "<tr>";
           $str.= "<td>".$count."</td>";
           $str.= "<td>".$value."</td>";
           $str.= "<td>".$cor_val."</td>";
           $str.= "</tr>";

           $write_data=array();
           $write_data[]=$keyword;  
           $write_data[]=$cor_val;  
           $write_data[]=$value;                 
           fputcsv($fp, $write_data);
        }

        if(count($correlation)==0) $str.="<tr><td colspan='3'>No data found!</td></tr>";


        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=8,$request=1);   
        //******************************//


        fclose($fp);
        echo $str.="<table>";

    }



    public function keyword_suggestion()
    {
        $data['body'] = 'admin/keyword/keyword_suggestion';
        $data['page_title'] = $this->lang->line("keyword auto suggestion");
        $this->_viewcontroller($data);
    }
    
    
    
    function keyword_suggestion_data(){
    
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
            $this->session->set_userdata('keyword_suggestion_keyword', $keyword);
            $this->session->set_userdata('keyword_suggestion_from_date', $from_date);
            $this->session->set_userdata('keyword_suggestion_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_keyword   = $this->session->userdata('keyword_suggestion_keyword');
        $search_from_date  = $this->session->userdata('keyword_suggestion_from_date');
        $search_to_date = $this->session->userdata('keyword_suggestion_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_keyword)    $where_simple['keyword like ']    = "%".$search_keyword."%";
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
        $table = "keyword_suggestion";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
        
    }



    public function keyword_suggestion_action()
    {
        //************************************************//
        $status=$this->_check_usage($module_id=8,$request=1);
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
        $is_google=$this->input->post('is_google', true);
        $is_bing=$this->input->post('is_bing', true);
        $is_yahoo=$this->input->post('is_yahoo', true);
        $is_wiki=$this->input->post('is_wiki', true);
        $is_amazon=$this->input->post('is_amazon', true);

        $caption="Keyword: ".$keyword;

        $download_id= $this->session->userdata('download_id');        
        $download_path=fopen("download/keyword_position/keyword_suggestion_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_count=0;
        
        /**Write header in csv file***/
        $write_data=array();            
        $write_data[]="Keyword";            
        $write_data[]="Search Engine";                  
        $write_data[]="Auto Suggestion";                  
        
        fputcsv($download_path, $write_data); 
       
        $no_process=0;
        $no_process=$is_google+$is_bing+$is_yahoo+$is_wiki+$is_amazon;
        $this->session->set_userdata('keyword_suggestion_complete_search',0);
        $this->session->set_userdata('keyword_suggestion_bulk_total_search',$no_process);
        $keyword_suggestion_complete_search=0;

        $searched_at= date("Y-m-d H:i:s");

        $keyword_suggestion_google_data=array();
        $keyword_suggestion_bing_data=array();        
        $keyword_suggestion_yahoo_data=array();        
        $keyword_suggestion_wiki_data=array();        
        $keyword_suggestion_amazon_data=array();        
        if($is_google==1)   
        {
            $keyword_suggestion_google_data=$this->web_common_report->google_auto_sugesstion_keyword($keyword); 
            $keyword_suggestion_complete_search++; 
            $this->session->set_userdata('keyword_suggestion_complete_search',$keyword_suggestion_complete_search);
        }
        if($is_bing==1)     
        {
            $keyword_suggestion_bing_data=$this->web_common_report->bing_auto_sugesstion_keyword($keyword);
            $keyword_suggestion_complete_search++;  
            $this->session->set_userdata('keyword_suggestion_complete_search',$keyword_suggestion_complete_search);
        }
        if($is_yahoo==1)    
        {
            $keyword_suggestion_yahoo_data=$this->web_common_report->yahoo_auto_sugesstion_keyword($keyword); 
            $keyword_suggestion_complete_search++; 
            $this->session->set_userdata('keyword_suggestion_complete_search',$keyword_suggestion_complete_search);
        }
        if($is_wiki==1)    
        {
            $keyword_suggestion_wiki_data=$this->web_common_report->wiki_auto_sugesstion_keyword($keyword);  
            $keyword_suggestion_complete_search++;
            $this->session->set_userdata('keyword_suggestion_complete_search',$keyword_suggestion_complete_search);
        }
        if($is_amazon==1)    
        {
            $keyword_suggestion_amazon_data=$this->web_common_report->amazon_auto_sugesstion_keyword($keyword);
            $keyword_suggestion_complete_search++;  
            $this->session->set_userdata('keyword_suggestion_complete_search',$keyword_suggestion_complete_search);
        }

        $google_suggestion=array();
        $bing_suggestion=array();
        $yahoo_suggestion=array();
        $wiki_suggestion=array();
        $amazon_suggestion=array();
		
		
		if(!is_array($keyword_suggestion_google_data))
			$keyword_suggestion_google_data=array();
			
		if(!is_array($keyword_suggestion_bing_data))
			$keyword_suggestion_bing_data=array();
			
		if(!is_array($keyword_suggestion_yahoo_data))
			$keyword_suggestion_yahoo_data=array();
			
		if(!is_array($keyword_suggestion_wiki_data))
			$keyword_suggestion_wiki_data=array();
		
		if(!is_array($keyword_suggestion_amazon_data))
			$keyword_suggestion_amazon_data=array();
			
		
		
		

        if($is_google==1    && array_key_exists(1, $keyword_suggestion_google_data))    $google_suggestion=$keyword_suggestion_google_data[1];
        if($is_bing==1      && array_key_exists(1, $keyword_suggestion_bing_data))      $bing_suggestion=$keyword_suggestion_bing_data[1];
        if($is_yahoo==1     && array_key_exists(1, $keyword_suggestion_yahoo_data))     $yahoo_suggestion=$keyword_suggestion_yahoo_data[1];
        if($is_wiki==1      && array_key_exists(1, $keyword_suggestion_wiki_data))      $wiki_suggestion=$keyword_suggestion_wiki_data[1];
        if($is_amazon==1    && array_key_exists(1, $keyword_suggestion_amazon_data))    $amazon_suggestion=$keyword_suggestion_amazon_data[1];
         
       

        $str="";

        if($is_google==1 && count($google_suggestion)!=0)
        {
            $str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Google Suggestion</h2></caption>
            <tr>
            <td>SL</td>
            <td>Suggestion</td>
            </tr>";

            $count1=0;

            foreach ($google_suggestion as $key => $value) 
            {
                
                $count1++;

                $write_data=array();
                $write_data[]=$keyword;            
                $write_data[]="Google";            
                $write_data[]=$value;
                fputcsv($download_path, $write_data);

                $str.=
                "<tr>
                <td>".$count1."</td>
                <td>".$value."</td>
                </tr>";
                 
            }
            $str.="</table>";
        }


        if($is_bing==1 && count($bing_suggestion)!=0)
        {
            $str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Bing Suggestion</h2></caption>
            <tr>
            <td>SL</td>
            <td>Suggestion</td>
            </tr>";

            $count2=0;

            foreach ($bing_suggestion as $key => $value) 
            {
                
                $count2++;

                $write_data=array();
                $write_data[]=$keyword;            
                $write_data[]="Bing";            
                $write_data[]=$value;
                fputcsv($download_path, $write_data);

                $str.=
                "<tr>
                <td>".$count2."</td>
                <td>".$value."</td>
                </tr>";
                 
            }
            $str.="</table>";
        }


        if($is_yahoo==1 && count($yahoo_suggestion)!=0)
        {
            $str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Yahoo Suggestion</h2></caption>
            <tr>
            <td>SL</td>
            <td>Suggestion</td>
            </tr>";

            $count3=0;

            foreach ($yahoo_suggestion as $key => $value) 
            {
                
                $count3++;

                $write_data=array();
                $write_data[]=$keyword;            
                $write_data[]="Yahoo";            
                $write_data[]=$value;
                fputcsv($download_path, $write_data);

                $str.=
                "<tr>
                <td>".$count3."</td>
                <td>".$value."</td>
                </tr>";
                 
            }
            $str.="</table>";
        }



        if($is_wiki==1 && count($wiki_suggestion)!=0)
        {
            $str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Wiki Suggestion</h2></caption>
            <tr>
            <td>SL</td>
            <td>Suggestion</td>
            </tr>";

            $count4=0;

            foreach ($wiki_suggestion as $key => $value) 
            {
                
                $count4++;

                $write_data=array();
                $write_data[]=$keyword;            
                $write_data[]="Wiki";            
                $write_data[]=$value;
                fputcsv($download_path, $write_data);

                $str.=
                "<tr>
                <td>".$count4."</td>
                <td>".$value."</td>
                </tr>";
                 
            }
            $str.="</table>";
        }


        if($is_amazon==1 && count($amazon_suggestion)!=0)
        {
            $str.="<table class='table table-bordered table-hover table-striped'><caption><h3 class='text-center'>".$caption."</h3><h2 class='text-center'>Amazon Suggestion</h2></caption>
            <tr>
            <td>SL</td>
            <td>Suggestion</td>
            </tr>";

            $count5=0;

            foreach ($amazon_suggestion as $key => $value) 
            {
                
                $count5++;

                $write_data=array();
                $write_data[]=$keyword;            
                $write_data[]="Amazon";            
                $write_data[]=$value;
                fputcsv($download_path, $write_data);

                $str.=
                "<tr>
                <td>".$count5."</td>
                <td>".$value."</td>
                </tr>";
                 
            }
            $str.="</table>";
        }


        $insert_data=array
        (
             'user_id'                          => $this->user_id,
             'searched_at'                      => $searched_at,
             'keyword'                          => $keyword          
        );

        if($is_google==1 && count($google_suggestion)!=0)
        {
             $insert_data['google_suggestion']= json_encode($google_suggestion);
        }

        if($is_bing==1 && count($bing_suggestion)!=0)
        {
             $insert_data['bing_suggestion']= json_encode($bing_suggestion);
        }

        if($is_yahoo==1 && count($yahoo_suggestion)!=0)
        {
             $insert_data['yahoo_suggestion']= json_encode($yahoo_suggestion);
        }

        if($is_wiki==1 && count($wiki_suggestion)!=0)
        {
           $insert_data['wiki_suggestion']= json_encode($wiki_suggestion);
        }

        if($is_amazon==1 && count($amazon_suggestion)!=0)
        {
            $insert_data['amazon_suggestion']= json_encode($amazon_suggestion);
        }

        if(count($google_suggestion)==0 && count($bing_suggestion)==0 && count($yahoo_suggestion)==0 && count($wiki_suggestion)==0 && count($amazon_suggestion)==0)
        {
            $insert_data=array();
        }

        if(!empty($insert_data)){
            
            $this->basic->insert_data('keyword_suggestion', $insert_data);

            //******************************//
            // insert data to useges log table
            $this->_insert_usage_log($module_id=8,$request=1);   
            //******************************//  
          
            echo $str;      
        }

    }

  

    public function keyword_suggestion_download()
    {
        $all=$this->input->post("all");
        $table = 'keyword_suggestion';
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
        $fp = fopen("download/keyword_position/keyword_suggestion_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
       
        $write_data=array();            
        $write_data[]="Keyword";            
        $write_data[]="Search Engine";                  
        $write_data[]="Auto Suggestion";
        $write_data[]="Searched at";
                    
        fputcsv($fp, $write_data);

        $write_data=array();   
        foreach ($info as  $value) 
        {         
                       
            $keyword = $value['keyword'];
            $searched_at = $value['searched_at'];

            $google_suggestion= json_decode($value['google_suggestion'],true);
            $bing_suggestion= json_decode($value['bing_suggestion'],true);
            $yahoo_suggestion= json_decode($value['yahoo_suggestion'],true);
            $wiki_suggestion= json_decode($value['wiki_suggestion'],true);
            $amazon_suggestion= json_decode($value['amazon_suggestion'],true);


            if(count($google_suggestion)>0)
            {
                foreach($google_suggestion as $key=>$val)
                {  
                   
                    $write_data=array(); 
                    $write_data[]=$keyword;            
                    $write_data[]="Google";  
                    $write_data[]=$val;  
                    $write_data[]=$searched_at;                  
                    fputcsv($fp, $write_data);                               
                }
            }

            if(count($bing_suggestion)>0)
            {
                foreach($bing_suggestion as $key=>$val)
                {  
                   
                    $write_data=array(); 
                    $write_data[]=$keyword;            
                    $write_data[]="Bing";  
                    $write_data[]=$val;  
                    $write_data[]=$searched_at;                  
                    fputcsv($fp, $write_data);                               
                }
            }

            if(count($yahoo_suggestion)>0)
            {
                foreach($yahoo_suggestion as $key=>$val)
                {  
                   
                    $write_data=array(); 
                    $write_data[]=$keyword;            
                    $write_data[]="Yahoo";  
                    $write_data[]=$val;  
                    $write_data[]=$searched_at;                  
                    fputcsv($fp, $write_data);                               
                }
            }

            if(count($wiki_suggestion)>0)
            {
                foreach($wiki_suggestion as $key=>$val)
                {  
                   
                    $write_data=array(); 
                    $write_data[]=$keyword;            
                    $write_data[]="Wiki";  
                    $write_data[]=$val;  
                    $write_data[]=$searched_at;                  
                    fputcsv($fp, $write_data);                               
                }
            }

            if(count($amazon_suggestion)>0)
            {
                foreach($amazon_suggestion as $key=>$val)
                {  
                   
                    $write_data=array(); 
                    $write_data[]=$keyword;            
                    $write_data[]="Amazon";  
                    $write_data[]=$val;  
                    $write_data[]=$searched_at;                  
                    fputcsv($fp, $write_data);                               
                }
            }
            
        }
            
        fclose($fp);
        $file_name = "download/keyword_position/keyword_suggestion_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function keyword_suggestion_delete()
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
        $this->db->delete('keyword_suggestion');
    }


    
    public function bulk_keyword_suggestion_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('keyword_suggestion_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('keyword_suggestion_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }

    public function keyword_analyzer()
    {
        $data['body'] = "admin/keyword/keyword_analyzer";
        $data['page_title'] = $this->lang->line("keyword analyzer");
        $this->_viewcontroller($data);
    }

    public function keyword_analyzer_data()
    {
        //************************************************//
        $status=$this->_check_usage($module_id=8,$request=1);
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

        $domain_name = $this->input->post('domain_name',TRUE);
        $data['domain_name'] = $domain_name;
        $data['meta_tag_info'] = $this->web_common_report->content_analysis($domain_name);

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=8,$request=1);   
        //******************************//

        $str = $this->load->view('admin/keyword/keyword_analyzer_data',$data);
        echo $str;
    }


	
}
?>