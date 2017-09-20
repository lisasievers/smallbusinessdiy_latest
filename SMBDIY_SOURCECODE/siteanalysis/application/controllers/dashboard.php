<?php

require_once('home.php'); // including home controller

/**
* class config
* @category controller
*/
class dashboard extends Home
{

    public $user_id;
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

       /* if ($this->session->userdata('logged_in')!= 1) {
            //redirect('home/login_page', 'location');
            redirect('home/login');
        }
		*/
        $this->important_feature();
        
        $this->user_id=$this->session->userdata('user_id');

        $this->member_validity();
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->admin_dashboard();
    }


    public function admin_dashboard()
    {
        $country_list = $this->get_country_names();
		//print_r($this->session->all_userdata());exit;
		//print_r($this->user_id);exit;
        if($this->session->userdata("user_type")=="Member")
        {
            $package_info=$this->session->userdata("package_info");              
            $package_name="No Package";
            if(isset($package_info["package_name"]))  $package_name=$package_info["package_name"];
            $validity="0";
            if(isset($package_info["validity"]))  $validity=$package_info["validity"];
            $price="0";
            if(isset($package_info["price"]))  $price=$package_info["price"];
            $data['package_name']=$package_name;
            $data['validity']=$validity;
            $data['price']=$price;
        }
        $data['payment_config']=$this->basic->get_data('payment_config');

        $data['body'] = "dashboard/admin_dashboard";
        $data['page_title'] = $this->lang->line('dashboard');

        $where['where'] = array('user_id'=>$this->user_id);

        $visitor_type_where['where'] = array("user_id"=>$this->user_id);
        $visitor_type_data = $this->basic->get_data('domain',$visitor_type_where,$select='',$join='',$limit=3,$start=NULL,$order_by='id desc');

        $k=0;
        foreach($visitor_type_data as $value1){
            $k++;
            $select=array("GROUP_CONCAT(is_new SEPARATOR ',') as new_vs_returning");
            $new_or_returning_where=array();

            $today = date("Y-m-d");
            $from_date = $today." 00:00:00";
            $to_date = $today." 23:59:59";

            $new_or_returning_where['where'] = array(
                "date_time >=" => $from_date,
                "date_time <=" => $to_date
                );
            $total_new_returning = $this->basic->get_data($value1['table_name'],$new_or_returning_where,$select,$join="",$limit='',$start='',$order_by='',$group_by='cookie_value,session_value');

            $new_or_returning = array();
            $new_user = 0;
            $returning_user = 0;
            foreach($total_new_returning as $value){
                $new_or_returning = explode(',', $value['new_vs_returning']);
                if(in_array(1, $new_or_returning)) $new_user++;
                else $returning_user++;
            }
            $data_number = "pie_chart_data_".$k;
            $website_name = "website_name_".$k; 
            $temp_data = array(
                0 => array(
                    "value" => $new_user,
                    "color" => "#51A39D",
                    "highlight" => "#51A39D",
                    "label" => "New Users",
                    ),
                1 => array(
                    "value" => $returning_user,
                    "color" => "#B7695C",
                    "highlight" => "#B7695C",
                    "label" => "Returning Users",
                    )
                );
            $data[$data_number] = json_encode($temp_data);
            $data[$website_name] = $value1['domain_name'];
        }


        $l=0;
        foreach($visitor_type_data as $value){
            $l++;
            $select=array("count(id) as user_number","country");
            $country_report_where=array();

            $today = date("Y-m-d");
            $from_date = $today." 00:00:00";
            $to_date = $today." 23:59:59";

            $country_report_where['where'] = array(
                "date_time >=" => $from_date,
                "date_time <=" => $to_date,
                "is_new"=>"1"
                );

            // $country_report_where['where'] = array("is_new"=>"1","date_format(date_time,'%Y-%m-%d')"=>date("Y-m-d"));
            $country_report_data = $this->basic->get_data($value['table_name'],$country_report_where,$select,$join="",$limit=5,$start='',$order_by='user_number desc',$group_by='country');

            $temp_data = array();
            $m=0;
            $color_array = array("#F9E559","#218C8D","#6CCECB","#EF7126","#8EDC9D","#473E3F");           

            $str = '<ul class="chart-legend clearfix" id="visitor_type_color_codes">';

            foreach($country_report_data as $value1){
                $temp_data[$m] = array(
                    "value" => $value1['user_number'],
                    "color" => $color_array[$m],
                    "highlight" => $color_array[$m],
                    "label" => $value1['country']
                    );
                $str .= '<li><i class="fa fa-circle-o" style="color: '.$color_array[$m].';"></i> '.array_search($value1['country'],$country_list).' : '.$value1['user_number'].'</li>';
                $m++;
            }

            $str .= '</ul>';

            $data_number = "country_chart_data_".$l;
            $country_name_data = "country_name_list_".$l;
            $data[$data_number] = json_encode($temp_data);
            $data[$country_name_data] = $str;
        }



        $where_visitor_today['where'] = array("user_id"=>$this->user_id);
        $visitor_analysis = $this->basic->get_data('domain',$where_visitor_today,$select='',$join='',$limit=7,$start=NULL,$order_by='id desc');
        $visitor_analysis_data = array();
        $bar_chart_data = array();
        $i = 0;
        foreach($visitor_analysis as $value){
            $new_user = array();

            $today = date("Y-m-d");
            $from_date = $today." 00:00:00";
            $to_date = $today." 23:59:59";

            $where_visitor_analysis['where'] = array(
                "date_time >=" => $from_date,
                "date_time <=" => $to_date,
                "is_new"=>"1"
                );

            // $where_visitor_analysis['where'] = array('is_new'=>'1',"date_format(date_time,'%Y-%m-%d')"=>date("Y-m-d"));
            $new_user = $this->basic->get_data($value['table_name'],$where_visitor_analysis);
            $visitor_analysis_data[$i]['id'] = $value['id'];
            $visitor_analysis_data[$i]['domain_name'] = $value['domain_name'];
            $visitor_analysis_data[$i]['new_user'] = count($new_user);

            $bar_chart_data[$i] = array("source_name"=>$value['domain_name'],"value"=>count($new_user));
            $i++;
        }
        $data['visitor_analysis'] = $visitor_analysis_data;
        $data['bar_chart_data'] = json_encode($bar_chart_data);


        $website_analysis = $this->basic->get_data('web_common_info',$where,$select=array('id','domain_name'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['website_analysis'] = $website_analysis;

        $search_engine_index = $this->basic->get_data('search_engine_index',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['search_engine_index'] = $search_engine_index;

        
        $search_engine_page_rank = $this->basic->get_data('search_engine_page_rank',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['search_engine_page_rank'] = $search_engine_page_rank;


        $whois_search = $this->basic->get_data('whois_search',$where,$select=array('id','domain_name','is_registered'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['whois_search'] = $whois_search;


        $alexa_info_full = $this->basic->get_data('alexa_info_full',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['alexa_info_full'] = $alexa_info_full;


        $alexa_rank = $this->basic->get_data('alexa_info_full',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['alexa_rank'] = $alexa_rank;


        $moz_info = $this->basic->get_data('moz_info',$where,$select=array('domain_authority','page_authority','url'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['moz_info'] = $moz_info;


        $dmoz_info = $this->basic->get_data('dmoz_info',$where,$select=array('domain_name','listed_or_not'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['dmoz_info'] = $dmoz_info;


        $malware_scan = $this->basic->get_data('antivirus_scan_info',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['malware_scan'] = $malware_scan;


        $similar_web_info = $this->basic->get_data('similar_web_info',$where,$select=array('id','domain_name','global_rank'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['similar_web_info'] = $similar_web_info;


        $domain_ip_check = $this->basic->get_data('ip_domain_info',$where,$select=array('ip','domain_name'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['domain_ip_check'] = $domain_ip_check;


        $ip_in_same_site = $this->basic->get_data('ip_same_site',$where,$select=array('ip'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['ip_in_same_site'] = $ip_in_same_site;


        $keyword_position = $this->basic->get_data('keyword_position',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['keyword_position'] = $keyword_position;


        $keyword_suggestion = $this->basic->get_data('keyword_suggestion',$where,$select=array('keyword'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['keyword_suggestion'] = $keyword_suggestion;


        $link_analysis = $this->basic->get_data('link_analysis',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['link_analysis'] = $link_analysis;


        $backlink_search = $this->basic->get_data('backlink_search',$where,$select='',$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['backlink_search'] = $backlink_search;


        $social_info = $this->basic->get_data('social_info',$where,$select=array('domain_name'),$join='',$limit=5,$start=NULL,$order_by='id desc');
        $data['social_info'] = $social_info;



        $this->_viewcontroller($data);
    }





}
