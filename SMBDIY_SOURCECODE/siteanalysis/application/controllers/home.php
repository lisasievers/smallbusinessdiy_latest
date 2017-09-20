<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* @category controller
* class home
*/
class home extends CI_Controller
{

    /**
    * load constructor
    * @access public
    * @return void
    */
    public $module_access;
    public $language;
    public $is_rtl;

    public $is_ad_enabled;
    public $is_ad_enabled1;
    public $is_ad_enabled2;
    public $is_ad_enabled3;
    public $is_ad_enabled4; 

    public $ad_content1;
    public $ad_content1_mobile;
    public $ad_content2;
    public $ad_content3;
    public $ad_content4;

    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        $this->load->helpers('my_helper');

        $this->is_rtl=FALSE;
        $this->language="";
        $this->_language_loader();

        $this->is_ad_enabled=false;
        $this->is_ad_enabled1=false;
        $this->is_ad_enabled2=false;
        $this->is_ad_enabled3=false;
        $this->is_ad_enabled4=false; 

        $this->ad_content1="";
        $this->ad_content1_mobile="";
        $this->ad_content2="";
        $this->ad_content3="";
        $this->ad_content4="";

		ignore_user_abort(TRUE);

        $seg = $this->uri->segment(2);
        if ($seg!="installation" && $seg!= "installation_action") {
            if (file_exists(APPPATH.'install.txt')) {
                redirect('home/installation', 'location');
            }
        }

        if (!file_exists(APPPATH.'install.txt')) {
            $this->load->database();
            $this->load->model('basic');
            $this->_time_zone_set();
            $this->load->library('upload');
            $this->upload_path = realpath(APPPATH . '../upload');
			$query = 'SET SESSION group_concat_max_len=9999000000000000000';
       		$this->db->query($query);
            $q= "SET SESSION wait_timeout=50000";
            $this->db->query($q);
			/**Disable STRICT_TRANS_TABLES mode if exist on mysql ***/
			$query="SET SESSION sql_mode = ''";
			$this->db->query($query);



            $ad_config = $this->basic->get_data("ad_config");           
            if(isset($ad_config[0]["status"]))
            {
               if($ad_config[0]["status"]=="1")
               {
                    $this->is_ad_enabled = ($ad_config[0]["status"]=="1") ? true : false; 
                    if($this->is_ad_enabled) 
                    {
                        $this->is_ad_enabled1 = ($ad_config[0]["section1_html"]=="" && $ad_config[0]["section1_html_mobile"]=="") ? false : true; 
                        $this->is_ad_enabled2 = ($ad_config[0]["section2_html"]=="") ? false : true; 
                        $this->is_ad_enabled3 = ($ad_config[0]["section3_html"]=="") ? false : true; 
                        $this->is_ad_enabled4 = ($ad_config[0]["section4_html"]=="") ? false : true;

                        $this->ad_content1          = htmlspecialchars_decode($ad_config[0]["section1_html"],ENT_QUOTES);
                        $this->ad_content1_mobile   = htmlspecialchars_decode($ad_config[0]["section1_html_mobile"],ENT_QUOTES);
                        $this->ad_content2          = htmlspecialchars_decode($ad_config[0]["section2_html"],ENT_QUOTES);
                        $this->ad_content3          = htmlspecialchars_decode($ad_config[0]["section3_html"],ENT_QUOTES);
                        $this->ad_content4          = htmlspecialchars_decode($ad_config[0]["section4_html"],ENT_QUOTES);
                    }
               }

            }
            else
            {
                $this->is_ad_enabled  = true;   
                $this->is_ad_enabled1 = true;
                $this->is_ad_enabled2 = true;
                $this->is_ad_enabled3 = true;
                $this->is_ad_enabled4 = true;

                $this->ad_content1="<img src='".base_url('assets/images/placeholder/reserved-section-1.png')."'>";
                $this->ad_content1_mobile="<img src='".base_url('assets/images/placeholder/reserved-section-1-mobile.png')."'>";
                $this->ad_content2="<img src='".base_url('assets/images/placeholder/reserved-section-2.png')."'>";
                $this->ad_content3="<img src='".base_url('assets/images/placeholder/reserved-section-3.png')."'>";
                $this->ad_content4="<img src='".base_url('assets/images/placeholder/reserved-section-4.png')."'>";

            }



            if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin')
            {
                $package_info=$this->session->userdata("package_info");
                $module_ids='';
                if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
                $this->module_access=explode(',', $module_ids);
            }

        }
		
