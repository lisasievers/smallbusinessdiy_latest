<?php

require_once("home.php"); // including home controller

/**
* class config
* @category controller
*/
class admin_config_lead extends Home
{
    /**
    * load constructor method
    * @access public
    * @return void
    */
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

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index($base_site="")
    {
        $this->lead_config();
    }

    /**
    * load config form method
    * @access public
    * @return void
    */
    public function lead_config()
    {                
        $data['body'] = "admin/config/edit_lead_config";
        $data['config_data'] = $this->basic->get_data("lead_config");       
        $data['page_title'] = $this->lang->line('lead settings');  
        $this->_viewcontroller($data);
    }

    /**
    * method to edit config
    * @access public
    * @return void
    */
    public function edit_lead_config()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('mailchimp_api_key',          '<b>'.$this->lang->line("mailchimp API key").'</b>',   		                                 'trim|xss_clean');
            $this->form_validation->set_rules('mailchimp_list_id',          '<b>'.$this->lang->line("mailchimp list ID").'</b>',                                         'trim|xss_clean');;
            $this->form_validation->set_rules('allowed_download_per_email', '<b>'.$this->lang->line("how many times a guest user can dowload using same email").'</b>',  'trim|xss_clean|integer');;
            $this->form_validation->set_rules('unlimited_download_emails',  '<b>'.$this->lang->line("email addresses that can download report unlimited times").'</b>',  'trim|xss_clean');;
            $this->form_validation->set_rules('status',                     '<b>'.$this->lang->line("status").'</b>',                                                    'trim|required|xss_clean');;
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->lead_config();
            } 
            else 
            {
                // assign
                $mailchimp_api_key=addslashes(strip_tags($this->input->post('mailchimp_api_key', true)));
                $mailchimp_list_id=addslashes(strip_tags($this->input->post('mailchimp_list_id', true)));
                $allowed_download_per_email=addslashes(strip_tags($this->input->post('allowed_download_per_email', true)));
                $unlimited_download_emails=addslashes(strip_tags($this->input->post('unlimited_download_emails', true)));
                $status=addslashes(strip_tags($this->input->post('status', true)));
               
                if($allowed_download_per_email=="") $allowed_download_per_email=10;

                if($status=="1")
                $insert_update_data=array("mailchimp_api_key"=>$mailchimp_api_key,"mailchimp_list_id"=>$mailchimp_list_id,"allowed_download_per_email"=>$allowed_download_per_email,"unlimited_download_emails"=>$unlimited_download_emails,"status"=>$status);
                else
                $insert_update_data=array("status"=>$status);

                if($this->basic->is_exist("lead_config",$where='',$select='id')) 
                $this->basic->update_data("lead_config",$where='',$insert_update_data);
                else $this->basic->insert_data("lead_config",$insert_update_data);
                  
                $this->session->set_flashdata('success_message', 1);
                redirect('admin_config_lead/lead_config', 'location');
            }
        }
    }

 


}
