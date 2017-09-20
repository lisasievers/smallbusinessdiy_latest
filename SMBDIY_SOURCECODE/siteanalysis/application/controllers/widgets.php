<?php

require_once("home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class widgets extends Home
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
        
        $this->load->helper('form');
        $this->load->library('upload');
        $this->load->library('Web_common_report');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);
        
    }


    public function index(){
        $this->get_widget();      
    }


    public function get_widget()
    {
        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        $this->member_validity(); 

        $user_id = $this->user_id;
        $where['where'] = array('user_id'=>$user_id);
        $domain_name = $this->basic->get_data('domain',$where,$select=array('domain_name'));
        $data['domain_name_array'] = $domain_name;
        $data['body'] = 'widgets/widget_landing_page';
        $data['page_title'] = $this->lang->line("native widget");
        $this->_viewcontroller($data);
    }

    public function get_table_name_for_domain()
    {

        $this->member_validity();

        $domain_name = $this->input->post('domain_name');
        $table = str_replace('http:', '', str_replace('https:', '', str_replace('www', '', str_replace('.', '_', str_replace('/', '', $domain_name)))));
        $table .= "_".$this->user_id;
        if($this->db->table_exists($table))
            echo '<iframe src="'.base_url().'widgets/public_content_overview_data/'.$table.'" frameborder="0" width="550" height="420" style=""></iframe>';
        else
            echo "no";
    }

    public function public_content_overview_data($table='')
    {        
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

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

        $data['total_view'] = $total_view;
        $data['content_overview_data'] = $content_overview_data;
        $data['from_date'] = date("d-M-y",strtotime($from_date));
        $data['to_date'] = date("d-M-y",strtotime($to_date));
        $this->load->view("widgets/widget_for_content_overview", $data);

    }

    public function get_table_name_for_domain_traffic()
    {
        $this->member_validity();

        $domain_name = $this->input->post('domain_name');
        $table = str_replace('http:', '', str_replace('https:', '', str_replace('www', '', str_replace('.', '_', str_replace('/', '', $domain_name)))));
        $table .= "_".$this->user_id;
        if($this->db->table_exists($table))
            echo '<iframe src="'.base_url().'widgets/public_traffic_source_data/'.$table.'" frameborder="0" width="700" height="300" style=""></iframe>';
        else
            echo "no";
    }

    public function public_traffic_source_data($table='')
    {
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";

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
		if($bounce_no_bounce == 0) $bounce_rate = 0;
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

        $data['total_page_view'] = number_format(count($total_page_view));
        $data['total_unique_visitro'] = number_format(count($total_unique_visitor));
        if(count($total_unique_visitor) == 0)
            $data['average_visit'] = number_format(count($total_page_view));
        else
            $data['average_visit'] = number_format(count($total_page_view)/count($total_unique_visitor), 2);

        $data['average_stay_time'] = $hours.":".$minutes.":".$seconds;
        $data['bounce_rate'] = $bounce_rate;
        $this->load->view("widgets/widget_for_overview", $data);
    }
	
	public function get_table_name_for_country_report()
	{
        $this->member_validity();

		$domain_name = $this->input->post('domain_name');
        $table = str_replace('http:', '', str_replace('https:', '', str_replace('www', '', str_replace('.', '_', str_replace('/', '', $domain_name)))));
        $table .= "_".$this->user_id;
        if($this->db->table_exists($table))
            echo '<iframe src="'.base_url().'widgets/public_country_report_data/'.$table.'" frameborder="0" width="650" height="450" style=""></iframe>';
        else
            echo "no";
	}

	
	public function public_country_report_data($table='')
	{
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));        

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";
        
        $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date
            );
        $select = array('country',"GROUP_CONCAT(is_new SEPARATOR ',') as new_user");
        $country_name = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='country');

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
        $data['country_graph_data'] = json_encode($country_report);
		$this->load->view("widgets/widget_for_country_report", $data);
		
	}


}