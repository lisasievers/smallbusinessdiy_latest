<?php

require_once("home.php"); // loading home controller

class server_info extends Home
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
    	$this->server_info();
    }

    public function server_info()
    {
        $data['body'] = 'admin/server/info';
        $data['page_title'] = $this->lang->line("Server Information");
        $this->_viewcontroller($data);
    }   

    public function server_info_action()
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
                    $server = '';
                    $connection = '';
                    $response = $this->web_common_report->get_header_response($url_value);
                    $response = explode(PHP_EOL, $response);
                    foreach($response as $single_response) :
                        $semicolon_position = strpos($single_response, ':');
                        if($semicolon_position !== FALSE) :
                            $title = substr($single_response, 0, $semicolon_position);
                            $value = str_replace($title . ': ','',$single_response);
                            if($title == 'Server') :
                                $server = $value;
                            endif;  
                            if($title == 'Connection') :
                                $connection = $value;
                            endif;  
                        endif;    
                    endforeach;    
                    $url_lists[] = array('url' => $url_value, 'server' => $server, 'connection' => $connection);
                endif;
            endforeach;
        endif;    

        header('Content-Type: application/json');
        echo json_encode(array('url_lists' => $url_lists));        
    } 

}    