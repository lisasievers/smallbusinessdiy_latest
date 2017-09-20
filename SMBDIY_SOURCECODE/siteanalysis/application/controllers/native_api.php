<?php
require_once("home.php");

class native_api extends Home
{
    public $user_id;
    
    public function __construct()
    {
        parent::__construct();   
        $this->user_id=$this->session->userdata("user_id");    
        $this->load->library('Web_common_report');

        // $this->important_feature();
    }


    public function api_member_validity($user_id='')
    {
        if($user_id!='') {
            $where['where'] = array('id'=>$user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$user_id)),$select="package.price as price, users.user_type",$join=array('package'=>"users.package_id=package.id,left"));

            if(is_array($package_data) && array_key_exists(0, $package_data) && $package_data[0]['user_type'] == 'Admin' )
                return true;

            $price = '';
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;

            
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            return false;
            else return true;
            

        }
    }


    public function index()
    {
       $this->get_api();
    }

    public function _api_key_generator()
    {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(15,$this->module_access))
        redirect('home/login_page', 'location');

        $this->member_validity();
        $val=$this->session->userdata("user_id")."-".substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 7 ).time()
        .substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789') , 0 , 7 );
        return $val;
    }

    public function get_api()
    {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(15,$this->module_access))
        redirect('home/login_page', 'location');

        $this->member_validity();

        $data['body'] = "api/native_api";
        $data['page_title'] = 'API';
        $api_data=$this->basic->get_data("native_api",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $data["api_key"]="";
        if(count($api_data)>0) $data["api_key"]=$api_data[0]["api_key"];
        $this->_viewcontroller($data);
    }

    public function get_api_action()
    { 
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login', 'location');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(15,$this->module_access))
        redirect('home/login_page', 'location');

        $api_key=$this->_api_key_generator(); 
        if($this->basic->is_exist("native_api",array("api_key"=>$api_key)))
        $this->get_api_action();

        $user_id=$this->session->userdata("user_id");        
        if($this->basic->is_exist("native_api",array("user_id"=>$user_id)))
        $this->basic->update_data("native_api",array("user_id"=>$user_id),array("api_key"=>$api_key));
        else $this->basic->insert_data("native_api",array("api_key"=>$api_key,"user_id"=>$user_id));
            
        redirect('native_api/get_api', 'location');
    }


 

    public function get_content_overview_data()
    {

        $return=array("status"=>"unknown","details"=>"unknown","content_overview_data"=>"unknown");

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain_code=$_GET["domain_code"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain_code=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("domain",array("user_id"=>$user_id,"domain_code"=>$domain_code)))
        {
            $return["status"]="0";           
            $return["details"]="This domain is not associated with this user.";
            $return["content_overview_data"]="";
            echo json_encode($return);
            exit();
        } else {
            $where['where'] = array('user_id'=>$user_id,'domain_code'=>$domain_code);
            $select = array('table_name');
            $table_name = $this->basic->get_data("domain",$where,$select);

            $select = array();
            $select = array("count(id) as total_view_for_this_url","visit_url");
            $content_overview_data = $this->basic->get_data($table_name[0]['table_name'],$where='',$select,$join='',$limit='',$start=NULL,$order_by='total_view_for_this_url desc',$group_by='visit_url');
            $total_view = 0;
            foreach($content_overview_data as $value){
                $total_view = $total_view+$value['total_view_for_this_url'];
            }

            $data['total_view_for_this_domain'] = $total_view;
            $data['content_overview_data'] = $content_overview_data;

            // insert data to useges log table
            $this->_insert_usage_log($module_id=15,$request=1,$user_id);

            echo json_encode($data);

        }

    }


    public function get_overview_data()
    {
        $return=array("status"=>"unknown","details"=>"unknown","overview_data"=>"unknown");

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain_code=$_GET["domain_code"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain_code=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("domain",array("user_id"=>$user_id,"domain_code"=>$domain_code)))
        {
            $return["status"]="0";           
            $return["details"]="This domain is not associated with this user.";
            $return["overview_data"]="";
            echo json_encode($return);
            exit();
        } else {
            $where['where'] = array('user_id'=>$user_id,'domain_code'=>$domain_code);
            $select = array('table_name');
            $table_name = $this->basic->get_data("domain",$where,$select);
            $table = $table_name[0]['table_name'];

            $total_page_view = $this->basic->get_data($table,$where='',$select='');
            $total_unique_visitor = $this->basic->get_data($table,$where='',$select='',$join='',$limit='',$start='',$order_by='',$group_by='cookie_value');
            
            $select = array();
            $select = array("count(id) as session_number","last_scroll_time","last_engagement_time");
            $total_unique_session = $this->basic->get_data($table,$where='',$select,$join='',$limit='',$start='',$order_by='',$group_by='session_value');
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
            $select = array();
            $select = array(
                "date_time as stay_from",
                "last_engagement_time",
                "last_scroll_time"
                );
            $stay_time_info = $this->basic->get_data($table,$where='',$select,$join='',$limit='',$start='',$order_by='',$group_by='');
            
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

            // insert data to useges log table
            $this->_insert_usage_log($module_id=15,$request=1,$user_id);
            
            echo json_encode($data);

        }

    }

    /* http://konok-pc/xeroneit/web_analytics/native_api/dmoz_check?api_key=1h4Kxo791458648346BqAKuWC&domain=http://www.xeroneit.net */
    /*status,details,listed_or_not*/
    public function dmoz_check()
    { 
        $return=array("status"=>"unknown","details"=>"unknown","listed_or_not"=>"unknown");

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        }


        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//


        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["listed_or_not"]="";
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);

        $dmoz_data=$this->web_common_report->dmoz_check($domain);  
        $return["status"]="1";           
        $return["details"]="Success";
        $return["listed_or_not"]=$dmoz_data;      


        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);   
        
        echo json_encode($return);    
    }


