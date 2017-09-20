    <?php

require_once("home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class domain_details_visitor extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(1,$this->module_access))
        redirect('home/login_page', 'location'); 
         
        $query = 'SET SESSION group_concat_max_len = 9999999999999999999';
        $this->db->query($query);

        if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
        }
    }


    public function index(){

        $this->domain_list_visitor();      
    }
    

    public function domain_list_visitor()
    {
        $data['body'] = 'domain_visitor/domain_list';
        $data['page_title'] = $this->lang->line("visitor analysis");
		//print_r($data);exit;
        $this->load->helper('cookie');
        $userid = get_cookie('user_id');
        $username = get_cookie('emailid');
		$data['sites']=$this->basic->get_user_sites($userid);
        //print_r($data['sites']);exit;
        $this->_viewcontroller($data);
    }

    public function domain_list_visitor_data()
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
            $this->session->set_userdata('domain_list_for_visitor_domain_name', $domain_name);
        }

        $search_domain_name = $this->session->userdata('domain_list_for_visitor_domain_name');

        $where_simple=array();

        if ($search_domain_name) {
            $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        }
        
        $where_simple['user_id'] = $this->user_id;
        
        $where  = array('where'=>$where_simple);

        $order_by_str=$sort." ".$order;

        $offset = ($page-1)*$rows;
        $result = array();

        $table = "domain";

        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id");      

        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function add_domain()
    {
        $data['body'] = 'domain_visitor/add_domain';
        $data['page_title'] = $this->lang->line("add website");
		$this->load->helper('cookie');
        $userid = get_cookie('user_id');
        $username = get_cookie('emailid');
		$data['sites']=$this->basic->get_user_sites($userid);
        $this->_viewcontroller($data);
    }

    public function add_domain_action()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        $domain_name = strtolower($this->input->post('domain_name', true));

        $where['where'] = array("user_id" => $this->user_id);
        $domain_exist = $this->basic->get_data('domain',$where,$select='');
        $given_only_domain = get_domain_only($domain_name);
        foreach($domain_exist as $value){
            $already_only_domain = get_domain_only($value['domain_name']);
            if($already_only_domain == $given_only_domain){
                echo "domain_already_exist"; 
                exit();
            }
        }


        //************************************************//
        $module_id = 1;
        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();
        $package_info=$this->session->userdata("package_info");

        if(isset($package_info["monthly_limit"])) $monthly_limit=json_decode($package_info["monthly_limit"],true);
        if(isset($package_info["module_ids"])) $module_ids=explode(',', $package_info["module_ids"]);

        if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id] < count($domain_exist)){
            echo 3;
            exit();
        }
        //************************************************//

        $this->db->trans_start(); 

        $random_num = $this->_random_number_generator();
        $js_code = '<script id="domain" data-name="'.$random_num.'" type="text/javascript" src="'.site_url().'js/analytics_js/client.js"></script>';
        $js_code=htmlspecialchars($js_code);
        
        $table_name = str_replace('http:', '', str_replace('https:', '', str_replace('www', '', str_replace('.', '_', str_replace('/', '', $domain_name)))));
        $table_name .= "_".$this->user_id;
        $site_orgin = $this->basic->get_site_data($this->user_id,$domain_name);
        $data = array(
            'user_id' => $this->user_id,
            'site_id' =>  $site_orgin,
            'domain_name' => $domain_name,
            'domain_code' => $random_num,
            'js_code' => $js_code,
            'table_name' => $table_name,
            'add_date' => date("Y-m-d")
            );        

        $this->basic->insert_data('domain',$data);
        $last_id = $this->db->insert_id();

        $where_update = array('id' => $last_id);
        $update_code = $last_id.$random_num;
        $js_code = '<script id="domain" data-name="'.$update_code.'" type="text/javascript" src="'.site_url().'js/analytics_js/client.js"></script>';
        $js_code=htmlspecialchars($js_code);
        $data_update = array('domain_code'=>$update_code,'js_code'=>$js_code);
        $this->basic->update_data('domain',$where_update,$data_update);

        $sql = "DROP TABLE IF EXISTS `".$table_name."`;";
        $sql2 = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `domain_id` int(11) NOT NULL,
              `domain_code` varchar(50) NOT NULL,
              `ip` varchar(20) NOT NULL,
              `country` varchar(250) NOT NULL,
              `city` varchar(250) NOT NULL,
              `org` varchar(250) NOT NULL,
              `latitude` varchar(250) NOT NULL,
              `longitude` varchar(250) NOT NULL,
              `postal` varchar(250) NOT NULL,
              `os` varchar(250) NOT NULL,
              `device` varchar(250) NOT NULL,
              `browser_name` varchar(200) NOT NULL,
              `browser_version` varchar(200) NOT NULL,
              `date_time` datetime NOT NULL,
              `referrer` varchar(200) CHARACTER SET utf8 NOT NULL,
              `visit_url` text CHARACTER SET utf8 NOT NULL,
              `cookie_value` varchar(200) NOT NULL,
              `session_value` varchar(200) NOT NULL,
              `is_new` int(11) NOT NULL,
              `last_scroll_time` datetime NOT NULL,
              `last_engagement_time` datetime NOT NULL,
              `browser_rawdata` varchar(250) NOT NULL,
              PRIMARY KEY (`id`),
              INDEX (  `date_time` , `country` , `is_new` , `browser_name` , `device` , `os` )
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
            ";

        $this->basic->execute_complex_query($sql);
        $this->basic->execute_complex_query($sql2);

        $where_domain['where'] = array('id'=>$last_id);
        $domain_info = $this->basic->get_data('domain',$where_domain,$select='');

        // insert data to useges log table
        // $this->_insert_usage_log($module_id=1,$request=count($domain_exist));

        $this->db->trans_complete();

        if($this->db->trans_status() === false){
            echo 0;
        } else {
            echo htmlspecialchars_decode($domain_info[0]['js_code']);
        }
    }
    /*************************************************************/    
    
    public function domain_details($id=0)
    {
        $data['id'] = $id;

        $data['body'] = 'domain_visitor/domain_details';
        $data['page_title'] = $this->lang->line("visitor analysis report");;
        $this->_viewcontroller($data);
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
            "date_format(date_time,'%Y-%m-%d') as date",
            "count(id) as number_of_user"
            );
        $day_wise_visitor = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");

        $day_count = date('Y-m-d', strtotime($from_date. " + 1 days"));


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

        $select=array("date_format(date_time,'%Y-%m-%d') as date_test","session_value","GROUP_CONCAT(referrer SEPARATOR ',') as referrer","GROUP_CONCAT(visit_url SEPARATOR ',') as visit_url_str");

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

        $select=array("date_format(date_time,'%Y-%m-%d') as date","GROUP_CONCAT(is_new SEPARATOR ',') as new_vs_returning");
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


            $s_country = array_search(trim($value["country"]), $country_list); 
            $image_link = base_url()."assets/images/flags/".$s_country.".png";

            $image = '<img style="height: 15px; width: 20px; margin-top: -3px;" src="'.$image_link.'" alt=" "> &nbsp;';
            if($value['country'] == '' || !isset($value['country'])){
                $image = '';
                $value['country'] = "Unknown";
            }

            $country_report_str .= "<tr class='country_wise_name' style='cursor:pointer;' data='".$value['country']."'><td>".$i."</td><td>".$image.$value['country']."</td><td>".$sessions."</td><td>".$new_users."</td></tr>";

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
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_format(date_time,'%Y-%m-%d') as date");
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
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_format(date_time,'%Y-%m-%d') as date");
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
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_format(date_time,'%Y-%m-%d') as date");
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
        $select = array("GROUP_CONCAT(session_value SEPARATOR ',') as sessions","date_format(date_time,'%Y-%m-%d') as date");
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

        $where_delete_table['where'] = array('id'=>$id);
        $table_name = $this->basic->get_data('domain',$where_delete_table,$select='');
        $this->basic->delete_data('domain',$where=array('id'=>$id));
        $sql = "DROP TABLE `".$table_name[0]['table_name']."`";
        $this->basic->execute_complex_query($sql);

        $this->db->trans_complete();
        if($this->db->trans_status() === false) {
            $this->session->set_userdata('delete_error',1);
            redirect('domain_details_visitor/domain_list_visitor','Location');
        } else {
            $this->session->set_userdata('delete_success',1);
            redirect('domain_details_visitor/domain_list_visitor','Location');
        }
    }


    public function delete_previous_data($id=0)
    {
        $where_delete_table['where'] = array('id'=>$id);
        $table_name = $this->basic->get_data('domain',$where_delete_table,$select='');
        $table_name = $table_name[0]['table_name'];
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));
        $from_date = $from_date." 23:59:59";
        $where = array(
            'date_time <' => $from_date
            );
        if($this->basic->delete_data($table_name,$where)){
            $this->session->set_userdata('delete_success',1);
            redirect('domain_details_visitor/domain_list_visitor','Location');
        } else {
            $this->session->set_userdata('delete_error',1);
            redirect('domain_details_visitor/domain_list_visitor','Location');
        }
    }

    public function js_code_generator()
    {
        $random_num = $this->_random_number_generator();
        $str = '<script id="domain" data-name="'.$random_num.'" type="text/javascript" src="analytics_js/client.js"></script>';
        echo $str;
    }


}