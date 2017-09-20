<?php

require_once("home.php");

class Admin_leads extends Home
{

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login', 'location');
        }

        $this->important_feature();
        $this->periodic_check();
    }

    public function index($base_site="")
    {
        $this->lead_list();
    }

    public function lead_list()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('leads');
        $crud->order_by('date_time','DESC');
        $crud->set_subject($this->lang->line("lead list"));  

        $state = $crud->getState();
        $state_info = $crud->getStateInfo();
         
        if($state == 'export')
        $crud->columns('name','email', 'date_time','no_of_search','domain');
        else $crud->columns('name','email', 'date_time','no_of_search');
        
        $crud->unset_add();
        $crud->unset_print();
        $crud->unset_delete();
        $crud->unset_edit();

        $crud->display_as('domain', $this->lang->line('website'));
        $crud->display_as('name', $this->lang->line('name'));
        $crud->display_as('email', $this->lang->line('email'));
        $crud->display_as('date_time', $this->lang->line('last updated at'));
        $crud->display_as('no_of_search', $this->lang->line('downloaded as guest : how many times?'));

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("lead list");
        $this->_viewcontroller($data);
    }
    
}