// Section for Facebook ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/facebook_ckeck?api_key=1-tzScXkU1458725792PTfaDyY&domain=http://www.xeroneit.net */
    /*status,details,total_share,total_like,total_comment*/ 

    public function facebook_ckeck()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return['total_share'] = "" ;     
            $return['total_like'] = "" ;     
            $return['total_comment'] = "";
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);

        $facebook_data=$this->web_common_report->fb_like_comment_share($domain);  
        $return["status"]="1";           
        $return["details"]="Success";
        $facebook_result=$facebook_data;

        $return['total_share'] =  $facebook_result['total_share'];     
        $return['total_like'] =  $facebook_result['total_like'];     
        $return['total_comment'] =  $facebook_result['total_comment'];     
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 

        echo json_encode($return);

    }



    // Section for Google+ ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/google_plus_ckeck?api_key=1-tzScXkU1458725792PTfaDyY&domain=http://www.xeroneit.net */
    /*status,details,google_plus_count*/ 

    public function google_plus_ckeck()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["google_plus_count"]='';
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $return["google_plus_count"]=$this->web_common_report->get_plusones($domain);

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo json_encode($return);

    }




     // Section for Linkedin ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/linkedin_check?api_key=1-tzScXkU1458725792PTfaDyY&domain=http://www.xeroneit.net */
    /*status,details,total_share*/ 

    public function linkedin_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $return["total_share"]=$this->web_common_report->linkdin_share($domain);   
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        echo json_encode($return);

    }




        // Section for Xing ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/xing_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,total_share*/ 

    public function xing_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_share"]='';
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $return["total_share"]=$this->web_common_report->xing_share_count($domain);   
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        echo json_encode($return);

    }






 // Section for Reddit ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/reddit_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,score,downs,ups*/ 

    public function reddit_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["score"]="";  
            $return["downs"]="";  
            $return["ups"]="";
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $reddit_result=$this->web_common_report->reddit_count($domain); 

        $return["score"]=$reddit_result['score'];  
        $return["downs"]=$reddit_result['downs'];  
        $return["ups"]=$reddit_result['ups'];  
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        echo json_encode($return);

    }





 // Section for Pinterest ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/pinterest_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,pinterest_pin*/ 

    public function pinterest_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["pinterest_pin"]="";           
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["pinterest_pin"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["pinterest_pin"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["pinterest_pin"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//


        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["pinterest_pin"]="";  
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["pinterest_pin"]="";  
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $return["pinterest_pin"]=$this->web_common_report->pinterest_pin($domain); 

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo json_encode($return);

    }




  
 // Section for Buffer ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/buffer_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,buffer_share*/ 

    public function buffer_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["buffer_share"]="";           
            echo json_encode($return);
            exit();
        }


        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["buffer_share"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["buffer_share"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["buffer_share"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//


        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["buffer_share"]="";  
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["buffer_share"]="";  
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $return["buffer_share"]=$this->web_common_report->buffer_share($domain); 

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo json_encode($return);

    }




     // Section for Stumbleupon ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/stumbleupon_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,total_view,total_like,total_comment,total_list*/ 

    public function stumbleupon_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";           
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";  
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["total_view"]= ""; 
            $return["total_like"]=""; 
            $return["total_comment"]= ""; 
            $return["total_list"]= "";  
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $stumbleupon_result=$this->web_common_report->stumbleupon_info($domain);

        $return["total_view"]=$stumbleupon_result["total_view"]; 
        $return["total_like"]=$stumbleupon_result["total_like"]; 
        $return["total_comment"]=$stumbleupon_result["total_comment"]; 
        $return["total_list"]=$stumbleupon_result["total_list"]; 

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo json_encode($return);

    }




// Section for Pagestatus  ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/pagestatus_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*status,details,http_code,total_time,namelookup_time,connect_time,speed_download*/ 

    public function pagestatus_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";           
            echo json_encode($return);
            exit();
        }


        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//


        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";  
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";

            $return["http_code"]=""; 
            $return["total_time"]=""; 
            $return["namelookup_time"]=""; 
            $return["connect_time"]=""; 
            $return["speed_download"]="";  
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";
        $page_status_result=$this->web_common_report->page_status_check($domain);

        $return["http_code"]=$page_status_result["http_code"]; 
        $return["total_time"]=$page_status_result["total_time"]; 
        $return["namelookup_time"]=$page_status_result["namelookup_time"]; 
        $return["connect_time"]=$page_status_result["connect_time"]; 
        $return["speed_download"]=$page_status_result["speed_download"]; 
          
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);

        echo json_encode($return);

    }



 
