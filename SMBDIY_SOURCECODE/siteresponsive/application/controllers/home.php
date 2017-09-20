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

        if (!file_exists(APPPATH.'install.txt'))
        {
            $this->load->database();
            $this->load->model('basic');
            
            $this->_time_zone_set();
            $this->load->library('upload');
            $this->upload_path = realpath(APPPATH . '../upload');
            $query = 'SET SESSION group_concat_max_len=9990000000000000000';
            $this->db->query($query);
            $query="SET SESSION sql_mode = ''";
            $this->db->query($query);  
            
            if(function_exists('ini_set'))
            ini_set('memory_limit', '-1');
        

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
        
        if (file_exists(APPPATH.'language/'.$this->language.'/common_lang.php'))
        $this->lang->load('common', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/message_lang.php'))
        $this->lang->load('message', $this->language);
        
        if (file_exists(APPPATH.'language/'.$this->language.'/admin_lang.php'))
        $this->lang->load('admin', $this->language);
        
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
                $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."';\n";
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
    public function index($base_site="")
    {
        // $this->login_page();
		$project_url=$this->config->item('project_url');
		$userid = get_cookie('user_id');
		if($userid==""){
		redirect($project_url.'/sitebuilder/public/userdashboard', 'refresh');
        }
		$data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
        $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
        $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
        //$data['bs']=array("base_site"=>$base_site);
        $this->_site_viewcontroller($data=array("base_site"=>$base_site));
    }
  public function compare($base_site="")
    {
        // $this->login_page();
        $project_url=$this->config->item('project_url');
		$userid = get_cookie('user_id');
		if($userid==""){
		redirect($project_url.'/sitebuilder/public/userdashboard', 'refresh');
        }
        $data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
        $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
        $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
        //$data['bs']=array("base_site"=>$base_site);
        $this->_compare_viewcontroller($data=array("base_site"=>$base_site));
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
      //  $this->load->view('admin/theme/theme', $data);
        $this->load->view('site/dashboard/theme', $data);
    }



    public function _site_viewcontroller($data=array())
    {
     //echo 'sv';exit; 
        if (!isset($data['page_title']))    $data['page_title']="Site Checkup";        
        if (!isset($data['body']))          $data['body']="site/index";
        if (!isset($data['load_css_js']))   $data['load_css_js']=0;

        if (!isset($data['base_site'])  || $data['base_site']=="") 
        {
            $data['base_site']=0;
            $data['compare']=0;
        }
        else $data['compare']=1;     

        $this->config->load("meta_config");
        if(!isset($data['seo_meta_description']))    $data['seo_meta_description']=$this->config->item("meta description");
        if(!isset($data['seo_meta_keyword']))        $data['seo_meta_keyword']=$this->config->item("meta keyword");   

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(2);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        //catcha for contact page
        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

        $this->load->helper('cookie');
        $userid = get_cookie('user_id');
        $username = get_cookie('emailid');
        //echo $userid;exit;
        $data['sites']=$this->basic->get_user_sites($userid);

        if($data['body']=="site/index")
        {
            $where_compare["comparision.user_id ="]=$userid;
            $where_single["user_id ="]=$userid;
            $where=array('where'=>$where_single);
            $where_compare=array('where'=>$where_compare);
            $this->load->helper('cookie');
            $data['username'] = get_cookie('name');
            $data["recent_search"]=$this->basic->get_data($table="site_check_report",$where,$select=array("id","domain_name","searched_at"),$join='',$limit='15',$start=NULL,$order_by='id DESC');
            $join=array('site_check_report as base_site_table'=>"base_site_table.id=comparision.base_site,left",'site_check_report as competutor_site_table'=>"competutor_site_table.id=comparision.competutor_site,left");
            $select=array("base_site_table.domain_name as base_domain","competutor_site_table.domain_name as competutor_domain","comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
            $data["recent_comparison"] = $this->basic->get_data("comparision",$where_compare,$select,$join,$limit='15',$start=NULL,$order_by='id DESC');
        }

        //$this->load->view('site/site_theme', $data);
		$this->load->view('site/dashboard/theme', $data);
    }

    public function _compare_viewcontroller($data=array())
    {
     //echo 'sv';exit; 
        if (!isset($data['page_title']))    $data['page_title']="Site Checkup";        
        if (!isset($data['body']))          $data['body']="site/compare";
        if (!isset($data['load_css_js']))   $data['load_css_js']=0;

        if (!isset($data['base_site'])  || $data['base_site']=="") 
        {
            $data['base_site']=0;
            $data['compare']=0;
        }
        else $data['compare']=1;     

        $this->config->load("meta_config");
        if(!isset($data['seo_meta_description']))    $data['seo_meta_description']=$this->config->item("meta description");
        if(!isset($data['seo_meta_keyword']))        $data['seo_meta_keyword']=$this->config->item("meta keyword");   

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(2);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        //catcha for contact page
        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

       // $this->load->helper('cookie');
        $userid = get_cookie('user_id');
        $username = get_cookie('emailid');
        //echo $userid;exit;
       // $data['sites']=$this->basic->get_user_sites($userid);

        if($data['body']=="site/compare")
        {
            $where_compare["comparision.user_id ="]=$userid;
            $where_single["user_id ="]=$userid;
            $where=array('where'=>$where_single);
            $where_compare=array('where'=>$where_compare);
           // $this->load->helper('cookie');
            $data['username'] = get_cookie('name');
            $data["recent_search"]=$this->basic->get_data($table="site_check_report",$where,$select=array("id","domain_name","searched_at"),$join='',$limit='15',$start=NULL,$order_by='id DESC');
            $join=array('site_check_report as base_site_table'=>"base_site_table.id=comparision.base_site,left",'site_check_report as competutor_site_table'=>"competutor_site_table.id=comparision.competutor_site,left");
            $select=array("base_site_table.domain_name as base_domain","competutor_site_table.domain_name as competutor_domain","comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
            $data["recent_comparison"] = $this->basic->get_data("comparision",$where_compare,$select,$join,$limit='15',$start=NULL,$order_by='id DESC');
        }

        //$this->load->view('site/site_theme', $data);
        $this->load->view('site/dashboard/theme', $data);
    }
    public function sitename()
    {
       $siteid=$_POST['website'];
        //$siteid=$this->uri->segment(2);
       $site=$this->basic->get_site_name($siteid);
       echo json_encode($site);
      }
    public function login_page()
    {
        if (file_exists(APPPATH.'install.txt')) {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
            redirect('domain_details_visitor/domain_details', 'location');
        }
                
        $this->load->view('page/login');
    }
    /*
    public function login() //loads home view page after login (this )
    {
        if (file_exists(APPPATH.'install.txt')) {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin') {
         redirect('domain_details_visitor/domain_details', 'location');
        }

        $this->form_validation->set_rules('username', '<b>'.$this->lang->line("email").'</b>', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('page/login');
        } else {
            $username = $this->input->post('username', true);
            $password = md5($this->input->post('password', true));

            $table = 'users';
            $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0", "status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);

            $count = $info['extra_index']['num_rows'];
            
            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect(uri_string());
            } else {
                $username = $info[0]['first_name'];
                $user_type = $info[0]['type'];
                $user_id = $info[0]['id'];

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'admin') {
                 redirect('domain_details_visitor/domain_details', 'location');
                }
            }
        }
    }
*/

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
            $url = "<a href='".site_url().'home/password_recovery'."'>".site_url().'home/password_recovery'."</a>";

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
function _mail_sender_report($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 0, $smtp = 1)
    {
       // echo 'mail'.$message;exit;
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
            $filename='report';
            $site_url=base_url();
            $attched_file = $_SERVER["DOCUMENT_ROOT"].'/siteresponsive/'.$message;
            //echo 'at'.$attched_file;exit;
           // echo 'loc'.$site_url.$message;exit;
            //$message='download/health_check_report_stripe.com_f178634e71eab88582e5704a3d7761922009480538.pdf';
            //echo 'loc'.$site_url.$message;exit;
            $this->email->attach($attched_file);
            $msg='Your website report (attached)';
            $this->email->message($msg);
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


 
    public function download_page_loader()
    {
        $this->load->view('page/download');
    }


    public function read_text_file()
    {
    	
        if ( isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0 && ($_FILES['file_upload']['type'] =='text/plain' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/comma-separated-values' || $_FILES['file_upload']['type']='text/x-comma-separated-values')) 
        {
        
            $ext=array_pop(explode('.', $_FILES['file_upload']['name']));          
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


    function _language_list() 
     {
        
         $language = array
         (
            "bengali"   =>array('country_code'=>'bd','label'=>'Bengali'),          
            "dutch"     =>array('country_code'=>'nl','label'=>'Dutch'),          
            "english"   =>array('country_code'=>'us','label'=>'English'),          
            "french"    =>array('country_code'=>'fr','label'=>'French'),          
            "german"    =>array('country_code'=>'de','label'=>'German'),          
            "greek"     =>array('country_code'=>'gr','label'=>'Greek'),          
            "italian"   =>array('country_code'=>'it','label'=>'Italian'),          
            "portuguese"=>array('country_code'=>'br','label'=>'Portuguese'),          
            "russian"   =>array('country_code'=>'ru','label'=>'Russian'),          
            "spanish"   =>array('country_code'=>'es','label'=>'Spanish'),
            "turkish"   =>array('country_code'=>'tr','label'=>'Turkish')
         );
         return $language;
     }

     public function language_changer()
    {
        $language=$this->input->post("language");
        $this->session->set_userdata("selected_language",$language);
    }




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



    function get_general_content($url,$proxy="")
    {        
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


    

    public function code_activation_check_action($purchase_code,$only_domain)
    {

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

    public function periodic_check()
    {

        $today= date('d');

        if($today%7==0)
        {

          if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt'))
          {
                $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
                $config_decoded_content = json_decode($config_existing_content, true);
                $last_check_date= $config_decoded_content['checking_date'];
                $purchase_code  = $config_decoded_content['purchase_code'];
                $base_url = base_url();
                $domain_name    = get_domain_only($base_url);

                if( strtotime(date('Y-m-d')) != strtotime($last_check_date))
                $this->code_activation_check_action($purchase_code,$domain_name);     
               
            }
        }
    }





    public function search_action()
    {
        
       $this->load->library("google");
       $this->load->library("site_check");

       $domain=trim($this->input->post('website', true));
       if($domain=="")
       {
          $response=array("status"=>"0");
          echo json_encode($response);
          exit();
       }
   

       $domain=addHttp($domain);
       $base_site=trim($this->input->post('base_site', true));
       $compare=trim($this->input->post('compare', true));

       $download_id=$this->session->userdata('download_id_front');
        
       $this->session->set_userdata('health_check_total',100);
       $this->session->set_userdata('health_check_count',0);
      
       $insert=array();

       // site check starts
       $site_stat=$this->site_check->site_statistic_check($domain);
       foreach ($site_stat as $key => $value) 
       {
           $insert[$key]= is_array($value) ? json_encode($value) : $value;
       }
       // end of site check


       //desktop starts
       $desktop_result=$this->google->google_page_speed_insight($domain,"desktop");

       $insert["response_code"] = isset($desktop_result["responseCode"]) ? $desktop_result["responseCode"] : "";
   
       $insert["speed_score"]  = isset($desktop_result["ruleGroups"]["SPEED"]["score"]) ? $desktop_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $pagestat = isset($desktop_result["pageStats"]) ? $desktop_result["pageStats"] : array();
       $insert["pagestat"] = json_encode($pagestat);
       
       $avoid_landing_page_redirects = array();
       $avoid_landing_page_redirects["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects["redirect_count"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects"]=json_encode($avoid_landing_page_redirects);

       $gzip_compression = array();
       $gzip_compression["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression["total_size_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression["total_percentage_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression"]=json_encode($gzip_compression);

       $leverage_browser_caching = array();
       $leverage_browser_caching["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching"]=json_encode($leverage_browser_caching);

       $main_resource_server_response_time = array();
       $main_resource_server_response_time["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time["response_time"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time"]=json_encode($main_resource_server_response_time);

       $minify_css = array();
       $minify_css["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css"]=json_encode($minify_css);

       $minify_html = array();
       $minify_html["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html"]=json_encode($minify_html);

       $minify_javaScript = array();
       $minify_javaScript["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript"]=json_encode($minify_javaScript);

       $minimize_render_blocking_resources = array();
       $minimize_render_blocking_resources["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources["js_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources["css_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources"]=json_encode($minimize_render_blocking_resources);

       $optimize_images = array();
       $optimize_images["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images["total_size_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images["total_percentage_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images"]=json_encode($optimize_images);

       $prioritize_visible_content = array();
       $prioritize_visible_content["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content"]=json_encode($prioritize_visible_content);

       $step_count=$this->session->userdata('health_check_count');
       if($step_count=="") $step_count=0;
       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of desktop


       
       // mobile starts
       $mobile_result=$this->google->google_page_speed_insight($domain,"mobile");
       $insert["response_code_mobile"] = isset($mobile_result["responseCode"]) ? $mobile_result["responseCode"] : "";
       
       $insert["speed_score_mobile"]   = isset($mobile_result["ruleGroups"]["SPEED"]["score"]) ? $mobile_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $insert["speed_usability_mobile"]   = isset($mobile_result["ruleGroups"]["USABILITY"]["score"]) ? $mobile_result["ruleGroups"]["USABILITY"]["score"] : "";
       
       $pagestat_mobile     = isset($mobile_result["pageStats"]) ? $mobile_result["pageStats"] : array();
       $insert["pagestat_mobile"]     = json_encode($pagestat_mobile);
       
       $avoid_interstitials_mobile = array();
       $avoid_interstitials_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"] : 0;
       $avoid_interstitials_mobile["app_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_interstitials_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_interstitials_mobile"]=json_encode($avoid_interstitials_mobile);

       $avoid_plugins_mobile = array();
       $avoid_plugins_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"] : 0;
       $avoid_plugins_mobile["plugin_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_plugins_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_plugins_mobile"]=json_encode($avoid_plugins_mobile);

       $configure_viewport_mobile = array();
       $configure_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"] : 0;
       $insert["configure_viewport_mobile"]=json_encode($configure_viewport_mobile);

       $size_content_to_viewport_mobile = array();
       $size_content_to_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"] : 0;
       $size_content_to_viewport_mobile["content_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $size_content_to_viewport_mobile["viewport_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $size_content_to_viewport_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_content_to_viewport_mobile"]=json_encode($size_content_to_viewport_mobile);

       $size_tap_targets_appropriately_mobile = array();
       $size_tap_targets_appropriately_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"] : 0;
       $size_tap_targets_appropriately_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_tap_targets_appropriately_mobile"]=json_encode($size_tap_targets_appropriately_mobile);

       $use_legible_font_sizes_mobile = array();
       $use_legible_font_sizes_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"] : 0;
       $use_legible_font_sizes_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"] : array();
       $insert["use_legible_font_sizes_mobile"]=json_encode($use_legible_font_sizes_mobile);

       $avoid_landing_page_redirects_mobile = array();
       $avoid_landing_page_redirects_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects_mobile["redirect_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects_mobile"]=json_encode($avoid_landing_page_redirects_mobile);

       $gzip_compression_mobile = array();
       $gzip_compression_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression_mobile["total_size_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression_mobile["total_percentage_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression_mobile"]=json_encode($gzip_compression_mobile);

       $leverage_browser_caching_mobile = array();
       $leverage_browser_caching_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching_mobile"]=json_encode($leverage_browser_caching_mobile);

       $main_resource_server_response_time_mobile = array();
       $main_resource_server_response_time_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time_mobile["response_time"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time_mobile"]=json_encode($main_resource_server_response_time_mobile);

       $minify_css_mobile = array();
       $minify_css_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css_mobile"]=json_encode($minify_css_mobile);

       $minify_html_mobile = array();
       $minify_html_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html_mobile"]=json_encode($minify_html_mobile);

       $minify_javaScript_mobile = array();
       $minify_javaScript_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript_mobile"]=json_encode($minify_javaScript_mobile);

       $minimize_render_blocking_resources_mobile = array();
       $minimize_render_blocking_resources_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources_mobile["js_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources_mobile["css_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources_mobile"]=json_encode($minimize_render_blocking_resources_mobile);

       $optimize_images_mobile = array();
       $optimize_images_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images_mobile["total_size_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images_mobile["total_percentage_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images_mobile"]=json_encode($optimize_images_mobile);

       $prioritize_visible_content_mobile = array();
       $prioritize_visible_content_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content_mobile"]=json_encode($prioritize_visible_content_mobile);

       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of mobile

       $insert["mobile_ready_data"] = $this->google->mobile_ready($domain);
       $step_count+=30;
       $this->session->set_userdata('health_check_count',$step_count);

       $insert["domain_name"]=$domain;

    $userid = get_cookie('user_id');
    $insert["user_id"]=$userid;
       $insert["searched_at"]=date("Y-m-d H:i:s");
       $this->basic->insert_data("site_check_report",$insert);
       $last_id=$this->db->insert_id();
       $step_count++;
       $this->session->set_userdata('health_check_count',$step_count);

       if($compare==1)
       {
            $insert=array();
        $insert["user_id"]=$userid;
            $insert["searched_at"]=date("Y-m-d H:i:s");
            $insert["base_site"]=$base_site;
            $insert["competutor_site"]=$last_id;
            $this->basic->insert_data("comparision",$insert);
            $comparision_id=$this->db->insert_id();
            $details_url=site_url('health_check/comparison_report'."/".$comparision_id);
       }
       else $details_url=site_url('health_check/report'."/".$last_id.'/'.$this->site_check->clean_domain_name($domain));
          
       $response=array("status"=>"1","details_url"=>$details_url);
       echo json_encode($response);

    }
    public function search_action_seo()
    {
        
       $this->load->library("google");
       $this->load->library("site_check");

       $domain=trim($this->input->post('website', true));
       if($domain=="")
       {
          $response=array("status"=>"0");
          echo json_encode($response);
          exit();
       }
   

       $domain=addHttp($domain);
       $base_site=trim($this->input->post('base_site', true));
       $compare=trim($this->input->post('compare', true));

       $download_id=$this->session->userdata('download_id_front');
        
       $this->session->set_userdata('health_check_total',100);
       $this->session->set_userdata('health_check_count',0);
      
       $insert=array();

       // site check starts
       $site_stat=$this->site_check->site_statistic_check($domain);
       foreach ($site_stat as $key => $value) 
       {
           $insert[$key]= is_array($value) ? json_encode($value) : $value;
       }
       // end of site check


       //desktop starts
       $desktop_result=$this->google->google_page_speed_insight($domain,"desktop");

       $insert["response_code"] = isset($desktop_result["responseCode"]) ? $desktop_result["responseCode"] : "";
   
       $insert["speed_score"]  = isset($desktop_result["ruleGroups"]["SPEED"]["score"]) ? $desktop_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $pagestat = isset($desktop_result["pageStats"]) ? $desktop_result["pageStats"] : array();
       $insert["pagestat"] = json_encode($pagestat);
       
       $avoid_landing_page_redirects = array();
       $avoid_landing_page_redirects["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects["redirect_count"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects"]=json_encode($avoid_landing_page_redirects);

       $gzip_compression = array();
       $gzip_compression["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression["total_size_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression["total_percentage_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression"]=json_encode($gzip_compression);

       $leverage_browser_caching = array();
       $leverage_browser_caching["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching"]=json_encode($leverage_browser_caching);

       $main_resource_server_response_time = array();
       $main_resource_server_response_time["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time["response_time"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time"]=json_encode($main_resource_server_response_time);

       $minify_css = array();
       $minify_css["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css"]=json_encode($minify_css);

       $minify_html = array();
       $minify_html["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html"]=json_encode($minify_html);

       $minify_javaScript = array();
       $minify_javaScript["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript"]=json_encode($minify_javaScript);

       $minimize_render_blocking_resources = array();
       $minimize_render_blocking_resources["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources["js_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources["css_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources"]=json_encode($minimize_render_blocking_resources);

       $optimize_images = array();
       $optimize_images["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images["total_size_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images["total_percentage_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images"]=json_encode($optimize_images);

       $prioritize_visible_content = array();
       $prioritize_visible_content["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content"]=json_encode($prioritize_visible_content);

       $step_count=$this->session->userdata('health_check_count');
       if($step_count=="") $step_count=0;
       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of desktop


       
       // mobile starts
       $mobile_result=$this->google->google_page_speed_insight($domain,"mobile");
       $insert["response_code_mobile"] = isset($mobile_result["responseCode"]) ? $mobile_result["responseCode"] : "";
       
       $insert["speed_score_mobile"]   = isset($mobile_result["ruleGroups"]["SPEED"]["score"]) ? $mobile_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $insert["speed_usability_mobile"]   = isset($mobile_result["ruleGroups"]["USABILITY"]["score"]) ? $mobile_result["ruleGroups"]["USABILITY"]["score"] : "";
       
       $pagestat_mobile     = isset($mobile_result["pageStats"]) ? $mobile_result["pageStats"] : array();
       $insert["pagestat_mobile"]     = json_encode($pagestat_mobile);
       
       $avoid_interstitials_mobile = array();
       $avoid_interstitials_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"] : 0;
       $avoid_interstitials_mobile["app_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_interstitials_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_interstitials_mobile"]=json_encode($avoid_interstitials_mobile);

       $avoid_plugins_mobile = array();
       $avoid_plugins_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"] : 0;
       $avoid_plugins_mobile["plugin_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_plugins_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_plugins_mobile"]=json_encode($avoid_plugins_mobile);

       $configure_viewport_mobile = array();
       $configure_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"] : 0;
       $insert["configure_viewport_mobile"]=json_encode($configure_viewport_mobile);

       $size_content_to_viewport_mobile = array();
       $size_content_to_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"] : 0;
       $size_content_to_viewport_mobile["content_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $size_content_to_viewport_mobile["viewport_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $size_content_to_viewport_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_content_to_viewport_mobile"]=json_encode($size_content_to_viewport_mobile);

       $size_tap_targets_appropriately_mobile = array();
       $size_tap_targets_appropriately_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"] : 0;
       $size_tap_targets_appropriately_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_tap_targets_appropriately_mobile"]=json_encode($size_tap_targets_appropriately_mobile);

       $use_legible_font_sizes_mobile = array();
       $use_legible_font_sizes_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"] : 0;
       $use_legible_font_sizes_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"] : array();
       $insert["use_legible_font_sizes_mobile"]=json_encode($use_legible_font_sizes_mobile);

       $avoid_landing_page_redirects_mobile = array();
       $avoid_landing_page_redirects_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects_mobile["redirect_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects_mobile"]=json_encode($avoid_landing_page_redirects_mobile);

       $gzip_compression_mobile = array();
       $gzip_compression_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression_mobile["total_size_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression_mobile["total_percentage_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression_mobile"]=json_encode($gzip_compression_mobile);

       $leverage_browser_caching_mobile = array();
       $leverage_browser_caching_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching_mobile"]=json_encode($leverage_browser_caching_mobile);

       $main_resource_server_response_time_mobile = array();
       $main_resource_server_response_time_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time_mobile["response_time"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time_mobile"]=json_encode($main_resource_server_response_time_mobile);

       $minify_css_mobile = array();
       $minify_css_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css_mobile"]=json_encode($minify_css_mobile);

       $minify_html_mobile = array();
       $minify_html_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html_mobile"]=json_encode($minify_html_mobile);

       $minify_javaScript_mobile = array();
       $minify_javaScript_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript_mobile"]=json_encode($minify_javaScript_mobile);

       $minimize_render_blocking_resources_mobile = array();
       $minimize_render_blocking_resources_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources_mobile["js_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources_mobile["css_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources_mobile"]=json_encode($minimize_render_blocking_resources_mobile);

       $optimize_images_mobile = array();
       $optimize_images_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images_mobile["total_size_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images_mobile["total_percentage_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images_mobile"]=json_encode($optimize_images_mobile);

       $prioritize_visible_content_mobile = array();
       $prioritize_visible_content_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content_mobile"]=json_encode($prioritize_visible_content_mobile);

       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of mobile

       $insert["mobile_ready_data"] = $this->google->mobile_ready($domain);
       $step_count+=30;
       $this->session->set_userdata('health_check_count',$step_count);

       $insert["domain_name"]=$domain;
       $insert["searched_at"]=date("Y-m-d H:i:s");
       $this->basic->insert_data("site_check_report",$insert);
       $last_id=$this->db->insert_id();
       $step_count++;
       $this->session->set_userdata('health_check_count',$step_count);

       if($compare==1)
       {
            $insert=array();
            $insert["searched_at"]=date("Y-m-d H:i:s");
            $insert["base_site"]=$base_site;
            $insert["competutor_site"]=$last_id;
            $this->basic->insert_data("comparision",$insert);
            $comparision_id=$this->db->insert_id();
            $details_url=site_url('health_check/comparison_report'."/".$comparision_id);
       }
       else $details_url=site_url('health_check/report_seo'."/".$last_id.'/'.$this->site_check->clean_domain_name($domain));
          
       $response=array("status"=>"1","details_url"=>$details_url);
       echo json_encode($response);

    }
public function search_action_responsive()
    {
        
       $this->load->library("google");
       $this->load->library("site_check");

       $domain=trim($this->input->post('website', true));
       if($domain=="")
       {
          $response=array("status"=>"0");
          echo json_encode($response);
          exit();
       }
   

       $domain=addHttp($domain);
       $base_site=trim($this->input->post('base_site', true));
       $compare=trim($this->input->post('compare', true));

       $download_id=$this->session->userdata('download_id_front');
        
       $this->session->set_userdata('health_check_total',100);
       $this->session->set_userdata('health_check_count',0);
      
       $insert=array();

       // site check starts
       $site_stat=$this->site_check->site_statistic_check($domain);
       foreach ($site_stat as $key => $value) 
       {
           $insert[$key]= is_array($value) ? json_encode($value) : $value;
       }
       // end of site check


       //desktop starts
       $desktop_result=$this->google->google_page_speed_insight($domain,"desktop");

       $insert["response_code"] = isset($desktop_result["responseCode"]) ? $desktop_result["responseCode"] : "";
   
       $insert["speed_score"]  = isset($desktop_result["ruleGroups"]["SPEED"]["score"]) ? $desktop_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $pagestat = isset($desktop_result["pageStats"]) ? $desktop_result["pageStats"] : array();
       $insert["pagestat"] = json_encode($pagestat);
       
       $avoid_landing_page_redirects = array();
       $avoid_landing_page_redirects["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects["redirect_count"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects"]=json_encode($avoid_landing_page_redirects);

       $gzip_compression = array();
       $gzip_compression["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression["total_size_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression["total_percentage_compressable"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression"]=json_encode($gzip_compression);

       $leverage_browser_caching = array();
       $leverage_browser_caching["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching"]=json_encode($leverage_browser_caching);

       $main_resource_server_response_time = array();
       $main_resource_server_response_time["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time["response_time"] = isset($desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time"]=json_encode($main_resource_server_response_time);

       $minify_css = array();
       $minify_css["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css"]=json_encode($minify_css);

       $minify_html = array();
       $minify_html["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html"]=json_encode($minify_html);

       $minify_javaScript = array();
       $minify_javaScript["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript["total_size_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript["total_percentage_minifiable"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript"]=json_encode($minify_javaScript);

       $minimize_render_blocking_resources = array();
       $minimize_render_blocking_resources["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources["js_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources["css_urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources"]=json_encode($minimize_render_blocking_resources);

       $optimize_images = array();
       $optimize_images["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images["total_size_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images["total_percentage_optimizable"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images["urls"] = isset($desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $desktop_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images"]=json_encode($optimize_images);

       $prioritize_visible_content = array();
       $prioritize_visible_content["rule_impact"] = isset($desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $desktop_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content"]=json_encode($prioritize_visible_content);

       $step_count=$this->session->userdata('health_check_count');
       if($step_count=="") $step_count=0;
       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of desktop


       
       // mobile starts
       $mobile_result=$this->google->google_page_speed_insight($domain,"mobile");
       $insert["response_code_mobile"] = isset($mobile_result["responseCode"]) ? $mobile_result["responseCode"] : "";
       
       $insert["speed_score_mobile"]   = isset($mobile_result["ruleGroups"]["SPEED"]["score"]) ? $mobile_result["ruleGroups"]["SPEED"]["score"] : "";
       
       $insert["speed_usability_mobile"]   = isset($mobile_result["ruleGroups"]["USABILITY"]["score"]) ? $mobile_result["ruleGroups"]["USABILITY"]["score"] : "";
       
       $pagestat_mobile     = isset($mobile_result["pageStats"]) ? $mobile_result["pageStats"] : array();
       $insert["pagestat_mobile"]     = json_encode($pagestat_mobile);
       
       $avoid_interstitials_mobile = array();
       $avoid_interstitials_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"] : 0;
       $avoid_interstitials_mobile["app_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_interstitials_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidInterstitials"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_interstitials_mobile"]=json_encode($avoid_interstitials_mobile);

       $avoid_plugins_mobile = array();
       $avoid_plugins_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"] : 0;
       $avoid_plugins_mobile["plugin_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_plugins_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidPlugins"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_plugins_mobile"]=json_encode($avoid_plugins_mobile);

       $configure_viewport_mobile = array();
       $configure_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"] : 0;
       $insert["configure_viewport_mobile"]=json_encode($configure_viewport_mobile);

       $size_content_to_viewport_mobile = array();
       $size_content_to_viewport_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"] : 0;
       $size_content_to_viewport_mobile["content_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $size_content_to_viewport_mobile["viewport_width"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $size_content_to_viewport_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeContentToViewport"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_content_to_viewport_mobile"]=json_encode($size_content_to_viewport_mobile);

       $size_tap_targets_appropriately_mobile = array();
       $size_tap_targets_appropriately_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"] : 0;
       $size_tap_targets_appropriately_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["urlBlocks"]["0"]["urls"] : array();
       $insert["size_tap_targets_appropriately_mobile"]=json_encode($size_tap_targets_appropriately_mobile);

       $use_legible_font_sizes_mobile = array();
       $use_legible_font_sizes_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"] : 0;
       $use_legible_font_sizes_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["urlBlocks"]["0"]["urls"] : array();
       $insert["use_legible_font_sizes_mobile"]=json_encode($use_legible_font_sizes_mobile);

       $avoid_landing_page_redirects_mobile = array();
       $avoid_landing_page_redirects_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["ruleImpact"] : 0;
       $avoid_landing_page_redirects_mobile["redirect_count"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["summary"]["args"]["0"]["value"] : 0;
       $avoid_landing_page_redirects_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["AvoidLandingPageRedirects"]["urlBlocks"]["0"]["urls"] : array();
       $insert["avoid_landing_page_redirects_mobile"]=json_encode($avoid_landing_page_redirects_mobile);

       $gzip_compression_mobile = array();
       $gzip_compression_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["ruleImpact"] : 0;
       $gzip_compression_mobile["total_size_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $gzip_compression_mobile["total_percentage_compressable"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $gzip_compression_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["EnableGzipCompression"]["urlBlocks"]["0"]["urls"] : array();
       $insert["gzip_compression_mobile"]=json_encode($gzip_compression_mobile);

       $leverage_browser_caching_mobile = array();
       $leverage_browser_caching_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["ruleImpact"] : 0;
       $leverage_browser_caching_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["LeverageBrowserCaching"]["urlBlocks"]["0"]["urls"] : array();
       $insert["leverage_browser_caching_mobile"]=json_encode($leverage_browser_caching_mobile);

       $main_resource_server_response_time_mobile = array();
       $main_resource_server_response_time_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["ruleImpact"] : 0;
       $main_resource_server_response_time_mobile["response_time"] = isset($mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MainResourceServerResponseTime"]["urlBlocks"]["0"]["header"]["args"]["0"]["value"] : "";
       $insert["main_resource_server_response_time_mobile"]=json_encode($main_resource_server_response_time_mobile);

       $minify_css_mobile = array();
       $minify_css_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["ruleImpact"] : 0;
       $minify_css_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_css_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_css_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyCss"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_css_mobile"]=json_encode($minify_css_mobile);

       $minify_html_mobile = array();
       $minify_html_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["ruleImpact"] : 0;
       $minify_html_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_html_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_html_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyHTML"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_html_mobile"]=json_encode($minify_html_mobile);

       $minify_javaScript_mobile = array();
       $minify_javaScript_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["ruleImpact"] : 0;
       $minify_javaScript_mobile["total_size_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $minify_javaScript_mobile["total_percentage_minifiable"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $minify_javaScript_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinifyJavaScript"]["urlBlocks"]["0"]["urls"] : array();
       $insert["minify_javaScript_mobile"]=json_encode($minify_javaScript_mobile);

       $minimize_render_blocking_resources_mobile = array();
       $minimize_render_blocking_resources_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["ruleImpact"] : 0;
       $minimize_render_blocking_resources_mobile["js_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["1"]["urls"] : array();
       $minimize_render_blocking_resources_mobile["css_urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["MinimizeRenderBlockingResources"]["urlBlocks"]["2"]["urls"] : array();
       $insert["minimize_render_blocking_resources_mobile"]=json_encode($minimize_render_blocking_resources_mobile);

       $optimize_images_mobile = array();
       $optimize_images_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["ruleImpact"] : 0;
       $optimize_images_mobile["total_size_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["1"]["value"] : "";
       $optimize_images_mobile["total_percentage_optimizable"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["header"]["args"]["2"]["value"] : "";
       $optimize_images_mobile["urls"] = isset($mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"]) ? $mobile_result["formattedResults"]["ruleResults"]["OptimizeImages"]["urlBlocks"]["0"]["urls"] : array();
       $insert["optimize_images_mobile"]=json_encode($optimize_images_mobile);

       $prioritize_visible_content_mobile = array();
       $prioritize_visible_content_mobile["rule_impact"] = isset($mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"]) ? $mobile_result["formattedResults"]["ruleResults"]["PrioritizeVisibleContent"]["ruleImpact"] : 0;
       $insert["prioritize_visible_content_mobile"]=json_encode($prioritize_visible_content_mobile);

       $step_count+=16;
       $this->session->set_userdata('health_check_count',$step_count);
       // end of mobile

       $insert["mobile_ready_data"] = $this->google->mobile_ready($domain);
       $step_count+=30;
       $this->session->set_userdata('health_check_count',$step_count);

       $insert["domain_name"]=$domain;
       $insert["searched_at"]=date("Y-m-d H:i:s");
       $this->basic->insert_data("site_check_report",$insert);
       $last_id=$this->db->insert_id();
       $step_count++;
       $this->session->set_userdata('health_check_count',$step_count);

       if($compare==1)
       {
            $insert=array();
            $insert["searched_at"]=date("Y-m-d H:i:s");
            $insert["base_site"]=$base_site;
            $insert["competutor_site"]=$last_id;
            $this->basic->insert_data("comparision",$insert);
            $comparision_id=$this->db->insert_id();
            $details_url=site_url('health_check/comparison_report'."/".$comparision_id);
       }
       else $details_url=site_url('health_check/report_responsive'."/".$last_id.'/'.$this->site_check->clean_domain_name($domain));
          
       $response=array("status"=>"1","details_url"=>$details_url);
       echo json_encode($response);

    }
    public function progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('health_check_total'); 
        $bulk_complete_search=$this->session->userdata('health_check_count');
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;       
        echo json_encode($response);        
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
          'ZW' => 'ZIMBABWE'
        );
        return $array_countries;
    }

function havesite($base_site=""){
   // $data['body'] = 'have_site/page_details';
    $data['page_title'] = 'Have a Website';
	$project_url=$this->config->item('project_url');
    $data['topmenu']=array('Home' => $project_url,'About Us' => $project_url.'/#aboutus','Products' => $project_url.'/#product','Resources' => $project_url.'/#resource','Blog' => $project_url.'/blog');
    $data['bmenu1']=array('Account' => $project_url.'/#','Support' => $project_url.'/#','Product Catalog' => $project_url.'/#');
    $data['bmenu2']= array('Sitemap' => $project_url.'/#','Find Domain' => $project_url.'/#','Whois Search' => $project_url.'/#');
    $data['bs']=array("base_site"=>$base_site);
    $this->_havesite_viewcontroller($data);    


}
public function _havesite_viewcontroller($data=array())
    {
     //echo 's';exit; 
        if (!isset($data['page_title']))    $data['page_title']="";        
        if (!isset($data['body']))          $data['body']="have_site/index";
        if (!isset($data['load_css_js']))   $data['load_css_js']=0;

        if (!isset($data['base_site'])  || $data['base_site']=="") 
        {
            $data['base_site']=0;
            $data['compare']=0;
        }
        else $data['compare']=1;     

        $this->config->load("meta_config");
        if(!isset($data['seo_meta_description']))    $data['seo_meta_description']=$this->config->item("meta description");
        if(!isset($data['seo_meta_keyword']))        $data['seo_meta_keyword']=$this->config->item("meta keyword");   

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(2);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        //catcha for contact page
        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

        if($data['body']=="have_site/index")
        {
            $data["recent_search"]=$this->basic->get_data($table="site_check_report",$where='',$select=array("id","domain_name","searched_at"),$join='',$limit='15',$start=NULL,$order_by='id DESC');

            $join=array('site_check_report as base_site_table'=>"base_site_table.id=comparision.base_site,left",'site_check_report as competutor_site_table'=>"competutor_site_table.id=comparision.competutor_site,left");
            $select=array("base_site_table.domain_name as base_domain","competutor_site_table.domain_name as competutor_domain","comparision.base_site","comparision.competutor_site","comparision.searched_at","comparision.id as id");
            $data["recent_comparison"] = $this->basic->get_data("comparision",$where='',$select,$join,$limit='15',$start=NULL,$order_by='id DESC');
        }

        $this->load->view('have_site/site_theme', $data);
    }
   
}