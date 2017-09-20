<?php

require_once("home.php"); // including home controller

/**
* class config
* @category controller
*/
class config extends Home
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

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login_page', 'location');
        }

        $this->user_id=$this->session->userdata('user_id');
        $this->important_feature();        
        $this->member_validity();
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->configuration();
    }

    /**
    * load config form method
    * @access public
    * @return void
    */
    public function configuration()
    {
                
        $data['body'] = "config/edit_config";
        $data['config_data'] = $this->basic->get_data("config",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));       
        $data['page_title'] = $this->lang->line('connectivity settings');
        $this->_viewcontroller($data);
    }

    /**
    * method to edit config
    * @access public
    * @return void
    */
    public function edit_config()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('google_safety_api',      '<b>Google API  Key</b>',               'trim|xss_clean');
            $this->form_validation->set_rules('moz_access_id',          '<b>MOZ Access ID</b>',                  'trim|xss_clean');
            $this->form_validation->set_rules('moz_secret_key',         '<b>MOZ Secret Key</b>',                 'trim|xss_clean');
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->configuration();
            } 

            else 
            {
                // assign
                $google_safety_api=addslashes(strip_tags($this->input->post('google_safety_api', true)));
                $moz_access_id=addslashes(strip_tags($this->input->post('moz_access_id', true)));
                $moz_secret_key=addslashes(strip_tags($this->input->post('moz_secret_key', true)));

                $update_data=array("google_safety_api"=>$google_safety_api,"moz_access_id"=>$moz_access_id,"moz_secret_key"=>$moz_secret_key,"mobile_ready_api_key"=>$google_safety_api);
                $insert_data=array("google_safety_api"=>$google_safety_api,"moz_access_id"=>$moz_access_id,"moz_secret_key"=>$moz_secret_key,"mobile_ready_api_key"=>$google_safety_api,"user_id"=>$this->session->userdata("user_id"));

                if($this->basic->is_exist("config",$where=array("user_id"=>$this->session->userdata("user_id")),$select='id')) 
                $this->basic->update_data("config",array("user_id"=>$this->session->userdata("user_id")),$update_data);
                else $this->basic->insert_data("config",$insert_data);
                  
                $this->session->set_flashdata('success_message', 1);
                redirect('config/configuration', 'location');
            }
        }
    }




    public function proxy()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('config_proxy');
        $crud->order_by('id');
        $crud->where('user_id', $this->session->userdata("user_id"));
        $crud->where('deleted', '0');
        $crud->set_subject($this->lang->line("proxy settings"));
     

        if($this->session->userdata("user_type")=="Member")
        {
           $crud->fields('proxy','port','username','password');
           $crud->required_fields('proxy','port');
           $crud->columns('proxy','port','username','password');
        }
        else
        {
            $crud->fields('proxy','port','username','password','admin_permission');
            $crud->required_fields('proxy','port','admin_permission');
            $crud->columns('proxy','port','username','password','admin_permission');
            $crud->callback_column('admin_permission', array($this, 'admin_permission_display_crud'));
            $crud->callback_field('admin_permission', array($this, 'admin_permission_field_crud'));
        }


        $crud->display_as('proxy', $this->lang->line('proxy'));    
        $crud->display_as('port', $this->lang->line('proxy port'));    
        $crud->display_as('username', $this->lang->line('proxy username'));    
        $crud->display_as('password', $this->lang->line('proxy password'));    
        $crud->display_as('admin_permission', $this->lang->line('admin permission'));    

        $crud->callback_after_insert(array($this, 'insert_user_id'));   
      
        
        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_export();

        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = $this->lang->line("proxy settings");
        $data['crud']=1;

        $this->_viewcontroller($data);
    }


    public function insert_user_id($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'config_proxy';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }

    public function admin_permission_field_crud($value, $row)
    {
        if ($value == '') 
        {
            $value = "everyone";
        }
        return form_dropdown('admin_permission', array('only me' => $this->lang->line('only me'), "everyone" => $this->lang->line('everyone')), $value, 'class="form-control" id="field-admin_permission"');
    }


    public function admin_permission_display_crud($value, $row)
    {
        if ($value == "only me") 
        {
            return "<span class='label label-success'>".$this->lang->line('only me')."</sapn>";
        } 
        else 
        {
            return "<span class='label label-warning'>".$this->lang->line('everyone')."</sapn>";
        }
    }


}