// Section for Whois  ****************+++++++++++++++++++**********************************
 /* http://konok-pc/xeroneit/web_analytics/native_api/whois_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */

    /*status,details,is_registered,registrant_email,tech_email,admin_email,name_servers,created_at,changed_at,sponsor,expire_at,registrar_url
registrant_name
registrant_organization
registrant_street
registrant_city
registrant_state
registrant_postal_code
registrant_country
registrant_phone
admin_name
admin_street
admin_city
admin_state
admin_postal_code
admin_country
admin_phone*/ 

   /* public function whois_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
           $return["is_registered"]= ""; 
        $return["registrant_email"]= ""; 
        $return["tech_email"]= ""; 
        $return["admin_email"]= ""; 
        $return["name_servers"]= ""; 
        $return["created_at"]= ""; 
        $return["changed_at"]= ""; 
        $return["sponsor"]= ""; 
        $return["expire_at"]= ""; 
        $return["registrar_url"]= ""; 
        
        $return["registrant_name"]= ""; 
        $return["registrant_organization"]= ""; 
        $return["registrant_street"]= ""; 
        $return["registrant_city"]= ""; 
        $return["registrant_state"]= ""; 
        $return["registrant_postal_code"]= ""; 
        $return["registrant_country"]= ""; 
        $return["registrant_phone"]= "";

        $return["admin_name"]= ""; 
        $return["admin_street"]= ""; 
        $return["admin_city"]= ""; 
        $return["admin_state"]= ""; 
        $return["admin_postal_code"]= ""; 
        $return["admin_country"]= ""; 
        $return["admin_phone"]= "";           
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
            $return["is_registered"]= ""; 
        $return["registrant_email"]= ""; 
        $return["tech_email"]= ""; 
        $return["admin_email"]= ""; 
        $return["name_servers"]= ""; 
        $return["created_at"]= ""; 
        $return["changed_at"]= ""; 
        $return["sponsor"]= ""; 
        $return["expire_at"]= ""; 
        $return["registrar_url"]= ""; 
        
        $return["registrant_name"]= ""; 
        $return["registrant_organization"]= ""; 
        $return["registrant_street"]= ""; 
        $return["registrant_city"]= ""; 
        $return["registrant_state"]= ""; 
        $return["registrant_postal_code"]= ""; 
        $return["registrant_country"]= ""; 
        $return["registrant_phone"]= "";

        $return["admin_name"]= ""; 
        $return["admin_street"]= ""; 
        $return["admin_city"]= ""; 
        $return["admin_state"]= ""; 
        $return["admin_postal_code"]= ""; 
        $return["admin_country"]= ""; 
        $return["admin_phone"]= "";  
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";

        $return["is_registered"]= ""; 
        $return["registrant_email"]= ""; 
        $return["tech_email"]= ""; 
        $return["admin_email"]= ""; 
        $return["name_servers"]= ""; 
        $return["created_at"]= ""; 
        $return["changed_at"]= ""; 
        $return["sponsor"]= ""; 
        $return["expire_at"]= ""; 
        $return["registrar_url"]= ""; 
        
        $return["registrant_name"]= ""; 
        $return["registrant_organization"]= ""; 
        $return["registrant_street"]= ""; 
        $return["registrant_city"]= ""; 
        $return["registrant_state"]= ""; 
        $return["registrant_postal_code"]= ""; 
        $return["registrant_country"]= ""; 
        $return["registrant_phone"]= "";

        $return["admin_name"]= ""; 
        $return["admin_street"]= ""; 
        $return["admin_city"]= ""; 
        $return["admin_state"]= ""; 
        $return["admin_postal_code"]= ""; 
        $return["admin_country"]= ""; 
        $return["admin_phone"]= "";  
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        $return["status"]="1";           
        $return["details"]="Success";

        $domain_info=$this->web_common_report->whois_email($domain);
            if($domain_info['tech_email']=='' && $domain_info['is_registered']=='yes'){
                sleep(5);
                $domain_info=$this->web_common_report->whois_email($domain);
            }
            
            if($domain_info['tech_email']=='' && $domain_info['is_registered']=='yes'){
                    $f_email=$this->web_common_report->get_email($domain_info['rawdata']);
                    $domain_info['tech_email']=$f_email[0];
                    $domain_info['admin_email']=$f_email[1];
            }


        
        
        echo json_encode($domain_info);

    } 


*/
     // Section for Alexa ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/alexa_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*reach_rank