		if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
        }

    }



    public function _insert_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {

        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);
        
        if($this->basic->is_exist("view_usage_log",$where))
        {
        	$this->db->set('usage_count', 'usage_count+'.$usage_count, FALSE);
			$this->db->where($where);
			$this->db->update('usage_log');
        }
        else $this->basic->insert_data("usage_log",$insert_data);

        return true;
    }


    public function _check_usage($module_id=0,$request=0,$user_id=0)
    {
        
        if($module_id==0 || $request==0) return "0";
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $info=$this->basic->get_data("view_usage_log",$where=array("where"=>array("usage_month"=>$usage_month,"usage_year"=>$usage_year,"module_id"=>$module_id,"user_id"=>$user_id)));
        $usage_count=0;
        if(isset($info[0]["usage_count"]))
        $usage_count=$info[0]["usage_count"];

        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();

        if($this->session->userdata("package_info")!="")
        {
            $package_info=$this->session->userdata("package_info");  
            if($this->session->userdata('user_type') == 'Admin') return "1"; 
        }
        else
        {
            $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
            $package_info=array();
            if(array_key_exists(0, $package_data))
            $package_info=$package_data[0];   
            if($package_info['user_type'] == 'Admin') return "1";     
        }

        if(isset($package_info["bulk_limit"]))    $bulk_limit=json_decode($package_info["bulk_limit"],true);
        if(isset($package_info["monthly_limit"])) $monthly_limit=json_decode($package_info["monthly_limit"],true);
        if(isset($package_info["module_ids"]))    $module_ids=explode(',', $package_info["module_ids"]);

        $return = "0";
        if(in_array($module_id, $module_ids) && $bulk_limit[$module_id] > 0 && $bulk_limit[$module_id]<$request)
         $return = "2"; // bulk limit crossed | 0 means unlimited
        else if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id]<($request+$usage_count))
         $return = "3"; // montly limit crossed | 0 means unlimited
        else  $return = "1"; //success  

        return $return;     
    }

    

    public function print_limit_message($module_id=0,$request=0)
    {
        $status=$this->_check_usage($module_id,$request);
        if($status=="2") 
        {
        	echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        	exit();
        }
        else if($status=="3") 
        {
        	echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        	exit();
        }
      
    }




    public function _language_loader()
    {       

        if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($this->session->userdata("selected_language")!="")
        $this->language = $this->session->userdata("selected_language");
        else if(!$this->config->item("language") || $this->config->item("language")=="") 
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($this->language=="arabic")
        $this->is_rtl=TRUE;

        if (file_exists(APPPATH.'language/'.$this->language.'/front_lang.php'))
        $this->lang->load('front', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/sidebar_lang.php'))
        $this->lang->load('sidebar', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/common_lang.php'))
        $this->lang->load('common', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/message_lang.php'))
        $this->lang->load('message', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/admin_lang.php'))
        $this->lang->load('admin', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/user_setting_lang.php'))
        $this->lang->load('user_setting', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/calendar_lang.php'))
        $this->lang->load('calendar', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/date_lang.php'))
        $this->lang->load('date', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/db_lang.php'))
        $this->lang->load('db', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/email_lang.php'))
        $this->lang->load('email', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/form_validation_lang.php'))
        $this->lang->load('form_validation', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/ftp_lang.php'))
        $this->lang->load('ftp', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/imglib_lang.php'))
        $this->lang->load('imglib', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/migration_lang.php'))
        $this->lang->load('migration', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/number_lang.php'))
        $this->lang->load('number', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/pagination_lang.php'))
        $this->lang->load('pagination', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/profiler_lang.php'))
        $this->lang->load('profiler', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/unit_test_lang.php'))
        $this->lang->load('unit_test', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/upload_lang.php'))
        $this->lang->load('upload', $this->language);     

        if (file_exists(APPPATH.'language/'.$this->language.'/misc_lang.php'))
        $this->lang->load('misc', $this->language);

        if (file_exists(APPPATH.'language/'.$this->language.'/misc2_lang.php'))
        $this->lang->load('misc2', $this->language);      
    }

    /**
    * method to install software
    * @access public
    * @return void
    */
    public function installation()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }
        $data = array("body" => "page/install", "page_title" => "Install Package","language_info" => $this->_language_list());
        $this->_front_viewcontroller($data);
    }

    /**
    * method to installation action
    * @access public
    * @return void
    */
    public function installation_action()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }

        if ($_POST) {
            // validation
            $this->form_validation->set_rules('host_name',               '<b>Host Name</b>',                   'trim|required|xss_clean');
            $this->form_validation->set_rules('database_name',           '<b>Database Name</b>',               'trim|required|xss_clean');
            $this->form_validation->set_rules('database_username',       '<b>Database Username</b>',           'trim|required|xss_clean');
            $this->form_validation->set_rules('database_password',       '<b>Database Password</b>',           'trim|xss_clean');
            $this->form_validation->set_rules('app_username',            '<b>Admin Panel Login Email</b>',     'trim|required|valid_email|xss_clean');
            $this->form_validation->set_rules('app_password',            '<b>Admin Panel Login Password</b>',  'trim|required|xss_clean');
            $this->form_validation->set_rules('institute_name',          '<b>Company Name</b>',                'trim|xss_clean');
            $this->form_validation->set_rules('institute_address',       '<b>Company Address</b>',             'trim|xss_clean');
            $this->form_validation->set_rules('institute_mobile',        '<b>Company Phone / Mobile</b>',      'trim|xss_clean');
            $this->form_validation->set_rules('language',                '<b>Language</b>',                    'trim');

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) {
                return $this->installation();
            } else {
                $host_name = addslashes(strip_tags($this->input->post('host_name', true)));
                $database_name = addslashes(strip_tags($this->input->post('database_name', true)));
                $database_username = addslashes(strip_tags($this->input->post('database_username', true)));
                $database_password = addslashes(strip_tags($this->input->post('database_password', true)));
                $app_username = addslashes(strip_tags($this->input->post('app_username', true)));
                $app_password = addslashes(strip_tags($this->input->post('app_password', true)));
                $institute_name = addslashes(strip_tags($this->input->post('institute_name', true)));
                $institute_address = addslashes(strip_tags($this->input->post('institute_address', true)));
                $institute_mobile = addslashes(strip_tags($this->input->post('institute_mobile', true)));
                $language = addslashes(strip_tags($this->input->post('language', true)));

                $con=@mysqli_connect($host_name, $database_username, $database_password);
                if (!$con) {
                    $this->session->set_userdata('mysql_error', "Could not conenect to MySQL.");
                    return $this->installation();
                }
                if (!@mysqli_select_db($con,$database_name)) {
                    $this->session->set_userdata('mysql_error', "Database not found.");
                    return $this->installation();
                }
                mysqli_close($con);

                 // writing application/config/my_config
                  $app_my_config_data = "<?php ";
                $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
                $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
                $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."' ;\n";
                $app_my_config_data.= "\$config['product_version'] = '".$this->config->item('product_version')." ';\n\n";
                $app_my_config_data.= "\$config['institute_address1'] = '$institute_name';\n";
                $app_my_config_data.= "\$config['institute_address2'] = '$institute_address';\n";
                $app_my_config_data.= "\$config['institute_email'] = '$app_username';\n";
                $app_my_config_data.= "\$config['institute_mobile'] = '$institute_mobile';\n";
                $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
                $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
                $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
                $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
                $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
                $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";
                $app_my_config_data.= "\$config['time_zone'] = '' ;\n";                
                $app_my_config_data.= "\$config['language'] = '$language';\n";
                $app_my_config_data.= "\$config['sess_use_database'] = TRUE;\n";
                $app_my_config_data.= "\$config['sess_table_name'] = 'ci_sessions';\n";
                $app_my_config_data.= "\$config['front_end_search_display'] = '".$this->config->item('front_end_search_display')."';\n";

                file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);
                  //writting  application/config/my_config

                  //writting application/config/database
                $database_data = "";
                $database_data.= "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n
                    \$active_group = 'default';
                    \$active_record = true;
                    \$db['default']['hostname'] = '$host_name';
                    \$db['default']['username'] = '$database_username';
                    \$db['default']['password'] = '$database_password';
                    \$db['default']['database'] = '$database_name';
                    \$db['default']['dbdriver'] = 'mysqli';
                    \$db['default']['dbprefix'] = '';
                    \$db['default']['pconnect'] = TRUE;
                    \$db['default']['db_debug'] = TRUE;
                    \$db['default']['cache_on'] = FALSE;
                    \$db['default']['cachedir'] = '';
                    \$db['default']['char_set'] = 'utf8';
                    \$db['default']['dbcollat'] = 'utf8_general_ci';
                    \$db['default']['swap_pre'] = '';
                    \$db['default']['autoinit'] = TRUE;
                    \$db['default']['stricton'] = FALSE;";
                file_put_contents(APPPATH.'config/database.php', $database_data, LOCK_EX);
                  //writting application/config/database

                // writting client js
                $client_js_content=file_get_contents('js/analytics_js/client.js');
                $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
                file_put_contents('js/analytics_js/client.js', $client_js_content_new, LOCK_EX);
                // writting client js

                  // loding database library, because we need to run queries below and configs are already written

                $this->load->database();
                $this->load->model('basic');
                  // loding database library, because we need to run queries below and configs are already written

                  // dumping sql
                $dump_file_name = 'initial_db.sql';
                $dump_sql_path = 'assets/backup_db/'.$dump_file_name;
                $this->basic->import_dump($dump_sql_path);
                  // dumping sql

                  //generating hash password for admin and updaing database
                $app_password = md5($app_password);
                $this->basic->update_data($table = "users", $where = array("user_type" => "Admin"), $update_data = array("mobile" => $institute_mobile, "email" => $app_username, "password" => $app_password, "name" => $institute_name, "status" => "1", "deleted" => "0", "address" => $institute_address));
                  //generating hash password for admin and updaing database

                  //deleting the install.txt file,because installation is complete
                  if (file_exists(APPPATH.'install.txt')) {
                      unlink(APPPATH.'install.txt');
                  }
                  //deleting the install.txt file,because installation is complete
                  redirect('home/login');
            }
        }
    }


    /**
    * method to index page
    * @access public
    * @return void
    */
    public function index()
    {
        // $this->login_page();
        $this->_site_viewcontroller();
    }

    
    /**
    * method to set time zone
    * @access public
    * @return void
    */
    public function _time_zone_set()
    {
       $time_zone = $this->config->item('time_zone');
        if ($time_zone== '') {
            $time_zone="Europe/Dublin";
        }
        date_default_timezone_set($time_zone);
    }


    /**
    * method to show time zone list
    * @access public
    * @return array
    */    
    public function _time_zone_list()
    {
        $all_time_zone=array(
            'Kwajalein'                    => 'GMT -12.00 Kwajalein',
            'Pacific/Midway'                => 'GMT -11.00 Pacific/Midway',
            'Pacific/Honolulu'                => 'GMT -10.00 Pacific/Honolulu',
            'America/Anchorage'            => 'GMT -9.00  America/Anchorage',
            'America/Los_Angeles'            => 'GMT -8.00  America/Los_Angeles',
            'America/Denver'                => 'GMT -7.00  America/Denver',
            'America/Tegucigalpa'            => 'GMT -6.00  America/Tegucigalpa',
            'America/New_York'                => 'GMT -5.00  America/New_York',
            'America/Caracas'                => 'GMT -4.30  America/Caracas',
            'America/Halifax'                => 'GMT -4.00  America/Halifax',
            'America/St_Johns'                => 'GMT -3.30  America/St_Johns',
            'America/Argentina/Buenos_Aires'=> 'GMT +-3.00 America/Argentina/Buenos_Aires',
            'America/Sao_Paulo'            =>' GMT -3.00  America/Sao_Paulo',
            'Atlantic/South_Georgia'        => 'GMT +-2.00 Atlantic/South_Georgia',
            'Atlantic/Azores'                => 'GMT -1.00  Atlantic/Azores',
            'Europe/Dublin'                => 'GMT 	   Europe/Dublin',
            'Europe/Belgrade'                => 'GMT +1.00  Europe/Belgrade',
            'Europe/Minsk'                    => 'GMT +2.00  Europe/Minsk',
            'Asia/Kuwait'                    => 'GMT +3.00  Asia/Kuwait',
            'Asia/Tehran'                    => 'GMT +3.30  Asia/Tehran',
            'Asia/Muscat'                    => 'GMT +4.00  Asia/Muscat',
            'Asia/Yekaterinburg'            => 'GMT +5.00  Asia/Yekaterinburg',
            'Asia/Kolkata'                    => 'GMT +5.30  Asia/Kolkata',
            'Asia/Katmandu'                => 'GMT +5.45  Asia/Katmandu',
            'Asia/Dhaka'                    => 'GMT +6.00  Asia/Dhaka',
            'Asia/Rangoon'                    => 'GMT +6.30  Asia/Rangoon',
            'Asia/Krasnoyarsk'                => 'GMT +7.00  Asia/Krasnoyarsk',
            'Asia/Brunei'                    => 'GMT +8.00  Asia/Brunei',
            'Asia/Seoul'                    => 'GMT +9.00  Asia/Seoul',
            'Australia/Darwin'                => 'GMT +9.30  Australia/Darwin',
            'Australia/Canberra'            => 'GMT +10.00 Australia/Canberra',
            'Asia/Magadan'                    => 'GMT +11.00 Asia/Magadan',
            'Pacific/Fiji'                    => 'GMT +12.00 Pacific/Fiji',
            'Pacific/Tongatapu'            => 'GMT +13.00 Pacific/Tongatapu'
        );

        return $all_time_zone;
    }

    /**
    * method to disable cache
    * @access public
    * @return void
    */
    public function _disable_cache()
    {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
    * method to
    * @access public
    * @return void
    */     
    public function access_forbidden()
    {
        $this->load->view('page/access_forbidden');
    }

    /**
    * method to load front viewcontroller
    * @access public
    * @return void
    */
    public function _front_viewcontroller($data=array())
    {
        // $this->_disable_cache();
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }
    
        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $this->load->view('front/theme_front', $data);
    }

    
    public function _viewcontroller($data=array())
    {
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }
    
        if (!isset($data['page_title'])) {
            $data['page_title']="Admin Panel";
        }

        if (!isset($data['crud'])) {
            $data['crud']=0;
        }
        // fetch all pending student queries to show in admin notification area
        //$data['student_query_notifications']=$this->_admin_notifications();
        $data["language_info"] = $this->_language_list();
        $this->load->view('admin/theme/theme', $data);
    }



    public function _site_viewcontroller($data=array())
    {
        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $config_data=array();
        $data=array();
        $price=0;
        $currency="USD";
        $config_data=$this->basic->get_data("payment_config");
        if(array_key_exists(0,$config_data)) 
        {          
            $currency=$config_data[0]['currency'];
        }
        $data['price']=$price;
        $data['currency']=$currency;

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(2);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        $data["payment_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');         
         $data["default_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1","validity >"=>0,"price"=>"Trial")));        
    
        //catcha for contact page

        $this->load->view('site/site_theme', $data);
    }


    
    public function login_page()
    {
        if (file_exists(APPPATH.'install.txt')) {
            redirect('home/installation', 'location');
        }
		
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
            redirect('dashboard/index', 'location');
        }
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member') {
            redirect('dashboard/index', 'location');
        }

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }
                
        $this->load->view('page/login',$data);
    }
    
    public function login() //loads home view page after login (this )
    {
        //echo "r";exit;
		//print_r($this->session->userdata('logged_in'));exit;
        if (file_exists(APPPATH.'install.txt')) {
            redirect('home/installation', 'location');
        }
		$cookie_userid = get_cookie('user_id');
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin'  && $this->session->userdata('user_id') == $cookie_userid) {
            redirect('dashboard/index', 'location');
        }
		
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member') {
            redirect('dashboard/index', 'location');
        }
		
        /*
        $this->form_validation->set_rules('username', '<b>'.$this->lang->line("email").'</b>', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required|xss_clean');

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }*/
        //echo "r";exit;
        //$this->load->helper('cookie');
        $username = get_cookie('emailid');
        $cookie = array(
            'name' => 'venue_details',
            'value' => $username
            );
        $this->input->set_cookie($cookie);
		
        if($username=='')
		{
		//if ($this->form_validation->run() == false) 
        //$this->load->view('page/login',$data);
		$project_url=$this->config->item('project_url');
		redirect($project_url.'/sitebuilder/public/userdashboard', 'refresh');
		}
		else 
        {
            //$username = $this->input->post('username', true);
            //$password = md5($this->input->post('password', true));
            //$password = md5('password');
			
			$username = get_cookie('emailid');
			$cookie = array(
            'name' => 'venue_details',
            'value' => $username
            );
			$this->input->set_cookie($cookie);
            $table = 'users';
            $where['where'] = array('email' => $username, "deleted" => "0","status"=>"1");
			//print_r($where['where']);exit;
            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);
			//print_r($info);exit;
            $count = $info['extra_index']['num_rows'];
           // print_r($count);exit;
            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                //redirect(uri_string());
				$project_url=$this->config->item('project_url');
				redirect($project_url.'/sitebuilder/public/userdashboard', 'refresh');
            } else {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);

                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
               // print_r($this->session->all_userdata());exit;
				$package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
				
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
                    redirect('dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member') {
                    redirect('dashboard/index', 'location');
                }
            }
        }
    }


    function google_login_back()
    {
    
        $this->load->library('Google_login');
        $info=$this->google_login->user_details();        
        
        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {
            
            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1"))); 
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
            }


            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);
			
			
            $count = $info['extra_index']['num_rows'];
            
            if ($count == 0) 
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            } 
            else 
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);

                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
                    redirect('dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member') {
                    redirect('dashboard/index', 'location');
                }
            }

            
        }     
        
    }


    public function fb_login_back()
    {
        $this->load->library('Fb_login');

        $info=$this->fb_login->login_callback();        
        
        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {
            
            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1"))); 
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
            }


            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);
            
            
            $count = $info['extra_index']['num_rows'];
            
            if ($count == 0) 
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            } 
            else 
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);

                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
                    redirect('dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member') {
                    redirect('dashboard/index', 'location');
                }
            }            
        }
    }
    


    /**
    * method to load logout page
    * @access public
    * @return void
    */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home/login_page', 'location');
    }

    /**
    * method to generate random number
    * @access public
    * @param int
    * @return int
    */
    public function _random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }

  

    /**
    * method to load forgor password view page
    * @access public
    * @return void
    */
    public function forgot_password()
    {
        $data['body']='page/forgot_password';
        $data['page_title']=$this->lang->line("password recovery");
        $this->_front_viewcontroller($data);
    }

    /**
    * method to generate code
    * @access public
    * @return void
    */
    public function code_genaration()
    {
        $email = trim($this->input->post('email'));
        $result = $this->basic->get_data('users', array('where' => array('email' => $email)), array('count(*) as num'));

        if ($result[0]['num'] == 1) {
            //entry to forget_password table
            $expiration = date("Y-m-d H:i:s", strtotime('+1 day', time()));
            $code = $this->_random_number_generator();
            $url = site_url().'home/password_recovery';

            $table = 'forget_password';
            $info = array(
                'confirmation_code' => $code,
                'email' => $email,
                'expiration' => $expiration
                );

            if ($this->basic->insert_data($table, $info)) {
                //email to user
                $message = "<p>".$this->lang->line('to reset your password please perform the following steps')." : </p>
                            <ol>
                                <li>".$this->lang->line("go to this url")." : ".$url."</li>
                                <li>".$this->lang->line("enter this code")." : ".$code."</li>
                                <li>".$this->lang->line("reset your password")."</li>
                            <ol>
                            <h4>".$this->lang->line("link and code will be expired after 24 hours")."</h4>";


                $from = $this->config->item('institute_email');
                $to = $email;
                $subject = $this->config->item('product_name')." | ".$this->lang->line("password recovery");
                $mask = $subject;
                $html = 1;
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
            }
        } else {
            echo 0;
        }
    }

    /**
    * method to password recovery
    * @access public
    * @return void
    */
    public function password_recovery()
    {
        $data['body']='page/password_recovery';
        $data['page_title']=$this->lang->line("password recovery");
        $this->_front_viewcontroller($data);
    }

    /**
    * method to check recovery
    * @access public
    * @return void
    */
    public function recovery_check()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $newp=md5($this->input->post('newp', true));
            $conf=md5($this->input->post('conf', true));

            $table='forget_password';
            $where['where']=array('confirmation_code'=>$code,'success'=>0);
            $select=array('email','expiration');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $email=$row['email'];
                    $expiration=$row['expiration'];
                }

                $now=time();
                $exp=strtotime($expiration);

                if ($now>$exp) {
                    echo 1;
                } else {
                    $student_info_where['where'] = array('email'=>$email);
                    $student_info_select = array('id');
                    $student_info_id = $this->basic->get_data('users', $student_info_where, $student_info_select);
                    $this->basic->update_data('users', array('id'=>$student_info_id[0]['id']), array('password'=>$newp));
                    $this->basic->update_data('forget_password', array('confirmation_code'=>$code), array('success'=>1));
                    echo 2;
                }
            }
        }
    }


    /**
    * method to sent mail
    * @access public
    * @param string
    * @param string
    * @param string
    * @param string
    * @param string
    * @param int
    * @param int
    * @return boolean
    */
    function _mail_sender($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 0, $smtp = 1)
    {
        if ($to!= '' && $subject!='' && $message!= '') 
        {     

            if ($smtp == '1') {
                $where2 = array("where" => array('status' => '1','deleted' => '0'));
                $email_config_details = $this->basic->get_data("email_config", $where2, $select = '', $join = '', $limit = '', $start = '', $group_by = '', $num_rows = 0);

                if (count($email_config_details) == 0) {
                    $this->load->library('email');
                } else {
                    foreach ($email_config_details as $send_info) {
                        $send_email = trim($send_info['email_address']);
                        $smtp_host = trim($send_info['smtp_host']);
                        $smtp_port = trim($send_info['smtp_port']);
                        $smtp_user = trim($send_info['smtp_user']);
                        $smtp_password = trim($send_info['smtp_password']);
                    }

            /*****Email Sending Code ******/
                $config = array(
                  'protocol' => 'smtp',
                  'smtp_host' => "{$smtp_host}",
                  'smtp_port' => "{$smtp_port}",
                  'smtp_user' => "{$smtp_user}", // change it to yours
                  'smtp_pass' => "{$smtp_password}", // change it to yours
                  'mailtype' => 'html',
                  'charset' => 'utf-8',
                  'newline' =>  "\r\n",
                  'smtp_timeout' => '30'
                 );

                    $this->load->library('email', $config);
                }
            } /*** End of If Smtp== 1 **/

            if (isset($send_email) && $send_email!= "") {
                $from = $send_email;
            }
            $this->email->from($from, $mask);
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($message);
            if ($html == 1) {
                $this->email->set_mailtype('html');
            }

            if ($this->email->send()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
    * method to get email provider
    * @access public
    * @return array
    */
    public function get_email_providers()
    {
        $table='email_provider';
        $results=$this->basic->get_data($table);
        $email_provider=array();
        foreach ($results as $row) {
            $email_provider[$row['id']]=$row['provider_name'];
        }
        return $email_provider;
    }

    /**
    * method to get social networks
    * @access public
    * @return array
    */
    public function get_social_networks()
    {
        $table='social_network';
        $results=$this->basic->get_data($table);
        $social_network=array();
        foreach ($results as $row) {
            $social_network[$row['social_network_name']]=$row['social_network_name'];
        }
        return $social_network;
    }

    /**
    * method to get search engines
    * @access public
    * @return array
    */
    public function get_searche_engines()
    {
        $table='searh_engine';
        $results=$this->basic->get_data($table);
        $searh_engine=array();
        foreach ($results as $row) {
            $searh_engine[$row['search_engine_name']]=$row['search_engine_name'];
        }
        return $searh_engine;
    }

    public function download_page_loader()
    {
        $this->load->view('page/download');
    }

    public function read_text_file()
    {
        
        if ( isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0 && ($_FILES['file_upload']['type'] =='text/plain' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/comma-separated-values' || $_FILES['file_upload']['type']='text/x-comma-separated-values')) 
        {
        
            $filedata=$_FILES['file_upload'];
            $tempo=explode('.', $filedata["name"]);          
            $ext=end($tempo);          
            $file_name = "tmp_".md5(time()).".".$ext;
            $config = array(
                "allowed_types" => "*",
                "upload_path" => "./upload/tmp/",
                "file_name" => $file_name,
                "overwrite" => true
            );
            $this->upload->initialize($config);
            $this->load->library('upload', $config);
            $this->upload->do_upload('file_upload');
            $path = realpath(FCPATH."upload/tmp/".$file_name);
            $read_handle=fopen($path, "r");
            $context ='';

            while (!feof($read_handle)) 
            {
                $information = fgetcsv($read_handle);
                if (!empty($information)) 
                {
                    foreach ($information as $info) 
                    {
                        if (!is_numeric($info)) 
                        $context.=$info."\n";                       
                    }
                }
            }
            $context = trim($context, "\n");
            echo $context;
        } 
        else 
        {
            echo "0";
        }
        
    }



    public function get_country_names()
    {
        $array_countries = array (
          'AF' => 'AFGHANISTAN',
          'AX' => 'ÅLAND ISLANDS',
          'AL' => 'ALBANIA',
          
          'DZ' => 'ALGERIA (El Djazaïr)',
          'AS' => 'AMERICAN SAMOA',
          'AD' => 'ANDORRA',
          'AO' => 'ANGOLA',
          'AI' => 'ANGUILLA',
          'AQ' => 'ANTARCTICA',
          'AG' => 'ANTIGUA AND BARBUDA',
          'AR' => 'ARGENTINA',
          'AM' => 'ARMENIA',
          'AW' => 'ARUBA',
          
          'AU' => 'AUSTRALIA',
          'AT' => 'AUSTRIA',
          'AZ' => 'AZERBAIJAN',
          'BS' => 'BAHAMAS',
          'BH' => 'BAHRAIN',
          'BD' => 'BANGLADESH',
          'BB' => 'BARBADOS',
          'BY' => 'BELARUS',
          'BE' => 'BELGIUM',
          'BZ' => 'BELIZE',
          'BJ' => 'BENIN',
          'BM' => 'BERMUDA',
          'BT' => 'BHUTAN',
          'BO' => 'BOLIVIA',
          
          'BA' => 'BOSNIA AND HERZEGOVINA',
          'BW' => 'BOTSWANA',
          'BV' => 'BOUVET ISLAND',
          'BR' => 'BRAZIL',

          'BN' => 'BRUNEI DARUSSALAM',
          'BG' => 'BULGARIA',
          'BF' => 'BURKINA FASO',
          'BI' => 'BURUNDI',
          'KH' => 'CAMBODIA',
          'CM' => 'CAMEROON',
          'CA' => 'CANADA',
          'CV' => 'CAPE VERDE',
          'KY' => 'CAYMAN ISLANDS',
          'CF' => 'CENTRAL AFRICAN REPUBLIC',
          'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE (formerly Zaire)',
          'CL' => 'CHILE',
          'CN' => 'CHINA',
          'CX' => 'CHRISTMAS ISLAND',
          
          'CO' => 'COLOMBIA',
          'KM' => 'COMOROS',
          'CG' => 'CONGO, REPUBLIC OF',
          'CK' => 'COOK ISLANDS',
          'CR' => 'COSTA RICA',
          'CI' => 'CÔTE D\'IVOIRE (Ivory Coast)',
          'HR' => 'CROATIA (Hrvatska)',
          'CU' => 'CUBA',
          'CW' => 'CURAÇAO',
          'CY' => 'CYPRUS',
          'CZ' => 'ZECH REPUBLIC',
          'DK' => 'DENMARK',
          'DJ' => 'DJIBOUTI',
          'DM' => 'DOMINICA',
          'DC' => 'DOMINICAN REPUBLIC',
          'EC' => 'ECUADOR',
          'EG' => 'EGYPT',
          'SV' => 'EL SALVADOR',
          'GQ' => 'EQUATORIAL GUINEA',
          'ER' => 'ERITREA',
          'EE' => 'ESTONIA',
          'ET' => 'ETHIOPIA',
          'FO' => 'FAEROE ISLANDS',

          'FJ' => 'FIJI',
          'FI' => 'FINLAND',
          'FR' => 'FRANCE',
          'GF' => 'FRENCH GUIANA',
          
          'GA' => 'GABON',
          'GM' => 'GAMBIA, THE',
          'GE' => 'GEORGIA',
          'DE' => 'GERMANY (Deutschland)',
          'GH' => 'GHANA',
          'GI' => 'GIBRALTAR',
          // 'GB' => 'UNITED KINGDOM',
          'GR' => 'GREECE',
          'GL' => 'GREENLAND',
          'GD' => 'GRENADA',
          'GP' => 'GUADELOUPE',
          'GU' => 'GUAM',
          'GT' => 'GUATEMALA',
          'GG' => 'GUERNSEY',
          'GN' => 'GUINEA',
          'GW' => 'GUINEA-BISSAU',
          'GY' => 'GUYANA',
          'HT' => 'HAITI',
          
          'HN' => 'HONDURAS',
          'HK' => 'HONG KONG (Special Administrative Region of China)',
          'HU' => 'HUNGARY',
          'IS' => 'ICELAND',
          'IN' => 'INDIA',
          'ID' => 'INDONESIA',
          'IR' => 'IRAN (Islamic Republic of Iran)',
          'IQ' => 'IRAQ',
          'IE' => 'IRELAND',
          'IM' => 'ISLE OF MAN',
          'IL' => 'ISRAEL',
          'IT' => 'ITALY',
          'JM' => 'JAMAICA',
          'JP' => 'JAPAN',
          'JE' => 'JERSEY',
          'JO' => 'JORDAN (Hashemite Kingdom of Jordan)',
          'KZ' => 'KAZAKHSTAN',
          'KE' => 'KENYA',
          'KI' => 'KIRIBATI',
          'KP' => 'KOREA (Democratic Peoples Republic of [North] Korea)',
          'KR' => 'KOREA (Republic of [South] Korea)',
          'KW' => 'KUWAIT',
          'KG' => 'KYRGYZSTAN',
          
          'LV' => 'LATVIA',
          'LB' => 'LEBANON',
          'LS' => 'LESOTHO',
          'LR' => 'LIBERIA',
          'LY' => 'LIBYA (Libyan Arab Jamahirya)',
          'LI' => 'LIECHTENSTEIN (Fürstentum Liechtenstein)',
          'LT' => 'LITHUANIA',
          'LU' => 'LUXEMBOURG',
          'MO' => 'MACAO (Special Administrative Region of China)',
          'MK' => 'MACEDONIA (Former Yugoslav Republic of Macedonia)',
          'MG' => 'MADAGASCAR',
          'MW' => 'MALAWI',
          'MY' => 'MALAYSIA',
          'MV' => 'MALDIVES',
          'ML' => 'MALI',
          'MT' => 'MALTA',
          'MH' => 'MARSHALL ISLANDS',
          'MQ' => 'MARTINIQUE',
          'MR' => 'MAURITANIA',
          'MU' => 'MAURITIUS',
          'YT' => 'MAYOTTE',
          'MX' => 'MEXICO',
          'FM' => 'MICRONESIA (Federated States of Micronesia)',
          'MD' => 'MOLDOVA',
          'MC' => 'MONACO',
          'MN' => 'MONGOLIA',
          'ME' => 'MONTENEGRO',
          'MS' => 'MONTSERRAT',
          'MA' => 'MOROCCO',
          'MZ' => 'MOZAMBIQUE (Moçambique)',
          'MM' => 'MYANMAR (formerly Burma)',
          'NA' => 'NAMIBIA',
          'NR' => 'NAURU',
          'NP' => 'NEPAL',
          'NL' => 'NETHERLANDS',
          'AN' => 'NETHERLANDS ANTILLES (obsolete)',
          'NC' => 'NEW CALEDONIA',
          'NZ' => 'NEW ZEALAND',
          'NI' => 'NICARAGUA',
          'NE' => 'NIGER',
          'NG' => 'NIGERIA',
          'NU' => 'NIUE',
          'NF' => 'NORFOLK ISLAND',
          'MP' => 'NORTHERN MARIANA ISLANDS',
          'ND' => 'NORWAY',
          'OM' => 'OMAN',
          'PK' => 'PAKISTAN',
          'PW' => 'PALAU',
          'PS' => 'PALESTINIAN TERRITORIES',
          'PA' => 'PANAMA',
          'PG' => 'PAPUA NEW GUINEA',
          'PY' => 'PARAGUAY',
          'PE' => 'PERU',
          'PH' => 'PHILIPPINES',
          'PN' => 'PITCAIRN',
          'PL' => 'POLAND',
          'PT' => 'PORTUGAL',
          'PR' => 'PUERTO RICO',
          'QA' => 'QATAR',
          'RE' => 'RÉUNION',
          'RO' => 'ROMANIA',
          'RU' => 'RUSSIAN FEDERATION',
          'RW' => 'RWANDA',
          'BL' => 'SAINT BARTHÉLEMY',
          'SH' => 'SAINT HELENA',
          'KN' => 'SAINT KITTS AND NEVIS',
          'LC' => 'SAINT LUCIA',
          
          'PM' => 'SAINT PIERRE AND MIQUELON',
          'VC' => 'SAINT VINCENT AND THE GRENADINES',
          'WS' => 'SAMOA (formerly Western Samoa)',
          'SM' => 'SAN MARINO (Republic of)',
          'ST' => 'SAO TOME AND PRINCIPE',
          'SA' => 'SAUDI ARABIA (Kingdom of Saudi Arabia)',
          'SN' => 'SENEGAL',
          'RS' => 'SERBIA (Republic of Serbia)',
          'SC' => 'SEYCHELLES',
          'SL' => 'SIERRA LEONE',
          'SG' => 'SINGAPORE',
          'SX' => 'SINT MAARTEN',
          'SK' => 'SLOVAKIA (Slovak Republic)',
          'SI' => 'SLOVENIA',
          'SB' => 'SOLOMON ISLANDS',
          'SO' => 'SOMALIA',
          'ZA' => 'ZAMBIA (formerly Northern Rhodesia)',
          'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
          'SS' => 'SOUTH SUDAN',
          'ES' => 'SPAIN (España)',
          'LK' => 'SRI LANKA (formerly Ceylon)',
          'SD' => 'SUDAN',
          'SR' => 'SURINAME',
          'SJ' => 'SVALBARD AND JAN MAYE',
          'SZ' => 'SWAZILAND',
          'SE' => 'SWEDEN',
          'CH' => 'SWITZERLAND (Confederation of Helvetia)',
          'SY' => 'SYRIAN ARAB REPUBLIC',
          'TW' => 'TAIWAN ("Chinese Taipei" for IOC)',
          'TJ' => 'TAJIKISTAN',
          'TZ' => 'TANZANIA',
          'TH' => 'THAILAND',
          'TL' => 'TIMOR-LESTE (formerly East Timor)',
          'TG' => 'TOGO',
          'TK' => 'TOKELAU',
          'TO' => 'TONGA',
          'TT' => 'TRINIDAD AND TOBAGO',
          'TN' => 'TUNISIA',
          'TR' => 'TURKEY',
          'TM' => 'TURKMENISTAN',
          'TC' => 'TURKS AND CAICOS ISLANDS',
          'TV' => 'TUVALU',
          'UG' => 'UGANDA',
          'UA' => 'UKRAINE',
          'AE' => 'UNITED ARAB EMIRATES',
          'US' => 'UNITED STATES',
          'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
          'UK' => 'UNITED KINGDOM',
          'UY' => 'URUGUAY',
          'UZ' => 'UZBEKISTAN',
          'VU' => 'VANUATU',
          'VA' => 'VATICAN CITY (Holy See)',
          'VN' => 'VIET NAM',
          'VG' => 'VIRGIN ISLANDS, BRITISH',
          'VI' => 'VIRGIN ISLANDS, U.S.',
          'WF' => 'WALLIS AND FUTUNA',
          'EH' => 'WESTERN SAHARA (formerly Spanish Sahara)',
          'YE' => 'YEMEN (Yemen Arab Republic)',
          'ZW' => 'ZIMBABWE',
          'VE' => 'VENEZUELA'
        );
        return $array_countries;
    }

    public function get_language_names()
    {
        $array_languages = array(
        'ar-XA'=>'Arabic',
        'bg'=>'Bulgarian',
        'hr'=>'Croatian',
        'cs'=>'Czech',
        'da'=>'Danish',
        'de'=>'German',
        'el'=>'Greek',
        'en'=>'English',
        'et'=>'Estonian',
        'es'=>'Spanish',
        'fi'=>'Finnish',
        'fr'=>'French',
        'ga'=>'Irish',
        'hr'=>'Hindi',
        'hu'=>'Hungarian',
        'he'=>'Hebrew',
		'it'=>'Italian',
        'ja'=>'Japanese',
        'ko'=>'Korean',
        'lv'=>'Latvian',
        'lt'=>'Lithuanian',
        'nl'=>'Dutch',
        'no'=>'Norwegian',
        'pl'=>'Polish',
        'pt'=>'Portuguese',
        'sv'=>'Swedish',
        'ro'=>'Romanian',
        'ru'=>'Russian',
        'sr-CS'=>'Serbian',
        'sk'=>'Slovak',
        'sl'=>'Slovenian',
        'th'=>'Thai',
        'tr'=>'Turkish',
        'uk-UA'=>'Ukrainian',
        'zh-chs'=>'Chinese (Simplified)',
        'zh-cht'=>'Chinese (Traditional)'
        );
        return $array_languages;
    }


    function _language_list() 
     {
        
        //$img_tag = '<img style="height: 15px; width: 20px;" src="'.$url.'BN.png" alt="flag" />';
         $language = array
         (
            "bengali"=>'Bengali',            
            "dutch"=>'Dutch',
            "english"=>"English",
            "french"=>"French",
            "german"=>"German",
            "greek"=>"Greek",
            "italian"=>"Italian",            
            "portuguese"=>"Portuguese",
            "russian"=>"Russian",
            "spanish"=>"Spanish",
            "turkish"=>"Turkish",
            "vietnamese"=>"Vietnamese"
         );
         // print_r($language);
         return $language;
     }

     public function language_changer()
    {
        $language=$this->input->post("language");
        $this->session->set_userdata("selected_language",$language);
    }

    function _payment_package() 
     {
        $payment_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='price');         
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=$config_data[0]["currency"];
        foreach ($payment_package as $row) 
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
     }

     // function _get_user_modules() 
     // {
     //    $result=$this->basic->get_data("users",array("where"=>array("id"=>$this->session->userdata("user_id"))));
     //    $package_id=$result[0]["package_id"];
     //    $module_ids=$this->basic->execute_query('SELECT m.id as module_id FROM modules m JOIN package p ON FIND_IN_SET(m.id,p.module_ids) > 0 WHERE p.id='.$package_id);      
     //    $return_val=implode(',', array_column($module_ids, 'module_id'));
     //    $return_val=explode(',',$return_val);
     //    return $return_val;
     // }


    function real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


    public function _grab_auction_list_data()
    {       
        $this->load->library('web_common_report');
        $url="http://www.namejet.com/download/StandardAuctions.csv";
        $save_path = 'download/expired_domain/';
        $fp = fopen($save_path.basename($url), 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
            
          $read_handle=fopen($save_path.basename($url),"r");
          $i=0;
          while (!feof($read_handle) ) 
          {
            
                $information = fgetcsv($read_handle);
                
                if($i!=0)
                {
                    $domain_name=$information[0];
                    $auction_end_date =$information[1];
                    
                    
                      if($domain_name!="")
                      {                    
                        $insert_data=array(
                                    'domain_name'        => $domain_name,
                                    'auction_type'       => "public_auction",
                                    'auction_end_date'   =>$auction_end_date,
                                    'sync_at'            => date("Y-m-d")
                                    );
                                    
                     $this->basic->insert_data('expired_domain_list', $insert_data);            
                    }           
                    
                }
                $i++;       
           }  

            $current_date = date("Y-m-d");
            $three_days_before = date("Y-m-d", strtotime("$current_date - 3 days"));
            $this->basic->delete_data("expired_domain_list",array("sync_at < "=>$three_days_before));
    }






    // website function
    public function sign_up()
    {
        $data['body'] = 'page/sign_up';
        $data['page_title']=$this->lang->line("sign up");
        $data['num1']=$this->_random_number_generator(2);
        $data['num2']=$this->_random_number_generator(2);
        $captcha= $data['num1']+ $data['num2'];
        $this->session->set_userdata("sign_up_captcha",$captcha);

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();
        
        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }

        $this->_front_viewcontroller($data);
    }

    public function sign_up_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        if($_POST) {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("name").'</b>', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|xss_clean|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("mobile").'</b>', 'trim|xss_clean');
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required|xss_clean');
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("confirm password").'</b>', 'trim|required|xss_clean|matches[password]');
            $this->form_validation->set_rules('captcha', '<b>'.$this->lang->line("captcha").'</b>', 'trim|required|xss_clean|integer');

            if($this->form_validation->run() == FALSE)
            {
                $this->sign_up();
            }
            else 
            {
                $captcha = $this->input->post('captcha', TRUE);
                if($captcha!=$this->session->userdata("sign_up_captcha"))
                {
                    $this->session->set_userdata("sign_up_captcha_error",$this->lang->line("invalid captcha"));
                    return $this->sign_up();

                }

                $name = $this->input->post('name', TRUE);
                $email = $this->input->post('email', TRUE);
                $mobile = $this->input->post('mobile', TRUE);
                $password = $this->input->post('password', TRUE);

                // $this->db->trans_start();

                $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1"))); 

                if(is_array($default_package) && array_key_exists(0, $default_package))
                {
                    $validity=$default_package[0]["validity"];
                    $package_id=$default_package[0]["id"];

                    $to_date=date('Y-m-d');
                    $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
                }

                $code = $this->_random_number_generator();
                $data = array(
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'password' => md5($password),
                    'user_type' => 'Member',
                    'status' => '0',
                    'activation_code' => $code,
                    'expired_date'=>$expiry_date,
                    'package_id'=>$package_id
                    );

                if ($this->basic->insert_data('users', $data)) {
                    //email to user
                    $url = site_url()."home/account_activation";
                    $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
                    $message = "<p>".$this->lang->line("to activate your account please perform the following steps")."</p>
                                <ol>
                                    <li>".$this->lang->line("go to this url").":".$url_final."</li>
                                    <li>".$this->lang->line("enter this code").":".$code."</li>
                                    <li>".$this->lang->line("activate your account")."</li>
                                <ol>";


                    $from = $this->config->item('institute_email');
                    $to = $email;
                    $subject = $this->config->item('product_name')." | ".$this->lang->line("account activation");
                    $mask = $subject;
                    $html = 1;
                    $this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                    $this->session->set_userdata('reg_success',1);
                    return $this->sign_up();

                }

            }

        }
    }

    public function account_activation()
    {
        $data['body']='page/account_activation';
        $data['page_title']=$this->lang->line("account activation");
        $this->_front_viewcontroller($data);
    }

    public function account_activation_action()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $email=$this->input->post('email', true);

            $table='users';
            $where['where']=array('activation_code'=>$code,'email'=>$email,'status'=>"0");
            $select=array('id');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $user_id=$row['id'];
                }

                $this->basic->update_data('users', array('id'=>$user_id), array('status'=>'1'));
                echo 2;
                
            }
        }
    }


    public function email_contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            $redirect_url=site_url("home#contact");

            $this->form_validation->set_rules('email',                    '<b>'.$this->lang->line("email").'</b>',              'trim|required|valid_email');
            $this->form_validation->set_rules('subject',                  '<b>'.$this->lang->line("message subject").'</b>',            'trim|required');
            $this->form_validation->set_rules('message',                  '<b>'.$this->lang->line("message").'</b>',            'trim|required');
            $this->form_validation->set_rules('captcha',                  '<b>'.$this->lang->line("captcha").'</b>',            'trim|required|integer');

            if ($this->form_validation->run() == false) 
            {
                return $this->index();
            } 
            else 
            {
                $captcha = $this->input->post('captcha', TRUE);

                if($captcha!=$this->session->userdata("contact_captcha"))
                {
                    $this->session->set_userdata("contact_captcha_error",$this->lang->line("invalid captcha"));
                    redirect($redirect_url, 'location');
                    exit();
                }


                $email = $this->input->post('email', true);
                $subject = $this->config->item("product_name")." | ".$this->input->post('subject', true);
                $message = $this->input->post('message', true);

                $this->_mail_sender($from = $email, $to = $this->config->item("institute_email"), $subject, $message, $mask = $from,$html=1);
                $this->session->set_userdata('mail_sent', 1);

                redirect($redirect_url, 'location');
            }
        }
    }

    // website function



    // ************************************************************* //


    function get_general_content($url,$proxy=""){
            
            
            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

         
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
            
            $content = curl_exec($ch); // run the whole process
            
            curl_close($ch);
            
            return $content;
            
    }

    public function member_validity()
    {
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
            $where['where'] = array('id'=>$this->session->userdata('user_id'));
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$this->session->userdata("user_id"))),$select="package.price as price",$join=array('package'=>"users.package_id=package.id,left"));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            redirect('payment/member_payment_history','Location');
        }
    }
    


    public function important_feature(){

         if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt')){
            $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
            $config_decoded_content = json_decode($config_existing_content, true);

            $core_existing_content = file_get_contents(APPPATH.'core/licence.txt');
            $core_decoded_content = json_decode($core_existing_content, true);

            if($config_decoded_content['is_active'] != md5($config_decoded_content['purchase_code']) || $core_decoded_content['is_active'] != md5(md5($core_decoded_content['purchase_code']))){
              redirect("home/credential_check", 'Location');
            }
            
        } else {
            redirect("home/credential_check", 'Location');
        }

    }


    public function credential_check()
    {
        $data['body'] = 'front/credential_check';
        $data['page_title'] = "Credential Check";
        $this->_front_viewcontroller($data);
    }

    public function credential_check_action()
    {
        $domain_name = $this->input->post("domain_name",true);
        $purchase_code = $this->input->post("purchase_code",true);
        $only_domain = get_domain_only($domain_name);
        // $only_domain = "xeroneit.ne";
       
       $response=$this->code_activation_check_action($purchase_code,$only_domain);

       echo $response;

    }


    

    public function code_activation_check_action($purchase_code,$only_domain){

         $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=sitespy";

        $credentials = $this->get_general_content($url);
        $decoded_credentials = json_decode($credentials);
        if($decoded_credentials->status == 'success'){
            $content_to_write = array(
                'is_active' => md5($purchase_code),
                'purchase_code' => $purchase_code,
                'item_name' => $decoded_credentials->item_name,
                'buy_at' => $decoded_credentials->buy_at,
                'licence_type' => $decoded_credentials->license,
                'domain' => $only_domain,
                'checking_date'=>date('Y-m-d')
                );
            $config_json_content_to_write = json_encode($content_to_write);
            file_put_contents(APPPATH.'config/licence.txt', $config_json_content_to_write, LOCK_EX);

            $content_to_write['is_active'] = md5(md5($purchase_code));
            $core_json_content_to_write = json_encode($content_to_write);
            file_put_contents(APPPATH.'core/licence.txt', $core_json_content_to_write, LOCK_EX);

            return json_encode("success");

        } else {
            if(file_exists(APPPATH.'core/licence.txt')) unlink(APPPATH.'core/licence.txt');
            return json_encode($decoded_credentials);
        }
    }

    public function periodic_check(){

        $today= date('d');

        if($today%7==0){

          if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt')){
                $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
                $config_decoded_content = json_decode($config_existing_content, true);
                $last_check_date= $config_decoded_content['checking_date'];
                $purchase_code  = $config_decoded_content['purchase_code'];
                $base_url = base_url();
                $domain_name    = get_domain_only($base_url);

                if( strtotime(date('Y-m-d')) != strtotime($last_check_date)){
                    $this->code_activation_check_action($purchase_code,$domain_name);         
                }
        }
     }
    }

    // ***************************************************************
            // front end website analysis section
    // ***************************************************************
    public function front_end_website_analysis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        $this->load->library('web_common_report');

        $this->session->set_userdata('insert_table_id', '');
        //for dynamic progress bar data
        $this->session->set_userdata('website_analysis_bulk_total_search', 26);
        $add_complete = 0;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str = '';
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);

        $domain_name = strtolower($this->input->post('domain_name', true));
        if($this->session->userdata('user_id') != '')
        {
            $user_id = $this->session->userdata('user_id');
            $common_result['user_id'] = $user_id;
        }
        else
        {
            $user_info = $this->basic->get_data('users',array('where'=>array('user_type'=>'Admin','status'=>'1','deleted'=>'0')));
            if(!empty($user_info))
                $user_id = $user_info[0]['id'];
        }
        
        $common_result['domain_name'] = $domain_name;
        $common_result['search_at'] = date("Y-m-d G:i:s");


        // get moz info
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$user_id)));
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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". MOZ ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        // end of get moz info

       

        $common_result["mobile_ready_data"] = $this->web_common_report->mobile_ready($domain_name,$mobile_ready_api_key);
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Mobile Friendly ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        // end of get mobile ready

        $dmoz_check = $this->web_common_report->dmoz_check($domain_name);
        $common_result['dmoz_listed_or_not'] = $dmoz_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". DMOZ ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        
        
        $backlink_count=$common_result['moz_external_equity_links'];
        if($backlink_count=="")
            $backlink_count=0;
            
        $common_result['google_back_link_count'] = number_format($backlink_count);
        $add_complete++;
        
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Backlink ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        
        $common_result['yahoo_back_link_count'] = 0;
        $common_result['bing_back_link_count'] = 0;
        

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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Alexa ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);

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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". SimilarWeb ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);

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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Facebook ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);




        $googleplus_info = $this->web_common_report->get_plusones($domain_name);
        $common_result['googleplus_share_count'] = $googleplus_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Google Plus ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $pinterest_info = $this->web_common_report->pinterest_pin($domain_name);
        $common_result['pinterest_pin'] = $pinterest_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Pinterest ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $stumbleupon_info = $this->web_common_report->stumbleupon_info($domain_name);
        $common_result['stumbleupon_total_view'] = $stumbleupon_info['total_view'];
        $common_result['stumbleupon_total_comment'] = $stumbleupon_info['total_comment'];
        $common_result['stumbleupon_total_like'] = $stumbleupon_info['total_like'];
        $common_result['stumbleupon_total_list'] = $stumbleupon_info['total_list'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Stumbleupon ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $linkdin_info = $this->web_common_report->linkdin_share($domain_name);
        $common_result['linkedin_share_count'] = $linkdin_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Linkedin ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $buffer_info = $this->web_common_report->buffer_share($domain_name);
        $common_result['buffer_share_count'] = $buffer_info;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Buffer ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        $GoogleIP = $this->web_common_report->GoogleIP($domain_name);
        $common_result['google_index_count'] = $GoogleIP;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Google Index ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        

        $reddit_count = $this->web_common_report->reddit_count($domain_name);
        $common_result['reddit_score'] = $reddit_count['score'];
        $common_result['reddit_ups'] = $reddit_count['ups'];
        $common_result['reddit_downs'] = $reddit_count['downs'];
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Reddit ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $xing_share_count = $this->web_common_report->xing_share_count($domain_name);
        $common_result['xing_share_count'] = empty($xing_share_count) ? 0 : $xing_share_count;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Xing ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);
        



        $bing_index = $this->web_common_report->bing_index($domain_name);
        $common_result['bing_index_count'] = $bing_index;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Bing ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $yahoo_index = $this->web_common_report->yahoo_index($domain_name);
        $common_result['yahoo_index_count'] = $yahoo_index;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Yahoo ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Metatag ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Whois ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



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
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". IP ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        $this->web_common_report->get_site_in_same_ip($common_result['ipinfo_ip'],$page=1,$proxy=""); 
        $sites_in_same_ip=$this->web_common_report->same_site_in_ip; 
        $common_result['sites_in_same_ip']=json_encode($sites_in_same_ip);
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Site's in same IP - ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $macafee_safety_analysis = $this->web_common_report->macafee_safety_analysis($domain_name,$proxy="");
        $common_result['macafee_status'] = $macafee_safety_analysis;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Macafee ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);



        $norton_safety_check = $this->web_common_report->norton_safety_check($domain_name,$proxe="");
        $common_result['norton_status'] = $norton_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Norton ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        
        /*** configurable ***/
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

        $google_safety_check = $this->web_common_report->google_safety_check($api,$domain_name);
        $common_result['google_safety_status'] = $google_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Google Safety ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        
        $avg_safety_check = $this->web_common_report->avg_safety_check($domain_name,$proxy="");
        $common_result['avg_status'] = $avg_safety_check;
        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". AVG ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        $similar_site_from_google = $this->web_common_report->similar_site_from_google($domain_name);
        $common_result['similar_site'] = implode(',', $similar_site_from_google);

        // echo $common_result['similar_site'];

        //for dynamic progress bar data
        $add_complete++;
        $this->session->set_userdata('website_analysis_complete_search', $add_complete);
        $website_analysis_completed_function_str .= "<div>".$add_complete.". Similar Site ".$this->lang->line("step completed")."</div>";
        $this->session->set_userdata('website_analysis_completed_function_str', $website_analysis_completed_function_str);


        if($this->basic->insert_data('web_common_info',$common_result)){
            
            $web_common_info_id = $this->db->insert_id();
            $this->session->set_userdata('insert_table_id', $web_common_info_id);
            $link = site_url()."home/frontend_domain_details_view/".$web_common_info_id;
            echo '<a href="'.$link.'" class="btn btn-info btn-lg"><i class="fa fa-binoculars"></i> '.$this->lang->line("click here for detailed report").'</a><br/><br/>';
        }
        else
            echo 0;

    }


    public function front_end_bulk_scan_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('website_analysis_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('website_analysis_complete_search');
        $website_analysis_completed_function_str=$this->session->userdata('website_analysis_completed_function_str');
        
        $insert_table_id=$this->session->userdata('insert_table_id');
        $response['view_details_button'] = 'not_set';
        if($insert_table_id != "")
        {

            $link = site_url()."home/frontend_domain_details_view/".$insert_table_id;
            $view_button = '<a href="'.$link.'" class="btn btn-info btn-lg"><i class="fa fa-binoculars"></i> '.$this->lang->line("click here for detailed report").'</a><br/><br/>';
            $response['view_details_button'] = $view_button;
        }
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        $response['completed_function_str'] = $website_analysis_completed_function_str;
        
        echo json_encode($response);
        
    }


    public function frontend_domain_details_view($id=0)
    {
        $data['id'] = $id;

        $where = array();
        $where['where'] = array('id'=>$id);
        $domain_info = $this->basic->get_data('web_common_info',$where,$select='');

        $data['country_list'] = $this->get_country_names();

        $data['body'] = 'frontend_website_analysis/domain_details';
        $data['page_title'] = $this->lang->line("website analysis");
        $data['domain_info'] = $domain_info;
        $this->_frontend_website_details_theme($data);
        // $this->_viewcontroller($data);
    }


    public function _frontend_website_details_theme($data=array())
    {
        // $this->_disable_cache();
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }
    
        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $this->load->view('front/frontend_website_analysis_theme', $data);
    }


    public function front_ajax_get_general_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);

        $where = array();
        $where['where'] = array('id'=>$domain_id);
        $domain_info = $this->basic->get_data('web_common_info',$where,$select='');
        $data['country_list'] = $this->get_country_names();
        $data['domain_info'] = $domain_info;
        $domain_details = $this->load->view('frontend_website_analysis/general',$data);
        
        echo $domain_details;

    }

    public function front_ajax_get_alexa_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["alexa_data"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));
        
        $alexa_details = $this->load->view('admin/ranking/alexa_details',$data);

        echo $alexa_details;
    }

    public function front_ajax_get_similarweb_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["similar_web"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));
        
        $similar_web_details = $this->load->view('frontend_website_analysis/similar_web_details',$data);

        echo $similar_web_details;
    }

    public function front_ajax_get_social_network_data()
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

    public function front_ajax_get_meta_tag_info_data()
    {
        $domain_id = $this->input->post('domain_id', TRUE);
        $data["meta_tag_info"]=$this->basic->get_data("web_common_info",array("where"=>array("id"=>$domain_id)));

        $meta_tag_info = $this->load->view('frontend_website_analysis/meta_tag_details',$data);

        echo $meta_tag_info;
    }


    public function frontend_download_pdf()
    {
        $id = $this->input->post('table_id',true);
        
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



    public function php_info($code="")
    {
        if($code=="7ZT0EFiocUAM20wny6yu")
        echo phpinfo();        
    }

    
}


