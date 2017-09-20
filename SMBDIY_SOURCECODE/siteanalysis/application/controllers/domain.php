<?php

require_once("home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class domain extends Home
{

    public $user_id;    

    /**
    * load constructor
    * @access public
    * @return void
    */
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        
        $this->load->helper('form');
        $this->load->library('upload');
        $this->load->library('Web_common_report');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        $this->periodic_check();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(2,$this->module_access))
        redirect('home/login_page', 'location'); 
    }


    public function index(){
        $this->domain_list_for_domain_details();      
    }
    

    public function domain_list_for_domain_details()
    {
        $data['body'] = 'domain_details/domain_list';
        $data['page_title'] = $this->lang->line('website analysis');
        $this->_viewcontroller($data);
    }

    public function domain_list_for_domain_details_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
            // setting variables for pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name      = trim($this->input->post("domain_name", true));
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) {            
            $this->session->set_userdata('domain_list_for_details_domain_name', $domain_name);
        }

        $search_domain_name = $this->session->userdata('domain_list_for_details_domain_name');

        $where_simple=array();

        if ($search_domain_name) {
            $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        }
        
        $where_simple['user_id'] = $this->user_id;
        
        $where  = array('where'=>$where_simple);

        $order_by_str=$sort." ".$order;

        $offset = ($page-1)*$rows;
        $result = array();

        $table = "web_common_info";

        $info = $this->basic->get_data($table, $where, $select=array('id','domain_name','search_at'), $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id");      

        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

    public function domain_details_view($id=0)
    {
        $data['id'] = $id;

        $where = array();
        $where['where'] = array('id'=>$id);
        $domain_info = $this->basic->get_data('web_common_info',$where,$select='');

        $data['country_list'] = $this->get_country_names();

        $data['body'] = 'domain_details/domain_details';
        $data['page_title'] = $this->lang->line("website analysis");
        $data['domain_info'] = $domain_info;
        $this->_viewcontroller($data);
    }

    public function add_domain()
    {
        $data['body'] = 'domain_details/add_domain';
        $data['page_title'] = $this->lang->line('website analysis');
		$this->load->helper('cookie');
        $userid = get_cookie('user_id');
        $username = get_cookie('emailid');
		$data['sites']=$this->basic->get_user_sites($userid);
        $this->_viewcontroller($data);
    }

    public function add_domain_action()
    {
        $status=$this->_check_usage($module_id=2,$request=1);
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


        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $this->session->set_userdata('insert_table_id_website_analysis', '');
        //for dynamic progress bar data
        $this->session->set_userdata('add_domain_bulk_total_search', 26);
        $add_complete = 0;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str = '';
        $this->session->set_userdata('completed_function_str', $completed_function_str);

        $domain_name = strtolower($this->input->post('domain_name', true));
        $user_id = $this->user_id; 

        $common_result['user_id'] = $user_id;
        $common_result['domain_name'] = $domain_name;
        $common_result['search_at'] = date("Y-m-d G:i:s");


        // get moz info
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
            $moz_access_id="";
            $moz_secret_key="";
            $mobile_ready_api_key="";
            if(count($config_data)>0)
            {
                $moz_access_id=$config_data[0]["moz_access_id"];
                $moz_secret_key=$config_data[0]["moz_secret_key"];
                $mobile_ready_api_key=$config_data[0]["mobile_ready_api_key"];
            }
        $get_moz_info = $this->web_common_report->get_moz_info($domain_name,$moz_access_id, $moz_secret_key);
        $common_result['moz_subdomain_normalized'] = $get_moz_info['mozrank_subdomain_normalized'];
        $common_result['moz_subdomain_raw'] = $get_moz_info['mozrank_subdomain_raw'];
        $common_result['moz_url_normalized'] = $get_moz_info['mozrank_url_normalized'];
        $common_result['moz_url_raw'] = $get_moz_info['mozrank_url_raw'];
        $common_result['moz_http_status_code'] = $get_moz_info['http_status_code'];
        $common_result['moz_domain_authority'] = $get_moz_info['domain_authority'];
        $common_result['moz_page_authority'] = $get_moz_info['page_authority'];
        $common_result['moz_external_equity_links'] = $get_moz_info['external_equity_links'];
        $common_result['moz_links'] = $get_moz_info['links'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". MOZ ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
        // end of get moz info

       

        $common_result["mobile_ready_data"] = $this->web_common_report->mobile_ready($domain_name,$mobile_ready_api_key);
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Mobile Friendly ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
        // end of get mobile ready

        $dmoz_check = $this->web_common_report->dmoz_check($domain_name);
        $common_result['dmoz_listed_or_not'] = $dmoz_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". DMOZ ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
		
		
		$backlink_count=$common_result['moz_external_equity_links'];
		if($backlink_count=="")
			$backlink_count=0;
			
		$common_result['google_back_link_count'] = number_format($backlink_count);
		$add_complete++;
		
		$this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Backlink ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
		
		$common_result['yahoo_back_link_count'] = 0;
		$common_result['bing_back_link_count'] = 0;
		
		


       /* $GoogleBL = $this->web_common_report->GoogleBL($domain_name);
        $common_result['google_back_link_count'] = $GoogleBL;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Google Backlink ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        $yahoo_backlink = $this->web_common_report->yahoo_backlink($domain_name);
        $common_result['yahoo_back_link_count'] = $yahoo_backlink;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Yahoo Backlink ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        $bing_backlink = $this->web_common_report->bing_backlink($domain_name);
        $common_result['bing_back_link_count'] = $bing_backlink;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Bing Backlink ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
        */

        // $get_google_page_rank = $this->web_common_report->get_google_page_rank($domain_name);
        // if($get_google_page_rank)
        //     $common_result['google_page_rank'] = $get_google_page_rank;
        // else
        //     $common_result['google_page_rank'] = 0;
        // $add_complete++;
        // $this->session->set_userdata('add_domain_complete_search', $add_complete);
        // $completed_function_str .= "<div>".$add_complete.". Google Page Rank ".$this->lang->line("step completed")."</div>";
        // $this->session->set_userdata('completed_function_str', $completed_function_str);

        /*********************** screen shot taker ***************/
        
        /*********************************************************/
        

        /*************** alexa info data ****************/
        $alexa_data_full = $this->web_common_report->alexa_raw_data($domain_name);
        
        $global_rank =$alexa_data_full["global_rank"];
        $country_rank =$alexa_data_full["country_rank"];
        $country =$alexa_data_full["country"];
        $traffic_rank_graph =$alexa_data_full["traffic_rank_graph"];
        $country_name =$alexa_data_full["country_name"];
        $country_percent_visitor =$alexa_data_full["country_percent_visitor"];
        $country_in_rank = $alexa_data_full["country_in_rank"];
        $bounce_rate =$alexa_data_full["bounce_rate"];
        $page_view_per_visitor =$alexa_data_full["page_view_per_visitor"];
        $daily_time_on_the_site =$alexa_data_full["daily_time_on_the_site"];
        $visitor_percent_from_searchengine =$alexa_data_full["visitor_percent_from_searchengine"];
        $search_engine_percentage_graph =$alexa_data_full["search_engine_percentage_graph"];
        $keyword_name =$alexa_data_full["keyword_name"];
        $keyword_percent_of_search_traffic =$alexa_data_full["keyword_percent_of_search_traffic"];
        $upstream_site_name =$alexa_data_full["upstream_site_name"];
        $upstream_percent_unique_visits =$alexa_data_full["upstream_percent_unique_visits"];
        $total_site_linking_in =$alexa_data_full["total_site_linking_in"];
        $linking_in_site_name =$alexa_data_full["linking_in_site_name"];
        $linking_in_site_address =$alexa_data_full["linking_in_site_address"];        
        $subdomain_name =$alexa_data_full["subdomain_name"];
        $subdomain_percent_visitors=$alexa_data_full["subdomain_percent_visitors"];
        $status=$alexa_data_full["status"];

        $common_result['global_rank'] = $global_rank;
        $common_result['country_rank'] = $country_rank;
        $common_result['country'] = $country;
        $common_result['traffic_rank_graph'] = $traffic_rank_graph;
        $common_result['country_name'] = json_encode($country_name);
        $common_result['country_percent_visitor'] = json_encode($country_percent_visitor);
        $common_result['country_in_rank'] = json_encode($country_in_rank);
        $common_result['bounce_rate'] = $bounce_rate;
        $common_result['page_view_per_visitor'] = $page_view_per_visitor;
        $common_result['daily_time_on_the_site'] = $daily_time_on_the_site;
        $common_result['visitor_percent_from_searchengine'] = $visitor_percent_from_searchengine;
        $common_result['search_engine_percentage_graph'] = $search_engine_percentage_graph;
        $common_result['keyword_name'] = json_encode($keyword_name);
        $common_result['keyword_percent_of_search_traffic'] = json_encode($keyword_percent_of_search_traffic);
        $common_result['upstream_site_name'] = json_encode($upstream_site_name);
        $common_result['upstream_percent_unique_visits'] = json_encode($upstream_percent_unique_visits);
        $common_result['total_site_linking_in'] = $total_site_linking_in;
        $common_result['linking_in_site_name'] = json_encode($linking_in_site_name);
        $common_result['linking_in_site_address'] = json_encode($linking_in_site_address);
        $common_result['subdomain_name'] = json_encode($subdomain_name);
        $common_result['subdomain_percent_visitors'] = json_encode($subdomain_percent_visitors);
        $common_result['status'] = $status;

        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Alexa ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);

        /********************** similar web information *************************/
        $similar_web_data=array();
        $similar_web_data=$this->web_common_report->similar_web_raw_data($domain_name);

        $similar_web_global_rank =$similar_web_data["global_rank"];
        $similar_web_country_rank =$similar_web_data["country_rank"];
        $similar_web_country =$similar_web_data["country"];

        $similar_web_category_rank=$similar_web_data["category_rank"];
        $similar_web_category=$similar_web_data["category"];
        $similar_web_total_visit=$similar_web_data["total_visit"];
        $similar_web_time_on_site=$similar_web_data["time_on_site"];
        $similar_web_page_views=$similar_web_data["page_views"];
        $similar_web_bounce=$similar_web_data["bounce"];

        $similar_web_traffic_country=$similar_web_data["traffic_country"];
        $similar_web_traffic_country_percentage=$similar_web_data["traffic_country_percentage"];


        $similar_web_direct_traffic=$similar_web_data["direct_traffic"];
        $similar_web_referral_traffic=$similar_web_data["referral_traffic"];
        $similar_web_search_traffic=$similar_web_data["search_traffic"];
        $similar_web_social_traffic=$similar_web_data["social_traffic"];
        $similar_web_mail_traffic=$similar_web_data["mail_traffic"];
        $similar_web_display_traffic=$similar_web_data["display_traffic"];

        $similar_web_top_referral_site=$similar_web_data["top_referral_site"];
        $similar_web_top_destination_site=$similar_web_data["top_destination_site"];

        $similar_web_organic_search_percentage=$similar_web_data["organic_search_percentage"];
        $similar_web_paid_search_percentage=$similar_web_data["paid_search_percentage"];

        $similar_web_top_organic_keyword=$similar_web_data["top_organic_keyword"];
        $similar_web_top_paid_keyword=$similar_web_data["top_paid_keyword"];

        $similar_web_social_site_name=$similar_web_data["social_site_name"];
        $similar_web_social_site_percentage=$similar_web_data["social_site_percentage"];          

        $similar_web_status=$similar_web_data["status"];

        $common_result['similar_web_global_rank']               = $similar_web_global_rank;
        $common_result['similar_web_country_rank']              = $similar_web_country_rank;
        $common_result['similar_web_country']                   = $similar_web_country;
        $common_result['similar_web_category']                  = $similar_web_category;
        $common_result['similar_web_category_rank']             = $similar_web_category_rank;
        $common_result['similar_web_total_visit']               = $similar_web_total_visit;
        $common_result['similar_web_time_on_site']              = $similar_web_time_on_site;
        $common_result['similar_web_page_views']                = $similar_web_page_views;
        $common_result['similar_web_bounce_rate']               = $similar_web_bounce;
        $common_result['similar_web_traffic_country']           = json_encode($similar_web_traffic_country);
        $common_result['similar_web_traffic_country_percentage']= json_encode($similar_web_traffic_country_percentage);
        $common_result['similar_web_direct_traffic']            = $similar_web_direct_traffic;
        $common_result['similar_web_referral_traffic']          = $similar_web_referral_traffic;
        $common_result['similar_web_search_traffic']            = $similar_web_search_traffic;
        $common_result['similar_web_social_traffic']            = $similar_web_social_traffic;
        $common_result['similar_web_mail_traffic']              = $similar_web_mail_traffic;
        $common_result['similar_web_display_traffic']           = $similar_web_display_traffic;
        $common_result['similar_web_top_referral_site']         = json_encode($similar_web_top_referral_site);
        $common_result['similar_web_top_destination_site']      = json_encode($similar_web_top_destination_site);
        $common_result['similar_web_organic_search_percentage'] = $similar_web_organic_search_percentage;
        $common_result['similar_web_paid_search_percentage']    = $similar_web_paid_search_percentage;
        $common_result['similar_web_top_organic_keyword']       = json_encode($similar_web_top_organic_keyword);
        $common_result['similar_web_top_paid_keyword']          = json_encode($similar_web_top_paid_keyword);
        $common_result['similar_web_social_site_name']          = json_encode($similar_web_social_site_name);
        $common_result['similar_web_social_site_percentage']    = json_encode($similar_web_social_site_percentage);
        $common_result['similar_web_status']                    = $similar_web_status;

        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". SimilarWeb ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);

        /********************** end of similar web information *************************/


        $fb_like_comment_share = $this->web_common_report->fb_like_comment_share(addHttp($domain_name));
        // $common_result['domain_id'] = $domain_info[0]['id'];
        if(isset($fb_like_comment_share['total_share']))
            $common_result['fb_total_share'] = $fb_like_comment_share['total_share'];
        else $common_result['fb_total_share'] = 0;

        if(isset($fb_like_comment_share['total_like']))
            $common_result['fb_total_like'] = $fb_like_comment_share['total_like'];
        else $common_result['fb_total_like'] = 0;

        if(isset($fb_like_comment_share['total_comment']))
            $common_result['fb_total_comment'] = $fb_like_comment_share['total_comment'];
        else $common_result['fb_total_comment'] = 0;

        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Facebook ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);




        $googleplus_info = $this->web_common_report->get_plusones($domain_name);
        $common_result['googleplus_share_count'] = $googleplus_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Google Plus ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $pinterest_info = $this->web_common_report->pinterest_pin($domain_name);
        $common_result['pinterest_pin'] = $pinterest_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Pinterest ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $stumbleupon_info = $this->web_common_report->stumbleupon_info($domain_name);
        $common_result['stumbleupon_total_view'] = $stumbleupon_info['total_view'];
        $common_result['stumbleupon_total_comment'] = $stumbleupon_info['total_comment'];
        $common_result['stumbleupon_total_like'] = $stumbleupon_info['total_like'];
        $common_result['stumbleupon_total_list'] = $stumbleupon_info['total_list'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Stumbleupon ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $linkdin_info = $this->web_common_report->linkdin_share($domain_name);
        $common_result['linkedin_share_count'] = $linkdin_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Linkedin ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $buffer_info = $this->web_common_report->buffer_share($domain_name);
        $common_result['buffer_share_count'] = $buffer_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Buffer ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        $GoogleIP = $this->web_common_report->GoogleIP($domain_name);
        $common_result['google_index_count'] = $GoogleIP;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Google Index ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
        

        $reddit_count = $this->web_common_report->reddit_count($domain_name);
        $common_result['reddit_score'] = $reddit_count['score'];
        $common_result['reddit_ups'] = $reddit_count['ups'];
        $common_result['reddit_downs'] = $reddit_count['downs'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Reddit ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $xing_share_count = $this->web_common_report->xing_share_count($domain_name);
        $common_result['xing_share_count'] = empty($xing_share_count) ? 0 : $xing_share_count;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Xing ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);
        



        $bing_index = $this->web_common_report->bing_index($domain_name);
        $common_result['bing_index_count'] = $bing_index;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Bing ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $yahoo_index = $this->web_common_report->yahoo_index($domain_name);
        $common_result['yahoo_index_count'] = $yahoo_index;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Yahoo ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $meta_tag_info = $this->web_common_report->content_analysis($domain_name);
        $common_result['h1'] = json_encode($meta_tag_info['h1']);
        $common_result['h2'] = json_encode($meta_tag_info['h2']);
        $common_result['h3'] = json_encode($meta_tag_info['h3']);
        $common_result['h4'] = json_encode($meta_tag_info['h4']);
        $common_result['h5'] = json_encode($meta_tag_info['h5']);
        $common_result['h6'] = json_encode($meta_tag_info['h6']);
        $common_result['blocked_by_robot_txt'] = $meta_tag_info['blocked_by_robot_txt'];
        $common_result['meta_tag_information'] = json_encode($meta_tag_info['meta_tag_information']);
        $common_result['blocked_by_meta_robot'] = $meta_tag_info['blocked_by_meta_robot'];
        $common_result['nofollowed_by_meta_robot'] = $meta_tag_info['nofollowed_by_meta_robot'];
        $common_result['one_phrase'] = json_encode($meta_tag_info['one_phrase']);
        $common_result['two_phrase'] = json_encode($meta_tag_info['two_phrase']);
        $common_result['three_phrase'] = json_encode($meta_tag_info['three_phrase']);
        $common_result['four_phrase'] = json_encode($meta_tag_info['four_phrase']);
        $common_result['total_words'] = $meta_tag_info['total_words'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Metatag ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $whois_info = @$this->web_common_report->whois_email($domain_name);
        $common_result['whois_is_registered'] = $whois_info['is_registered'];
        $common_result['whois_tech_email'] = $whois_info['tech_email'];
        $common_result['whois_admin_email'] = $whois_info['admin_email'];
        $common_result['whois_name_servers'] = $whois_info['name_servers'];
        $common_result['whois_created_at'] = $whois_info['created_at'];
        $common_result['whois_sponsor'] = $whois_info['sponsor'];
        $common_result['whois_changed_at'] = $whois_info['changed_at'];
        $common_result['whois_expire_at'] = $whois_info['expire_at'];
        $common_result['whois_registrar_url'] = $whois_info['registrar_url'];
        $common_result['whois_registrant_name'] = $whois_info['registrant_name'];
        $common_result['whois_registrant_organization'] = $whois_info['registrant_organization'];
        $common_result['whois_registrant_street'] = $whois_info['registrant_street'];
        $common_result['whois_registrant_city'] = $whois_info['registrant_city'];
        $common_result['whois_registrant_state'] = $whois_info['registrant_state'];
        $common_result['whois_registrant_postal_code'] = $whois_info['registrant_postal_code'];
        $common_result['whois_registrant_email'] = $whois_info['registrant_email'];
        $common_result['whois_registrant_country'] = $whois_info['registrant_country'];
        $common_result['whois_registrant_phone'] = $whois_info['registrant_phone'];
        $common_result['whois_admin_name'] = $whois_info['admin_name'];
        $common_result['whois_admin_street'] = $whois_info['admin_street'];
        $common_result['whois_admin_city'] = $whois_info['admin_city'];
        $common_result['whois_admin_state'] = $whois_info['admin_state'];
        $common_result['whois_admin_postal_code'] = $whois_info['admin_postal_code'];
        $common_result['whois_admin_country'] = $whois_info['admin_country'];
        $common_result['whois_admin_phone'] = $whois_info['admin_phone'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Whois ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $get_ip_country = $this->web_common_report->get_ip_country($domain_name);
        $common_result['ipinfo_isp'] = $get_ip_country['isp'];
        $common_result['ipinfo_ip'] = $get_ip_country['ip'];
        $common_result['ipinfo_city'] = $get_ip_country['city'];
        $common_result['ipinfo_region'] = $get_ip_country['region'];
        $common_result['ipinfo_country'] = $get_ip_country['country'];
        $common_result['ipinfo_time_zone'] = $get_ip_country['time_zone'];
        $common_result['ipinfo_longitude'] = $get_ip_country['longitude'];
        $common_result['ipinfo_latitude'] = $get_ip_country['latitude'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". IP ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        $this->web_common_report->get_site_in_same_ip($common_result['ipinfo_ip'],$page=1,$proxy=""); 
        $sites_in_same_ip=$this->web_common_report->same_site_in_ip; 
        $common_result['sites_in_same_ip']=json_encode($sites_in_same_ip);
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Site's in same IP - ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $macafee_safety_analysis = $this->web_common_report->macafee_safety_analysis($domain_name,$proxy="");
        $common_result['macafee_status'] = $macafee_safety_analysis;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Macafee ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);



        $norton_safety_check = $this->web_common_report->norton_safety_check($domain_name,$proxe="");
        $common_result['norton_status'] = $norton_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Norton ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        
        /*** configurable ***/
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

        $google_safety_check = $this->web_common_report->google_safety_check($api,$domain_name);
        $common_result['google_safety_status'] = $google_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Google Safety ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        
        $avg_safety_check = $this->web_common_report->avg_safety_check($domain_name,$proxy="");
        $common_result['avg_status'] = $avg_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". AVG ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);


        $similar_site_from_google = $this->web_common_report->similar_site_from_google($domain_name);
        $common_result['similar_site'] = implode(',', $similar_site_from_google);

        // echo $common_result['similar_site'];

        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('add_domain_complete_search', $add_complete);
        $completed_function_str .= "<div>".$add_complete.". Similar Site ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('completed_function_str', $completed_function_str);




        $where_common_check['where'] = array('domain_name'=>$domain_name,'user_id'=>$user_id);
        $where_common_update = array('domain_name'=>$domain_name,'user_id'=>$user_id);
        $temp = $this->basic->get_data('web_common_info',$where_common_check,$select=array('id'));
        
        if(!empty($temp)) {     
            $this->basic->update_data('web_common_info',$where_common_update,$common_result);
            $web_common_info_id = $temp[0]['id'];
            $this->session->set_userdata('insert_table_id_website_analysis', $web_common_info_id);

            $link = site_url()."domain/domain_details_view/".$web_common_info_id;
            echo '<a href="'.$link.'" class="btn btn-info btn-lg"><i class="fa fa-binoculars"></i> '.$this->lang->line("click here for detailed report").'</a><br/><br/>';        
        } else {
            if($this->basic->insert_data('web_common_info',$common_result)){
                
                $web_common_info_id = $this->db->insert_id();
                $this->session->set_userdata('insert_table_id_website_analysis', $web_common_info_id);

                //***************************************//
                // insert data to useges log table
                $this->_insert_usage_log($module_id=2,$request=1);
                //***************************************//
                
                $link = site_url()."domain/domain_details_view/".$web_common_info_id;
                echo '<a href="'.$link.'" class="btn btn-info btn-lg"><i class="fa fa-binoculars"></i> '.$this->lang->line("click here for detailed report").'</a><br/><br/>';
            }
            else
                echo 0;
        }

    }


    public function bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('add_domain_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('add_domain_complete_search');
        $completed_function_str=$this->session->userdata('completed_function_str');

        $insert_table_id=$this->session->userdata('insert_table_id_website_analysis');
        $response['view_details_button'] = 'not_set';
        if($insert_table_id != "")
        {

            $link = site_url()."domain/domain_details_view/".$insert_table_id;
            $view_button = '<a href="'.$link.'" class="btn btn-info btn-lg"><i class="fa fa-binoculars"></i> '.$this->lang->line("click here for detailed report").'</a><br/><br/>';
            $response['view_details_button'] = $view_button;
        }
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        $response['completed_function_str'] = $completed_function_str;
        
        echo json_encode($response);
        
    }

    public function update_common_info($domain_id=0)
    {
        if($this->update_common_info_action($domain_id))
            return $this->domain_list_for_domain_details();
        else
            return $this->domain_list_for_domain_details();
            
    }

    public function update_common_info_action($domain_name='',$user_id=0)
    {
        
        
      
    }


    public function ajax_get_general_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);

        $where = array();
        $where['where'] = array('id'=>$domain_id);
        $domain_info = $this->basic->get_data('web_common_info',$where,$select='');
        $data['country_list'] = $this->get_country_names();
        $data['domain_info'] = $domain_info;
        $domain_details = $this->load->view('domain_details/general',$data);
        
        echo $domain_details;

    }

    public function ajax_get_alexa_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["alexa_data"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));
        
        $alexa_details = $this->load->view('admin/ranking/alexa_details',$data);

        echo $alexa_details;
    }

    public function ajax_get_similarweb_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["similar_web"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));
        
        $similar_web_details = $this->load->view('domain_details/similar_web_details',$data);

        echo $similar_web_details;
    }

    public function ajax_get_social_network_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);

        $where = array();
        $where['where'] = array('id'=>$domain_id);
        $infos = $this->basic->get_data('web_common_info',$where,$select='');

        $domain_info = array();  
        $domain_info['domain_name'] = $infos[0]['domain_name'];

        $domain_info['fb_total_like'] = is_numeric($infos[0]['fb_total_like']) ? number_format($infos[0]['fb_total_like']) : $infos[0]['fb_total_like'];
        $domain_info['fb_total_share'] = is_numeric($infos[0]['fb_total_share']) ? number_format($infos[0]['fb_total_share']) : $infos[0]['fb_total_share'];
        $domain_info['fb_total_comment'] = is_numeric($infos[0]['fb_total_comment']) ? number_format($infos[0]['fb_total_comment']) : $infos[0]['fb_total_comment'];

        $domain_info['stumbleupon_total_view'] = is_numeric($infos[0]['stumbleupon_total_view']) ? number_format($infos[0]['stumbleupon_total_view']) : $infos[0]['stumbleupon_total_view'];
        $domain_info['stumbleupon_total_comment'] = is_numeric($infos[0]['stumbleupon_total_comment']) ? number_format($infos[0]['stumbleupon_total_comment']) : $infos[0]['stumbleupon_total_comment'];
        $domain_info['stumbleupon_total_like'] = is_numeric($infos[0]['stumbleupon_total_like']) ? number_format($infos[0]['stumbleupon_total_like']) : $infos[0]['stumbleupon_total_like'];
        $domain_info['stumbleupon_total_list'] = is_numeric($infos[0]['stumbleupon_total_list']) ? number_format($infos[0]['stumbleupon_total_list']) : $infos[0]['stumbleupon_total_list'];

        $domain_info['reddit_score'] = is_numeric($infos[0]['reddit_score']) ? number_format($infos[0]['reddit_score']) : $infos[0]['reddit_score'];
        $domain_info['reddit_ups'] = is_numeric($infos[0]['reddit_ups']) ? number_format($infos[0]['reddit_ups']) : $infos[0]['reddit_ups'];
        $domain_info['reddit_downs'] = is_numeric($infos[0]['reddit_downs']) ? number_format($infos[0]['reddit_downs']) : $infos[0]['reddit_downs'];

        $domain_info['google_plus_share'] = is_numeric($infos[0]['googleplus_share_count']) ? number_format($infos[0]['googleplus_share_count']) : $infos[0]['googleplus_share_count'];
        $domain_info['pinterest_pin'] = is_numeric($infos[0]['pinterest_pin']) ? number_format($infos[0]['pinterest_pin']) : $infos[0]['pinterest_pin'];
        $domain_info['buffer_share'] = is_numeric($infos[0]['buffer_share_count']) ? number_format($infos[0]['buffer_share_count']) : $infos[0]['buffer_share_count'];
        $domain_info['xing_share'] = is_numeric($infos[0]['xing_share_count']) ? number_format($infos[0]['xing_share_count']) : $infos[0]['xing_share_count'];
        $domain_info['linkedin_share'] = is_numeric($infos[0]['linkedin_share_count']) ? number_format($infos[0]['linkedin_share_count']) : $infos[0]['linkedin_share_count'];

        $domain_info['social_network_pieChart'] = array(
            0 => array(
                'value' => $infos[0]['fb_total_share'],
                'color' => '#44B3C2',
                'highlight' => '#44B3C2',
                'label' => 'Facebook Share'
                ),
            1 => array(
                'value' => $infos[0]['googleplus_share_count'],
                'color' => '#F1A94E',
                'highlight' => '#F1A94E',
                'label' => 'Google Plus Share'
                ),
            2 => array(
                'value' => $infos[0]['pinterest_pin'],
                'color' => '#86269B',
                'highlight' => '#86269B',
                'label' => 'Pinterest Pin'
                ),
            3 => array(
                'value' => $infos[0]['reddit_score'],
                'color' => '#F7F960',
                'highlight' => '#F7F960',
                'label' => 'Reddit Score'
                ),
            4 => array(
                'value' => $infos[0]['buffer_share_count'],
                'color' => '#FF534B',
                'highlight' => '#FF534B',
                'label' => 'Buffer Share'
                ),
            5 => array(
                'value' => $infos[0]['xing_share_count'],
                'color' => '#FE9601',
                'highlight' => '#FE9601',
                'label' => 'Xing Share'
                ),
            6 => array(
                'value' => $infos[0]['stumbleupon_total_list'],
                'color' => '#82683B',
                'highlight' => '#82683B',
                'label' => 'Stumbleupon List'
                ),
            7 => array(
                'value' => $infos[0]['linkedin_share_count'],
                'color' => '#E45641',
                'highlight' => '#E45641',
                'label' => 'Linkedin Share'
                )
            );
        $domain_info['color_codes'] = "
                                        <li><i class='fa fa-circle-o' style='color:#44B3C2'></i> Facebook Share </li>
                                        <li><i class='fa fa-circle-o' style='color:#F1A94E'></i> Google Plus Share </li>
                                        <li><i class='fa fa-circle-o' style='color:#86269B'></i> Pinterest Pin </li>
                                        <li><i class='fa fa-circle-o' style='color:#F7F960'></i> Reddit Score </li>
                                        <li><i class='fa fa-circle-o' style='color:#FF534B'></i> Buffer Share </li>
                                        <li><i class='fa fa-circle-o' style='color:#FE9601'></i> Xing Share </li>
                                        <li><i class='fa fa-circle-o' style='color:#82683B'></i> Stumbleupon List </li>
                                        <li><i class='fa fa-circle-o' style='color:#E45641'></i> Linkedin Share </li>
                                      ";
        echo json_encode($domain_info);
    }

    public function ajax_get_meta_tag_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["meta_tag_info"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));

        $meta_tag_info = $this->load->view('domain_details/meta_tag_details',$data);

        echo $meta_tag_info;
    }

    public function ajax_get_visitor_analysis_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        // this domain name will be placed for all the pages of visitor analysis tab
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_format(date_time,'%Y-%m-%d') >=" => $from_date,
            "date_format(date_time,'%Y-%m-%d') <=" => $to_date
            );
        $total_page_view = $this->basic->get_data($table,$where,$select='');

        $total_unique_visitor = $this->basic->get_data($table,$where,$select='',$join='',$limit='',$start='',$order_by='',$group_by='cookie_value');

        $total_unique_session = $this->basic->get_data($table,$where,$select='',$join='',$limit='',$start='',$order_by='',$group_by='session_value');

        // code for average stay time
        //"if(status='1',count(book_info.id),0) as available_book"
        $select = array(
            "date_time as stay_from",
            "last_engagement_time",
            "last_scroll_time"
            );
        $stay_time_info = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='');
        
        $total_stay_time = 0;
        if(!empty($stay_time_info)) {
            foreach($stay_time_info as $value){
                $total_stay_time_individual = 0;
                if($value['last_scroll_time']=='0000-00-00 00:00:00' && $value['last_engagement_time']=='0000-00-00 00:00:00')
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                else if ($value['last_scroll_time']=='0000-00-00 00:00:00' && $value['last_engagement_time']!='0000-00-00 00:00:00'){
                    $total_stay_time_individual = strtotime($value['last_engagement_time']) - strtotime($value['stay_from']);
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else if ($value['last_scroll_time']!='0000-00-00 00:00:00' && $value['last_engagement_time']=='0000-00-00 00:00:00'){
                   $total_stay_time_individual = strtotime($value['last_scroll_time']) - strtotime($value['stay_from']);
                   $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else {
                    if($value['last_scroll_time']>$value['last_engagement_time']){
                       $total_stay_time_individual = strtotime($value['last_scroll_time']) - strtotime($value['stay_from']);
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                    else{
                       $total_stay_time_individual = strtotime($value['last_engagement_time']) - strtotime($value['stay_from']);  
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                }
            }
        }


        $average_stay_time = 0;
        if($total_stay_time != 0)
            $average_stay_time = $total_stay_time/count($total_unique_session);

        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        $hours = floor($average_stay_time / 3600);
        $minutes = floor(($average_stay_time / 60) % 60);
        $seconds = $average_stay_time % 60;
        // end of average stay time

        // code for line chart
        $where = array();
        $where['where'] = array(
            "date_format(date_time,'%Y-%m-%d') >=" => $from_date,
            "date_format(date_time,'%Y-%m-%d') <=" => $to_date,
            "is_new" => 1
            );
        $select = array(
            "date_format(date_time,'%Y-%m-%d') as date",
            "count(id) as number_of_user"
            );
        $day_wise_visitor = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");

        foreach ($day_wise_visitor as $value){
            $day_wise_info[$value['date']] = $value['number_of_user'];
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();
        for($i=0;$i<=$no_of_days;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($day_wise_info[$day_count])){
                $line_char_data[$i]['user'] = $day_wise_info[$day_count];
            } else {
                $line_char_data[$i]['user'] = 0;
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }
        // end of code for line chart

        $info['line_chart'] = $line_char_data;
        $info['total_page_view'] = number_format(count($total_page_view));
        $info['total_unique_visitor'] = number_format(count($total_unique_visitor));
        $info['total_unique_session'] = number_format(count($total_unique_session));
        if(count($total_unique_visitor) == 0)
            $info['average_visit'] = number_format(count($total_page_view));
        else
            $info['average_visit'] = number_format(count($total_page_view)/count($total_unique_visitor), 2);

        $info['average_stay_time'] = $hours.":".$minutes.":".$seconds;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);

    }

    public function ajax_get_overview_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        // this domain name will be placed for all the pages of visitor analysis tab
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );
        $total_page_view = $this->basic->get_data($table,$where,$select='');

        $total_unique_visitor = $this->basic->get_data($table,$where,$select='',$join='',$limit='',$start='',$order_by='',$group_by='cookie_value');


        $select = array("count(id) as session_number","last_scroll_time","last_engagement_time");
        $total_unique_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='session_value');

        $bounce = 0;
        $no_bounce = 0;
        foreach($total_unique_session as $value){
            if($value['session_number'] > 1)
                $no_bounce++;
            if($value['session_number'] == 1){
                if($value['last_scroll_time']=="0000-00-00 00:00:00" && $value['last_engagement_time']=="0000-00-00 00:00:00")
                    $bounce++;
                else
                    $no_bounce++;
            }
        }
        $bounce_no_bounce = $bounce+$no_bounce;
        if($bounce_no_bounce == 0)
            $bounce_rate = 0;
        else
            $bounce_rate = number_format($bounce*100/$bounce_no_bounce, 2);

        // code for average stay time
        //"if(status='1',count(book_info.id),0) as available_book"
        $select = array(
            "date_time as stay_from",
            "last_engagement_time",
            "last_scroll_time"
            );
        $stay_time_info = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='');


        $total_stay_time = 0;
        if(!empty($stay_time_info)) {
            foreach($stay_time_info as $value){
                $total_stay_time_individual = 0;
                if($value['last_scroll_time']=='0000-00-00 00:00:00' && $value['last_engagement_time']=='0000-00-00 00:00:00')
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                else if ($value['last_scroll_time']=='0000-00-00 00:00:00' && $value['last_engagement_time']!='0000-00-00 00:00:00'){
                    $total_stay_time_individual = strtotime($value['last_engagement_time']) - strtotime($value['stay_from']);
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else if ($value['last_scroll_time']!='0000-00-00 00:00:00' && $value['last_engagement_time']=='0000-00-00 00:00:00'){
                   $total_stay_time_individual = strtotime($value['last_scroll_time']) - strtotime($value['stay_from']);
                   $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else {
                    if($value['last_scroll_time']>$value['last_engagement_time']){
                       $total_stay_time_individual = strtotime($value['last_scroll_time']) - strtotime($value['stay_from']);
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                    else{
                       $total_stay_time_individual = strtotime($value['last_engagement_time']) - strtotime($value['stay_from']);  
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                }
            }
        }


        $average_stay_time = 0;
        if($total_stay_time != 0)
            $average_stay_time = $total_stay_time/count($total_unique_session);

        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        $hours = floor($average_stay_time / 3600);
        $minutes = floor(($average_stay_time / 60) % 60);
        $seconds = $average_stay_time % 60;        

        // end of average stay time

        // code for line chart
        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "is_new" => 1
            );
        $select = array(
            "date_time as date",
            "count(id) as number_of_user"
            );
        $day_wise_visitor = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");


        foreach ($day_wise_visitor as $value){
            $day_wise_info[$value['date']] = $value['number_of_user'];
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();
        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($day_wise_info[$day_count])){
                $line_char_data[$i]['user'] = $day_wise_info[$day_count];
            } else {
                $line_char_data[$i]['user'] = 0;
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }
        // end of code for line chart

        $info['line_chart'] = $line_char_data;
        $info['total_page_view'] = number_format(count($total_page_view));
        $info['total_unique_visitor'] = number_format(count($total_unique_visitor));
        $info['total_unique_session'] = number_format(count($total_unique_session));
        if(count($total_unique_visitor) == 0)
            $info['average_visit'] = number_format(count($total_page_view));
        else
            $info['average_visit'] = number_format(count($total_page_view)/count($total_unique_visitor), 2);

        $info['average_stay_time'] = $hours.":".$minutes.":".$seconds;
        $info['bounce_rate'] = $bounce_rate." %";
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }

    public function ajax_get_traffic_source_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select=array("date_time as date_test","session_value","GROUP_CONCAT(referrer SEPARATOR ',') as referrer","GROUP_CONCAT(visit_url SEPARATOR ',') as visit_url_str");

        $traffic_source_info = $this->basic->get_data($table,array(),$select,$join='',$limit='',$start='',$order_by='',$group_by='session_value');

        // echo $this->db->last_query(); exit();

        $daily_traffic_source_info = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='session_value,date_test');

        
        $search_engine_array = array('Baidu','Bing','DuckDuckGo','Ecosia','Exalead','Gigablast','Google','Munax','Qwant','Sogou','Soso.com','Yahoo','Yandex','Youdao','FAROO','YaCy','DeeperWeb','Dogpile','Excite','HotBot','Info.com','Mamma','Metacrawler','Mobissimo','Otalo','Skyscanner','WebCrawler','Accoona','Ansearch','Biglobe','Daum','Egerin','Leit.is','Maktoob','Miner.hu','Najdi.si','Naver','Onkosh','Rambler','Rediff','SAPO','Search.ch','Sesam','Seznam','Walla!','Yandex.ru','ZipLocal');
        $social_network_array = array('Twitter','Facebook','Xing','Renren','plus.Google','Disqus','Linkedin Pulse','Snapchat','Tumblr','Pintarest','Twoo','MyMFB','Instagram','Vine','WhatsApp','vk.com','Meetup','Secret','Medium','Youtube');


        $search_link_count = 0;
        $social_link_count = 0;
        $referrer_link_count = 0;
        $direct_link_count = 0;

        $k = 0;
        $referrer_info = array();
        $search_engine_info = array();
        $social_network_info = array();
        $referrer_name = array();

        // print_r($traffic_source_info); exit();

        foreach($traffic_source_info as $value){
            $referrer_array = array();
            if($value['referrer'] != ''){
                $referrer_array = explode(',', $value['referrer']);
                // $empty_reffer = array_filter($referrer_array);
                // $empty_reffer = array_values($empty_reffer);

                $visit_url = explode(',', $value['visit_url_str']);
                // $visit_url = array_filter($visit_url);
                // $visit_url = array_values($visit_url);            
            }

            // print_r($referrer_array);

            if(empty($referrer_array)){
                $direct_link_count++;

                if(isset($referrer_info['direct_link']))
                    $referrer_info['direct_link']++;
                else
                   $referrer_info['direct_link'] = 1;
            }
            else{
                $first_part_of_domain_array = array();
                $first_index_of_referrer = get_domain_only($referrer_array[0]);
                $first_index_of_url = get_domain_only($visit_url[0]);
                /** creating referrer info array with count **/
                for($i=0;$i<count($referrer_array);$i++){                    
                    
                    if($first_index_of_referrer != $first_index_of_url && $referrer_array[0] != ''){
                        if(isset($referrer_info[$referrer_array[$i]]))
                            $referrer_info[$referrer_array[$i]]++;
                        else 
                            $referrer_info[$referrer_array[$i]] = 1;
                    }
                    $only_domain_name = get_domain_only($referrer_array[$i]);
                    $first_part_of_domain_array[] = $only_domain_name; 
                    
                } // end of for loop

                if($first_index_of_referrer == $first_index_of_url){
                    $direct_link_count++;
                    if(isset($referrer_info['direct_link']))
                        $referrer_info['direct_link']++;
                    else
                       $referrer_info['direct_link'] = 1;
                }
                if($referrer_array[0] == ''){
                    $direct_link_count++;
                    if(isset($referrer_info['direct_link']))
                        $referrer_info['direct_link']++;
                    else
                       $referrer_info['direct_link'] = 1;
                } 


                $count_search_engine = array();
                $count_social_network = array();
                /** for social network and search engine array creation and counter **/
                for($i=0;$i<count($first_part_of_domain_array);$i++){

                    for($j=0;$j<count($search_engine_array);$j++){
                        $occurance_search_engine = stripos($first_part_of_domain_array[$i], $search_engine_array[$j]);
                        if($occurance_search_engine !== FALSE){
                            if(isset($search_engine_info[$search_engine_array[$j]])){
                                $search_engine_info[$search_engine_array[$j]]++;
                                $count_search_engine[] = $search_engine_array[$j];
                            }
                            else{
                                $search_engine_info[$search_engine_array[$j]] = 1;
                                $count_search_engine[] = $search_engine_array[$j];
                            }
                        }
                    } // end of for loop
                    
                    for($k=0;$k<count($social_network_array);$k++){
                        $occurance_social_network = stripos($first_part_of_domain_array[$i], $social_network_array[$k]);
                        if($occurance_social_network !== FALSE){
                            if(isset($social_network_info[$social_network_array[$k]])){
                                $social_network_info[$social_network_array[$k]]++;
                                $count_social_network[] = $social_network_array[$k];
                            }
                            else{
                                $social_network_info[$social_network_array[$k]] = 1;
                                $count_social_network[] = $social_network_array[$k];
                            }
                        }
                    } // end of for loop

                } // end of for loop

                if(!empty($count_search_engine)){
                    $search_link_count = $search_link_count + count($count_search_engine);
                }
                if(!empty($count_social_network)){
                    $social_link_count = $social_link_count + count($count_social_network);
                }
                if(empty($count_search_engine) && empty($count_social_network)){
                    if($first_index_of_referrer != $first_index_of_url && $first_index_of_referrer != '')
                        $referrer_link_count = $referrer_link_count + count($first_part_of_domain_array);
                }

            }

        }

        // print_r($social_network_info); exit();

        // for top five referrer section
        $total_referrers = $direct_link_count+$search_link_count+$social_link_count+$referrer_link_count;
        $top_referrer = asort($referrer_info);
        $top_referrer = array_reverse($referrer_info);
        $top_referrer_keys = array_keys($top_referrer);
        $top_referrer_values = array_values($top_referrer);
        $no_of_top_referrer = 0;
        if(count($top_referrer)>5) $no_of_top_referrer = 5;
        else $no_of_top_referrer = count($top_referrer);

        $color_array = array("#44B3C2", "#F1A94E", "#E45641", "#5D4C46", "#7B8D8E");
        $top_five_referrer = array();
        for($i=0;$i<$no_of_top_referrer;$i++){
            $top_five_referrer[$i]['value'] = number_format($top_referrer_values[$i]*100/$total_referrers,2);
            $top_five_referrer[$i]['color'] = $color_array[$i];
            $top_five_referrer[$i]['highlight'] = $color_array[$i];
            if($top_referrer_keys[$i] == 'direct_link')
                $link_name = "Direct Link";
            else $link_name = $top_referrer_keys[$i];
            $top_five_referrer[$i]['label'] = $link_name;
        }

        $info['top_referrer_data'] = $top_five_referrer;
        //end of top five referrer section

        //section for search engine info
        $search_engine_info_keys = array_keys($search_engine_info);
        $search_engine_info_values = array_values($search_engine_info);
        $search_engine_color = array("#44B3C2","#F1A94E","#E45641","#5D4C46","#7B8D8E","#F2EDD8","#BCCF3D","#BCCF3D","#82683B","#B6A754","#D79C8C");
        $j = 0;
        $search_engine_result = array();
        $search_engine_names = array();
        for($i=0;$i<count($search_engine_info);$i++){
            $search_engine_result[$i]['value'] = $search_engine_info_values[$i];
            $search_engine_result[$i]['color'] = $search_engine_color[$j];
            $search_engine_result[$i]['highlight'] = $search_engine_color[$j];
            $search_engine_result[$i]['label'] = "visitor from ".$search_engine_info_keys[$i];

            $search_engine_names[$i]['name'] = $search_engine_info_keys[$i];
            $search_engine_names[$i]['color'] = $search_engine_color[$j];
            $j++;
            if($j == 10) $j=0;
        }

        $info['search_engine_info'] = $search_engine_result;

        $search_engine_names_str = '';
        foreach($search_engine_names as $value){
            $search_engine_names_str .= "<li><i class='fa fa-circle-o' style='color:".$value['color']."'></i> ".$value['name']." </li>";
        }
        $info['search_engine_names'] = $search_engine_names_str;
        // end of search engine info

        
        //social network info
        $social_network_info_keys = array_keys($social_network_info);
        $social_network_info_values = array_values($social_network_info);
        $social_network_color = array_reverse(array("#FCF4D9","#8ED2C9","#00AAA0","#FF7A5A","#FFB85F","#462066","#BCCF3D","#BCCF3D","#82683B","#B6A754","#D79C8C"));
        $j = 0;
        $social_network_result = array();
        $social_network_names = array();
        for($i=0;$i<count($social_network_info);$i++){
            $social_network_result[$i]['value'] = $social_network_info_values[$i];
            $social_network_result[$i]['color'] = $social_network_color[$j];
            $social_network_result[$i]['highlight'] = $social_network_color[$j];
            $social_network_result[$i]['label'] = "visitor from ".$social_network_info_keys[$i];

            $social_network_names[$i]['name'] = $social_network_info_keys[$i];
            $social_network_names[$i]['color'] = $social_network_color[$j];
            $j++;
            if($j == 10) $j=0;
        }

        $info['social_network_info'] = $social_network_result;

        $social_network_names_str = '';
        foreach($social_network_names as $value){
            $social_network_names_str .= "<li><i class='fa fa-circle-o' style='color:".$value['color']."'></i> ".$value['name']." </li>";
        }
        $info['social_network_names'] = $social_network_names_str;
        // end of social network info

        // print_r($daily_traffic_source_info); exit();

        $day_wise_search_link_count = 0;
        $day_wise_social_link_count = 0;
        $day_wise_referrer_link_count = 0;
        $day_wise_direct_link_count = 0;

        //for daily report section
        $visit_url = array();
        foreach($daily_traffic_source_info as $value){
            $referrer_array = array();
            if(isset($value['referrer'])){
                $referrer_array = explode(',', $value['referrer']);
                $empty_referrer_array = array_filter($referrer_array);
                $empty_referrer_array = array_values($empty_referrer_array);

                $visit_url = explode(',', $value['visit_url_str']);
                // $visit_url = array_filter($visit_url);
                // $visit_url = array_values($visit_url); 
            }

            if(empty($empty_referrer_array)){

                $day_wise_direct_link_count++;
                if(isset($daily_report[$value['date_test']]['direct_link_count']))
                    $daily_report[$value['date_test']]['direct_link_count'] = $daily_report[$value['date_test']]['direct_link_count'] + $day_wise_direct_link_count;
                else
                    $daily_report[$value['date_test']]['direct_link_count'] = $day_wise_direct_link_count;
                $day_wise_direct_link_count = 0;

            }
            else{
                $first_part_of_domain_array = array();
                for($i=0;$i<count($referrer_array);$i++){
                    $only_domain_name = get_domain_only($referrer_array[$i]);
                    $first_part_of_domain_array[] = $only_domain_name;  
                }

                $first_index_of_referrer = get_domain_only($referrer_array[0]);
                $first_index_of_url = get_domain_only($visit_url[0]);
                if($first_index_of_referrer == $first_index_of_url){
                    $day_wise_direct_link_count++;
                    if(isset($daily_report[$value['date_test']]['direct_link_count']))
                        $daily_report[$value['date_test']]['direct_link_count'] = $daily_report[$value['date_test']]['direct_link_count'] + $day_wise_direct_link_count;
                    else
                       $daily_report[$value['date_test']]['direct_link_count'] = $day_wise_direct_link_count;
                   $day_wise_direct_link_count = 0;
                }
                if($referrer_array[0] == ''){
                    $day_wise_direct_link_count++;
                    if(isset($daily_report[$value['date_test']]['direct_link_count']))
                        $daily_report[$value['date_test']]['direct_link_count'] = $daily_report[$value['date_test']]['direct_link_count'] + $day_wise_direct_link_count;
                    else
                       $daily_report[$value['date_test']]['direct_link_count'] = $day_wise_direct_link_count;
                   $day_wise_direct_link_count = 0;
                }

                $count_search_engine = array();
                $count_social_network = array();

                for($i=0;$i<count($first_part_of_domain_array);$i++){

                    for($j=0;$j<count($search_engine_array);$j++){
                        $occurance_search_engine = stripos($first_part_of_domain_array[$i], $search_engine_array[$j]);
                        if($occurance_search_engine !== FALSE){
                            $count_search_engine[] = $search_engine_array[$j];
                        }
                    }
                    
                    for($k=0;$k<count($social_network_array);$k++){
                        $occurance_social_network = stripos($first_part_of_domain_array[$i], $social_network_array[$k]);
                        if($occurance_social_network !== FALSE){
                            $count_social_network[] = $social_network_array[$k];
                        }
                    }

                }                

                if(!empty($count_search_engine)){
                    $day_wise_search_link_count = $day_wise_search_link_count + count($count_search_engine);
                    if(isset($daily_report[$value['date_test']]['search_link_count']))
                        $daily_report[$value['date_test']]['search_link_count'] = $daily_report[$value['date_test']]['search_link_count'] + $day_wise_search_link_count;
                    else
                        $daily_report[$value['date_test']]['search_link_count'] = $day_wise_search_link_count;
                    $day_wise_search_link_count = 0;
                }
                if(!empty($count_social_network)){
                    $day_wise_social_link_count = $day_wise_social_link_count + count($count_social_network);
                    if(isset($daily_report[$value['date_test']]['social_link_count']))
                        $daily_report[$value['date_test']]['social_link_count'] = $daily_report[$value['date_test']]['social_link_count'] + $day_wise_social_link_count;
                    else
                        $daily_report[$value['date_test']]['social_link_count'] = $day_wise_social_link_count;
                    $day_wise_social_link_count = 0;
                }
                if(empty($count_search_engine) && empty($count_social_network)) {
                    if($first_index_of_referrer != $first_index_of_url && $first_index_of_referrer != ''){

                        $day_wise_referrer_link_count = $day_wise_referrer_link_count + count($first_part_of_domain_array);
                        if(isset($daily_report[$value['date_test']]['referrer_link_count']))
                            $daily_report[$value['date_test']]['referrer_link_count'] = $daily_report[$value['date_test']]['referrer_link_count'] + $day_wise_referrer_link_count;
                        else
                            $daily_report[$value['date_test']]['referrer_link_count'] = $day_wise_referrer_link_count;
                        $day_wise_referrer_link_count = 0;
                    }
                }

            }
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();
        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($daily_report[$day_count])){
                if(isset($daily_report[$day_count]['direct_link_count']))
                    $line_char_data[$i]['direct_link'] = $daily_report[$day_count]['direct_link_count'];
                else
                    $line_char_data[$i]['direct_link'] = 0;

                if(isset($daily_report[$day_count]['search_link_count']))
                    $line_char_data[$i]['search_link'] = $daily_report[$day_count]['search_link_count'];
                else
                    $line_char_data[$i]['search_link'] = 0;

                if(isset($daily_report[$day_count]['social_link_count']))
                    $line_char_data[$i]['social_link'] = $daily_report[$day_count]['social_link_count'];
                else
                    $line_char_data[$i]['social_link'] = 0;

                if(isset($daily_report[$day_count]['referrer_link_count']))
                    $line_char_data[$i]['referrer_link'] = $daily_report[$day_count]['referrer_link_count'];
                else
                    $line_char_data[$i]['referrer_link'] = 0;
            } else {
                $line_char_data[$i]['direct_link'] = 0;
                $line_char_data[$i]['search_link'] = 0;
                $line_char_data[$i]['social_link'] = 0;
                $line_char_data[$i]['referrer_link'] = 0;
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['line_chart_data'] = $line_char_data;
        // end of daily report section

        $info['bar_chart_data'] = array(
            0 => array('source_name' => 'Direct Link', 'value' => $direct_link_count),
            1 => array('source_name' => 'Search Engine', 'value' => $search_link_count),
            2 => array('source_name' => 'Social Network', 'value' => $social_link_count),
            3 => array('source_name' => 'Referal', 'value' => $referrer_link_count)
            );

        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }

    public function ajax_get_visitor_type_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select=array("GROUP_CONCAT(is_new SEPARATOR ',') as new_vs_returning");
        $total_new_returning = $this->basic->get_data($table,$where2="",$select,$join="",$limit='',$start='',$order_by='',$group_by='cookie_value,session_value');


        $new_or_returning = array();
        $new_user = 0;
        $returning_user = 0;
        foreach($total_new_returning as $value){
            $new_or_returning = explode(',', $value['new_vs_returning']);
            if(in_array(1, $new_or_returning)) $new_user++;
            else $returning_user++;
        }

        $info['total_new_returning'] = array(
            0 => array(
                "value" => $new_user,
                "color" => "#C8EBE5",
                "highlight" => "#C8EBE5",
                "label" => "New Users",
                ),
            1 => array(
                "value" => $returning_user,
                "color" => "#F5A196",
                "highlight" => "#F5A196",
                "label" => "Returning Users",
                )
            );

        $select=array("date_time as date","GROUP_CONCAT(is_new SEPARATOR ',') as new_vs_returning");
        $daily_total_new_returning = $this->basic->get_data($table,$where,$select,$join="",$limit='',$start='',$order_by='',$group_by='cookie_value,session_value,date');


        $daily_report = array();
        $new_or_returning = array();
        $new_user = 0;
        $returning_user = 0;
        $i = 0;
        foreach($daily_total_new_returning as $value){
            $daily_report[$value['date']]['date'] = $value['date'];

            $new_or_returning = explode(',', $value['new_vs_returning']);                
            if(in_array(1, $new_or_returning)){
                if(isset($daily_report[$value['date']]['new_user'])){
                    $daily_report[$value['date']]['new_user']=$daily_report[$value['date']]['new_user']+1;
                }
                else{
                   $daily_report[$value['date']]['new_user'] = 1; 
                }
            } 
            else {
                if(isset($daily_report[$value['date']]['returning_user']))
                    $daily_report[$value['date']]['returning_user']=$daily_report[$value['date']]['returning_user']+1;
                else{
                   $daily_report[$value['date']]['returning_user'] = 1;
                }
            }
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();

        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($daily_report[$day_count])){
                if(isset($daily_report[$day_count]['new_user']))
                    $line_char_data[$i]['new_user'] = $daily_report[$day_count]['new_user'];
                else
                    $line_char_data[$i]['new_user'] = 0;

                if(isset($daily_report[$day_count]['returning_user']))
                    $line_char_data[$i]['returning_user'] = $daily_report[$day_count]['returning_user'];
                else
                    $line_char_data[$i]['returning_user'] = 0;

            } else {
                $line_char_data[$i]['new_user'] = 0;
                $line_char_data[$i]['returning_user'] = 0;                
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['daily_new_vs_returning'] = $line_char_data;

        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }

    public function ajax_get_content_overview_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $table_name = $this->basic->get_data('domain',$where,$select=array('table_name'));
        $table = $table_name[0]['table_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select = array("count(id) as total_view","visit_url");
        $content_overview_data = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='total_view desc',$group_by='visit_url');

        $total_view = 0;
        foreach($content_overview_data as $value){
            $total_view = $total_view+$value['total_view'];
        }

        $top_url = '';
        $i = 0;
        foreach($content_overview_data as $value){
            $percentage = number_format($value['total_view']*100/$total_view, 2);
            $i++;
            $top_url .= $i.". ".$value['visit_url']." <span class='pull-right'><b>".$percentage." %</b></span>";
            $top_url .= 
            '<div class="progress">                                         
              <div class="progress-bar progress-bar-striped " role="progressbar" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$percentage.'%">
              </div>
            </div>';
            if($i==10) break;
        }

        $info['progress_bar_data'] = $top_url;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));
        echo json_encode($info);
    }

    public function ajax_get_country_wise_report_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );
        $select = array('country',"GROUP_CONCAT(is_new SEPARATOR ',') as new_user");
        $country_name = $this->basic->get_data($table,'',$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='country');

        $i = 0;
        $country_report = array();
        $a = array('Country','New Visitor');
        $country_report[$i] = $a;
        foreach($country_name as $value){
            $new_users = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);
            $temp = array();
            $temp[] = $value['country'];
            $temp[] = $new_users;
            $country_report[$i] = $temp;
        }

        $info['country_graph_data'] = $country_report;

        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","country");
        $browser_report = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='country');

        $country_report_str = "<table class='table table-hover'>
                                    <tr style='background:#D9FDC7;color:blue'>
                                        <th>SL</th>
                                        <th>Country Name</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";
        $country_list = $this->get_country_names();       
        $i = 0;
        foreach($browser_report as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $s_country = array_search(trim(strtoupper($value["country"])), $country_list); 
            $image_link = base_url()."assets/images/flags/".$s_country.".png";
            $country_report_str .= "<tr class='country_wise_name' style='cursor:pointer;' data='".$value['country']."'><td>".$i."</td><td>".'<img style="height: 15px; width: 20px; margin-top: -3px;" src="'.$image_link.'" alt=" "> &nbsp;'.$value['country']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $country_report_str .= "</table>";
        $info['country_wise_table_data'] = $country_report_str;

        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }

    public function ajax_get_individual_country_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $country_name = $this->input->post('country_name', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $table_name = $this->basic->get_data('domain',$where,$select=array('table_name'));
        $table = $table_name[0]['table_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "country" => $country_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_time as date");
        $country_daily_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='city');

        foreach($country_daily_session as $value){
            $sessions = array();
            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);
            $report[$value['date']]['sessions'] = $sessions;
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();

        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($report[$day_count])){
                $line_char_data[$i]['session'] = $report[$day_count]['sessions'];
            } else {
                $line_char_data[$i]['session'] = 0;               
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['country_daily_session_data'] = $line_char_data;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));



        $where_version = array();
        $where_version['where'] = array(
            'country' => $country_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","country","city");
        $country_city = array();
        $country_city = $this->basic->get_data($table,$where_version,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='city');

        $country_city_str = "<table class='table table-hover'>
                                    <tr style='background:#0073B7;color:white'>
                                        <th>SL</th>
                                        <th>Country Name</th>
                                        <th>City Name</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";
        $country_list_individual = $this->get_country_names();       
        $i = 0;
        foreach($country_city as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $s_country = array_search(trim(strtoupper($value["country"])), $country_list_individual); 
            $image_link = base_url()."assets/images/flags/".$s_country.".png";

            $country_city_str .= "<tr><td>".$i."</td><td>".'<img style="height: 15px; width: 20px; margin-top: -3px;" src="'.$image_link.'" alt=" "> &nbsp;'.$value['country']."</td><td>".$value['city']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $country_city_str .= "</table>";

        $info['country_city_str'] = $country_city_str;

        echo json_encode($info);
    }

    public function ajax_get_browser_report_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","browser_name");
        $browser_report = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='browser_name');

        $browser_report_str = "<table class='table table-hover'>
                                    <tr style='background:#0073B7;color:white'>
                                        <th>SL</th>
                                        <th>Browser Name</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";        
        $i = 0;
        foreach($browser_report as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $browser_report_str .= "<tr class='browser_name' style='cursor:pointer;' data='".$value['browser_name']."'><td>".$i."</td><td>".$value['browser_name']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $browser_report_str .= "</table>";

        $info['browser_report_name'] = $browser_report_str;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));


        echo json_encode($info);
    }

    public function ajax_get_individual_browser_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $browser_name = $this->input->post('browser_name', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $table_name = $this->basic->get_data('domain',$where,$select=array('table_name'));
        $table = $table_name[0]['table_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "browser_name" => $browser_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_time as date");
        $browser_daily_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='date');
        foreach($browser_daily_session as $value){
            $sessions = array();
            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);
            $report[$value['date']]['sessions'] = $sessions;
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();

        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($report[$day_count])){
                $line_char_data[$i]['session'] = $report[$day_count]['sessions'];
            } else {
                $line_char_data[$i]['session'] = 0;               
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['browser_daily_session_data'] = $line_char_data;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));



        $where_version = array();
        $where_version['where'] = array(
            'browser_name' => $browser_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","browser_version","browser_name");
        $browser_versions = $this->basic->get_data($table,$where_version,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='browser_version');

        $browser_version_str = "<table class='table table-hover'>
                                    <tr style='background:#0073B7;color:white'>
                                        <th>SL</th>
                                        <th>Browser Name</th>
                                        <th>Browser Version</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";        
        $i = 0;
        foreach($browser_versions as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $browser_version_str .= "<tr><td>".$i."</td><td>".$value['browser_name']."</td><td>".$value['browser_version']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $browser_version_str .= "</table>";

        $info['browser_version'] = $browser_version_str;

        echo json_encode($info);
    }



    public function ajax_get_os_report_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","os");
        $os_report = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='os');

        $os_report_str = "<table class='table table-hover'>
                                    <tr style='background:#00AAA0;color:white'>
                                        <th>SL</th>
                                        <th>OS Name</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";        
        $i = 0;
        foreach($os_report as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $os_report_str .= "<tr class='os_name' style='cursor:pointer;' data='".$value['os']."'><td>".$i."</td><td>".$value['os']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $os_report_str .= "</table>";
        $info['os_report_name'] = $os_report_str;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);

    }


    public function ajax_get_individual_os_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $os_name = $this->input->post('os_name', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $table_name = $this->basic->get_data('domain',$where,$select=array('table_name'));
        $table = $table_name[0]['table_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "os" => $os_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_time as date");
        $browser_daily_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='date');
        foreach($browser_daily_session as $value){
            $sessions = array();
            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);
            $report[$value['date']]['sessions'] = $sessions;
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();

        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($report[$day_count])){
                $line_char_data[$i]['session'] = $report[$day_count]['sessions'];
            } else {
                $line_char_data[$i]['session'] = 0;               
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['os_daily_session_data'] = $line_char_data;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }



    public function ajax_get_device_report_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $domain_info = $this->basic->get_data('domain',$where,$select="");
        $table = $domain_info[0]['table_name'];
        $info['domain_name'] = $domain_info[0]['domain_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );

        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","GROUP_CONCAT(is_new SEPARATOR ',') as new_user","device");
        $device_report = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='device');

        $device_report_str = "<table class='table table-hover'>
                                    <tr style='background:#FF7A5A;color:white'>
                                        <th>SL</th>
                                        <th>Device Name</th>
                                        <th>Sessions</th>
                                        <th>New Users</th>
                                    </tr>
                                ";        
        $i = 0;
        foreach($device_report as $value){
            $new_users = array();
            $sessions = array();
            $i++;
            $new_users = explode(',', $value['new_user']);
            $new_users = array_filter($new_users);
            $new_users = array_values($new_users);
            $new_users = count($new_users);

            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);

            $device_report_str .= "<tr class='device_name' style='cursor:pointer;' data='".$value['device']."'><td>".$i."</td><td>".$value['device']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

        }
        $device_report_str .= "</table>";

        $info['device_report_name'] = $device_report_str;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }


    public function ajax_get_individual_device_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $date_range = $this->input->post('date_range', TRUE);
        $device_name = $this->input->post('device_name', TRUE);

        $from_and_to_date = array();
        if ($date_range != '') {
            $from_and_to_date = explode(" - ", $date_range);
        }

        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        if (!empty($from_and_to_date)) {
            $from_date = date("Y-m-d",strtotime($from_and_to_date[0]));
            $to_date = date("Y-m-d",strtotime($from_and_to_date[1]));
        }

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

        $where = array();
        $where['where'] = array('id' => $domain_id);
        $table_name = $this->basic->get_data('domain',$where,$select=array('table_name'));
        $table = $table_name[0]['table_name'];

        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "device" => $device_name
            );
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_time as date");
        $browser_daily_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='date');
        foreach($browser_daily_session as $value){
            $sessions = array();
            $sessions = explode(',', $value['sessions']);
            $sessions = array_filter($sessions);
            $sessions = array_values($sessions);
            $sessions = array_unique($sessions);
            $sessions = count($sessions);
            $report[$value['date']]['sessions'] = $sessions;
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();

        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($report[$day_count])){
                $line_char_data[$i]['session'] = $report[$day_count]['sessions'];
            } else {
                $line_char_data[$i]['session'] = 0;               
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }

        $info['device_daily_session_data'] = $line_char_data;
        $info['from_date'] = date("d-M-y",strtotime($from_date));
        $info['to_date'] = date("d-M-y",strtotime($to_date));

        echo json_encode($info);
    }


    public function delete_domain($id=0)
    {
        $this->db->trans_start();

        $this->basic->delete_data('web_common_info',$where=array('id'=>$id));

        $this->db->trans_complete();
        if($this->db->trans_status() === false) {
            $this->session->set_userdata('delete_error',1);
            redirect('domain/domain_list_for_domain_details','Location');
        } else {
            $this->session->set_userdata('delete_success',1);
            redirect('domain/domain_list_for_domain_details','Location');
        }
    }



    public function download_pdf()
    {
        $id = $this->input->post('table_id',true);
        $data["user_data"] = $this->basic->get_data("users",array("where"=>array("id"=>$this->session->userdata("user_id"))));
        
        $where = array();        
        $where['where'] = array('id'=>$id);        
        $domain_info = $this->basic->get_data('web_common_info',$where,$select='');
        $data['country_list'] = $this->get_country_names();
        $data['domain_info'] = $domain_info;
        $data["similar_web"] = $domain_info;

        $info['fb_total_like'] = is_numeric($domain_info[0]['fb_total_like']) ? number_format($domain_info[0]['fb_total_like']) : 0;
        $info['fb_total_share'] = is_numeric($domain_info[0]['fb_total_share']) ? number_format($domain_info[0]['fb_total_share']) : 0;
        $info['fb_total_comment'] = is_numeric($domain_info[0]['fb_total_comment']) ? number_format($domain_info[0]['fb_total_comment']) : 0;

        $info['stumbleupon_total_view'] = is_numeric($domain_info[0]['stumbleupon_total_view']) ? number_format($domain_info[0]['stumbleupon_total_view']) : 0;
        $info['stumbleupon_total_comment'] = is_numeric($domain_info[0]['stumbleupon_total_comment']) ? number_format($domain_info[0]['stumbleupon_total_comment']) : 0;
        $info['stumbleupon_total_like'] = is_numeric($domain_info[0]['stumbleupon_total_like']) ? number_format($domain_info[0]['stumbleupon_total_like']) : 0;
        $info['stumbleupon_total_list'] = is_numeric($domain_info[0]['stumbleupon_total_list']) ? number_format($domain_info[0]['stumbleupon_total_list']) : 0;

        $info['reddit_score'] = is_numeric($domain_info[0]['reddit_score']) ? number_format($domain_info[0]['reddit_score']) : 0;
        $info['reddit_ups'] = is_numeric($domain_info[0]['reddit_ups']) ? number_format($domain_info[0]['reddit_ups']) : 0;
        $info['reddit_downs'] = is_numeric($domain_info[0]['reddit_downs']) ? number_format($domain_info[0]['reddit_downs']) : 0;

        $info['google_plus_share'] = is_numeric($domain_info[0]['googleplus_share_count']) ? number_format($domain_info[0]['googleplus_share_count']) : 0;
        $info['pinterest_pin'] = is_numeric($domain_info[0]['pinterest_pin']) ? number_format($domain_info[0]['pinterest_pin']) : 0;
        $info['buffer_share'] = is_numeric($domain_info[0]['buffer_share_count']) ? number_format($domain_info[0]['buffer_share_count']) : 0;
        $info['xing_share'] = is_numeric($domain_info[0]['xing_share_count']) ? number_format($domain_info[0]['xing_share_count']) : 0;
        $info['linkedin_share'] = is_numeric($domain_info[0]['linkedin_share_count']) ? number_format($domain_info[0]['linkedin_share_count']) : 0;

        $data['info'] = $info;

        ob_start();
        $this->load->view("website_analysis_pdf/report",$data); 
        ob_get_contents();
        $html=ob_get_clean();  
        include("mpdf/mpdf.php");
        $mpdf2=new mpdf('utf-8','Letter','','arialms');
        $mpdf2->addPage();
        $mpdf2->SetDisplayMode('fullpage');
        $mpdf2->writeHTML($html);       
        $domain = time();
        $download_id=$this->_random_number_generator(10);
        $file_name="download/website_analysis/website_analysis_".$domain."_".$download_id.".pdf";
        $mpdf2->output($file_name, 'F');
        $this->session->set_userdata('download_file_name',$file_name);
        echo $file_name;
    }





}