country
country_rank
traffic_rank
reach_rank
country
country_rank
traffic_rank*/ 

    public function alexa_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
        $alexa_info=$this->web_common_report->get_alexa_rank($domain); 

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo json_encode($alexa_info);

    } 


    public function similar_web_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
        $similar_web_info=$this->web_common_report->similar_web_raw_data($domain); 

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);  
        
        echo json_encode($similar_web_info);

    } 



    // Section for Google Page Rank ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/google_page_rank_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*google_page_rank*/ 

    // public function google_page_rank_check()
    // {
    //     $return=array();

    //     $user_id="";
    //     $api_key=$_GET["api_key"];
    //     $domain=$_GET["domain"];


    //     if($api_key!="")
    //     {
    //         $explde_api_key=explode('-',$api_key);
    //         $user_id="";
    //         if(array_key_exists(0, $explde_api_key))
    //         $user_id=$explde_api_key[0];
    //     }


    //     if($api_key=="" || $user_id=="" || $domain=="")
    //     {
    //         $return["status"]="0";            
    //         $return["details"]="API Key and Domain are required.";
                      
    //         echo json_encode($return);
    //         exit();
    //     }

    //     if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
    //     {
    //         $return["status"]="0";           
    //         $return["details"]="API Key does not match with any user.";
              
            
    //         echo json_encode($return);
    //         exit();
    //     }

    //     if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
    //     {
    //         $return["status"]="0";           
    //         $return["details"]="API Key does not match with any user.";
             
            
    //         echo json_encode($return);
    //         exit();
    //     }  

    //     $domain=str_replace("www.","",$domain);
    //     $domain=str_replace("http://","",$domain);
    //     $domain=str_replace("https://","",$domain);
         
        
    //      $google_page_rank=$this->web_common_report->get_google_page_rank($domain);  
    //         if($google_page_rank=="") $google_page_rank="0"; 

          
        
    //     echo $google_page_rank;

    // } 



