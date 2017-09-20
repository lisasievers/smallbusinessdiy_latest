<?php 
class google{

	public $google_api_key="";
	public $mailchimp_api_key=""; 
    public $mailchimp_list_id="";

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');

		$google_config=$this->CI->basic->get_data("connectivity_config");
		if(isset($google_config[0]))
		{			
			$this->google_api_key=$google_config[0]["google_api_key"];
		}

		$lead_config=$this->CI->basic->get_data("lead_config",array("where"=>array("status"=>"1")));
		if(isset($lead_config[0]))
		{			
			$this->mailchimp_api_key=$lead_config[0]["mailchimp_api_key"];
			$this->mailchimp_list_id=$lead_config[0]["mailchimp_list_id"];	
		}


	}
	

	function google_page_speed_insight($domain="",$strategy="desktop")
	{

		$key=$this->google_api_key;
		if($domain=="" || $key=="") exit();

		$url="https://www.googleapis.com/pagespeedonline/v3beta1/runPagespeed?key=".$key."&url=".$domain."&strategy=".$strategy;

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
     
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch); // run the whole process

		$content= json_decode($content,TRUE);
		// echo "<pre>";
		// print_r($content);
		// echo "</pre>";
		curl_close($ch);

		return $content;

	}


	function clean_domain_name($domain){

 		$domain=trim($domain);
		$domain=strtolower($domain);
		
		$domain=str_replace("www.","",$domain);
		$domain=str_replace("http://","",$domain);
		$domain=str_replace("https://","",$domain);
		$domain=str_replace("/","",$domain);
		
		return $domain; 
	}

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
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method
     
		 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
            
            $content = curl_exec($ch); // run the whole process
			
            curl_close($ch);
			
			return $content;
			
	}
	
	



	function mobile_ready($domain="")
	{		
		$key=$this->google_api_key;
		
		if($domain=="" || $key=="") exit();
		$domain=$this->clean_domain_name($domain);
		$domain=addHttp($domain);
		$url="https://www.googleapis.com/pagespeedonline/v3beta1/mobileReady?key=".$key."&url=".$domain."&strategy=mobile";
		$respose=$this->get_general_content($url);
		$respose_array=json_decode($respose,true);
		if(!$respose_array || !isset($respose_array["ruleGroups"]["USABILITY"]["pass"]))
		$respose=$this->get_general_content($url);
		else return $respose;
	}


	function syncMailchimp($data='') 
 	{
        $apikey = $this->mailchimp_api_key; // They key is generated at mailchimps controlpanel under settings.
        $apikey_explode = explode('-',$apikey); // The API ID is the last part of your api key, after the hyphen (-), 
        if(is_array($apikey_explode) && isset($apikey_explode[1])) $api_id=$apikey_explode[1];
        else $api_id="";
        $listId = $this->mailchimp_list_id; //  example: us2 or us10 etc.

        if($apikey=="" || $api_id=="" || $listId=="" || $data=="") exit();
      
        $auth = base64_encode( 'user:'.$apikey );
		
        $insert_data=array
        (
			'email_address'  => $data['email'],
			'status'         => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
			'merge_fields'  => array('FNAME'=>$data['firstname'],'LNAME'=>'','CITY'=>'','MMERGE5'=>"Subscriber")	
	    );
			
		$insert_data=json_encode($insert_data);
 	
		$url="https://".$api_id.".api.mailchimp.com/3.0/lists/".$listId."/members/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Basic '.$auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $insert_data);                                                                                                           
        $result = curl_exec($ch);
    }
	
	
	
	
	
}


?>