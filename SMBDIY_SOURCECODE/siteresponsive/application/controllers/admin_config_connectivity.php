<?php

require_once("home.php"); // including home controller


class admin_config_connectivity extends Home
{
   
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login_page', 'location');
        }

        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->user_id=$this->session->userdata('user_id');
        $this->important_feature(); 
        $this->periodic_check();
    }

  
     public function index($base_site="")
    {
        $this->connectivity_config();
    }



    public function connectivity_config()
    {
                
        $data['body'] = "admin/config/edit_connectivity_config";
        $data['config_data'] = $this->basic->get_data("connectivity_config");       
        $data['page_title'] = $this->lang->line('google API settings');
        $this->_viewcontroller($data);
    }

    

    public function edit_config()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
          
            $this->form_validation->set_rules('google_api_key',           '<b>'.$this->lang->line("google API key").'</b>',    'trim|xss_clean|required');
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->connectivity_config();
            } 

            else 
            {
                $google_api_key=addslashes(strip_tags($this->input->post('google_api_key', true)));

                $update_data=array("google_api_key"=>$google_api_key);
                $insert_data=array("google_api_key"=>$google_api_key);

                if($this->basic->is_exist("connectivity_config",$where=array("id >"=>0),$select='id')) 
                $this->basic->update_data("connectivity_config",$where=array("id >"=>0),$update_data);
                else $this->basic->insert_data("connectivity_config",$insert_data);
                  
                $this->session->set_flashdata('success_message', 1);
                redirect('admin_config_connectivity/index', 'location');
            }
        }
    }



 


}