// Section for Google Index ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/google_index_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*google_index*/ 

    /*public function google_index_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
         $google_index   =   $this->web_common_report->GoogleIP($domain);  
            
          
        
        echo $google_index;

    } 
*/


    // Section for Bing Index ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/bing_index_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*bing_index*/ 

    public function bing_index_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//


        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
        $bing_index     =   $this->web_common_report->bing_index($domain,$proxy="");  
            
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        
        echo $bing_index;

    }





// Section for Yahoo Index ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/yahoo_index_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=http://www.xeroneit.net */
    /*yahoo_index*/ 

    public function yahoo_index_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
        $yahoo_index    =   $this->web_common_report->yahoo_index($domain,$proxy="");

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);      
        
        echo $yahoo_index;

    }


// Section for Link Analysis ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/link_analysis_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.youtube.com/watch?v=vIHLaQo7wCk */
    /*link_analysis_data*/ 

    public function link_analysis_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
         
        
        $link_analysis_data=$this->web_common_report->link_statistics($domain);

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        
        echo json_encode($link_analysis_data);

    } 



 // Section for Backlink Analysis ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/backlink_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.facebook.com */
    /*link_analysis_data*/ 

    public function backlink_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);

        $backlink_count=array();
        $backlink_count=$this->web_common_report->GoogleBL($domain);

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        
        echo $backlink_count;

    } 



  // Section for Malware Google Check ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/google_malware_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.facebook.com */
    /*google_status*/ 

    public function google_malware_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
        $api="";
        $config_data=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->user_id)));
        if(count($config_data)>0) $api=$config_data[0]["google_safety_api"];

        $backlink_count=array();
        $google_status=$this->web_common_report->google_safety_check($api,$domain);

        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        
        echo $google_status;

    } 



// Section for Malware Google Check ****************+++++++++++++++++++**********************************

 /* http://konok-pc/xeroneit/web_analytics/native_api/macafee_malware_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.facebook.com */
    /*macafee_status*/ 

    public function macafee_malware_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
        
      
       $macafee_status=$this->web_common_report->macafee_safety_analysis($domain,$proxy="");
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 
        echo $macafee_status;

    }             

 /* http://konok-pc/xeroneit/web_analytics/native_api/avg_malware_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.facebook.com */
    /*avg_status*/ 

    public function avg_malware_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
        
      
       $avg_status=$this->web_common_report->avg_safety_check($domain,$proxy="");
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id); 

        echo $avg_status;

    }             


/* http://konok-pc/xeroneit/web_analytics/native_api/norton_malware_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.facebook.com */
    /*norton_status*/ 

    public function norton_malware_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }


        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
       
      
       $norton_status=$this->web_common_report->norton_safety_check($domain,$proxy=""); 
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);
        echo $norton_status;

    }             


/* http://konok-pc/xeroneit/web_analytics/native_api/domain_ip_check?api_key=1-KNUF3JL14587280016cbKPGS&domain=https://www.webasroy.com */
/*domain_ip_data*/ 

    public function domain_ip_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["domain"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $domain=str_replace("https://","",$domain);
        

      
      $domain_ip_data=$this->web_common_report->get_ip_country($domain,$proxy="");  
        
        // insert data to useges log table
        $this->_insert_usage_log($module_id=15,$request=1,$user_id);

        echo json_encode($domain_ip_data);

    }             



