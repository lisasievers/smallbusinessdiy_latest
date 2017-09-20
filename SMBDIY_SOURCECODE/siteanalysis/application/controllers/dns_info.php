<?php

require_once("home.php"); // loading home controller

class dns_info extends Home
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

        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        redirect('home/login_page', 'location'); 
    }



    public function index()
    {
    	$this->dns_info();
    }

    public function dns_info()
    {
        $data['body'] = 'admin/dns/info';
        $data['page_title'] = $this->lang->line("DNS Information");
        $this->_viewcontroller($data);
    }   

    public function dns_info_action()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET') :
            redirect('home/access_forbidden', 'location');     
        endif;       

        $this->load->library('web_common_report');
        $url_lists = array();
        $url_values = explode(',',$this->input->post('urlvalues'));

        if(count($url_values) <= 50) :
            foreach($url_values as $url_value) :
                $url_value = trim($url_value);
                if(is_valid_url($url_value) === TRUE || is_valid_domain_name($url_value) === TRUE) :
                    $check_data = $this->web_common_report->dns_information($url_value);
                    $first_element = $check_data[0];
                    $url_lists[] = array('url' => $url_value, 'host' => $first_element['host'], 'type' => $first_element['type'], 'ip' => $first_element['ip'], 'class' => $first_element['class'], 'ttl' => $first_element['ttl'], 'fullinfo' => $check_data);
                endif;
            endforeach;
        endif;    

        header('Content-Type: application/json');
        echo json_encode(array('url_lists' => $url_lists));        
    } 

}    