/* http://konok-pc/xeroneit/web_analytics/native_api/sites_in_same_ip_check?api_key=1-KNUF3JL14587280016cbKPGS&ip=104.244.42.1 */
/*same_site_data*/ 

    public function sites_in_same_ip_check()
    {
        $return=array();

        $user_id="";
        $api_key=$_GET["api_key"];
        $domain=$_GET["ip"];


        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }


        if($api_key=="" || $user_id=="" || $domain=="")
        {
            $return["status"]="0";            
            $return["details"]="API Key and Domain are required.";
                      
            echo json_encode($return);
            exit();
        }

        //*******************************************************************************//
        // =====================module access + limit check [mostofa]========================

        if(!$this->api_member_validity($user_id))
        {        
            $return["status"]="0";            
            $return["details"]="Your membership has been expired.";
            echo json_encode($return);
            exit();
        }

        $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
        $package_info=array();
        if(array_key_exists(0, $package_data))
            $package_info=$package_data[0]; 

        $module_ids='';
        if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
        $this->module_access=explode(',', $module_ids);

        if($package_info["user_type"] != 'Admin' && !in_array(15,$this->module_access))
        {            
            $return["status"]="0";            
            $return["details"]="Access Denied.";
            echo json_encode($return);
            exit();
        } 

        $status=$this->_check_usage($module_id=15,$request=1,$user_id);
        if($status=="2") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your bulk limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        else if($status=="3") 
        {
            $return["status"]="0";            
            $return["details"]= "sorry, your monthly limit is exceeded for this module.";
            echo json_encode($return);
            exit();
        }
        // =====================module access + limit check========================
        //*******************************************************************************//

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
              
            
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";           
            $return["details"]="API Key does not match with any user.";
             
            
            echo json_encode($return);
            exit();
        }  

        $domain=str_replace("www.","",$domain);
        $domain=str_replace("http://","",$domain);
        $ip=str_replace("https://","",$domain);       
      
      $same_site_data=array();
      $this->web_common_report->get_site_in_same_ip($ip,$page=1,$proxy="");  
      $same_site_data=$this->web_common_report->same_site_in_ip;  
      
      // insert data to useges log table
      $this->_insert_usage_log($module_id=15,$request=1,$user_id);  
      echo json_encode($same_site_data);

    }

    public function auction_domain($api_key)
    {
        if ($api_key=="") exit();
        $user_id=substr($api_key, 0, 1);

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "Invalid user.";
            exit();
        } 
        
        $this->_grab_auction_list_data();
    }


    public function send_notification($api_key="")
    {
        if ($api_key=="") exit();
        $user_id=substr($api_key, 0, 1);

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "Invalid user.";
            exit();
        }     

        $current_date = date("Y-m-d");
        $tenth_day_before_expire = date("Y-m-d", strtotime("$current_date + 10 days"));
        $one_day_before_expire = date("Y-m-d", strtotime("$current_date + 1 days"));
        $one_day_after_expire = date("Y-m-d", strtotime("$current_date - 1 days"));

        // echo $tenth_day_before_expire."<br/>".$one_day_before_expire."<br/>".$one_day_after_expire;

        //send notification to members before 10 days of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $tenth_day_before_expire
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = "";
        $mask = $this->config->item('product_name');
        $subject = "Payment Notification";
        foreach ($info as $value) 
        {
            $message = "Dear {$value['first_name']} {$value['last_name']},<br/> your account will expire after 10 days, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
            $to = $value['email'];
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
        }

        //send notificatio to members before 1 day of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $one_day_before_expire
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');
        $subject = "Payment Notification";
        foreach ($info as $value) {
            $message = "Dear {$value['first_name']} {$value['last_name']},<br/> your account will expire tomorrow, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
            $to = $value['email'];
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
        }

        //send notificatio to members after 1 day of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $one_day_after_expire
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');
        $subject = "Payment Notification";
        foreach ($info as $value) {
            $message = "Dear {$value['name']},<br/> your account has been expired, Please pay your fees for continuity.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
            $to = $value['email'];
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
        }

    }



    public function get_keyword_position_data($api_key="")
    {
        $user_id="";
        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }

        if($api_key=="")
        {        
            echo "API Key is required.";    
            exit();
        }

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
           echo "API Key does not match with any user.";
           exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any authentic user.";
            exit();
        }

        $this->load->library('web_common_report');
        $keywords = $this->basic->get_data("keyword_position_set");

        foreach($keywords as $value){

            $keyword = $value['keyword'];
            $country = $value['country'];
            $language = $value['language'];
            $domain = $value['website'];


            $keyword_position_google_data=$this->web_common_report->keyword_position_google($keyword, $page_number=0, $proxy="",$country,$language,$domain);

            $keyword_position_bing_data=$this->web_common_report->keyword_position_bing($keyword, $page_number=0, $proxy="",$country,$language,$domain);

            $keyword_position_yahoo_data=$this->web_common_report->keyword_position_yahoo($keyword, $page_number=0, $proxy="",$country,$language,$domain);

            $data = array(
                "keyword_id" => $value['id'],
                "google_position" => $keyword_position_google_data["status"],
                "bing_position" => $keyword_position_bing_data["status"],
                "yahoo_position" => $keyword_position_yahoo_data["status"],
                "date" => date("Y-m-d")
                );
            $this->basic->insert_data("keyword_position_report",$data);

        }
    }



    

    

    


    
}
