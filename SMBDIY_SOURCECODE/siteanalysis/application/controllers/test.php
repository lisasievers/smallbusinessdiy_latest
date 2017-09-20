<?php
class Test extends CI_Controller
{
	public $user_id;
    public function test()
    {
		parent::__construct();
		
		$this->load->model('basic');
		$this->load->library('Web_common_report');
		$this->load->library('session');
		$this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        echo $this->user_id;
    }
	


	function x()
	{
		echo $_GET["api_key"];
		echo $_GET["domain"];
	}
	
	function index()
	{
	
		//$c=$this->web_common_report->ip_canonical_check("xeroneit.net");
		echo "<pre>";
		//echo $c=$this->web_common_report->puny_encoder("㯙㯜㯙㯟.net");
		
		 $c=$this->web_common_report->get_dailymotion_video_search("Software");
		
		print_r($c);
		
		exit();
	
	
	
		$key="AIzaSyCGwZ99I4ag9VNVRL44fxYB3Ir62m5l-YQ";
		$url="https://www.googleapis.com/urlshortener/v1/url?fields=analytics%2Ccreated%2Cid%2Ckind%2ClongUrl%2Cstatus&key={$key}";
		
		$long_url="http://stackoverflow.com/questions/7843406/codeigniter-how-to-catch-db-errors";
		
		$data['longUrl']=$long_url;
		$data=json_encode($data);
		
		 $headers = array("Content-type: application/json");
						
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POST,TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		
		
        $content = curl_exec($ch); // run the whole process
		
		$content= json_decode($content,TRUE);
		
		echo "<pre>";
			print_r($content);
		echo "</pre>";
		
        curl_close($ch);
		
	}


	public function content()
	{
		$key="AIzaSyCGwZ99I4ag9VNVRL44fxYB3Ir62m5l-YQ";
		$short_url="http://goo.gl/Je2kfn";
		$short_url_encode	= urlencode($short_url);
		
		$url="https://www.googleapis.com/urlshortener/v1/url?shortUrl={$short_url_encode}&projection=FULL&fields=analytics%2Ccreated%2Cid%2Ckind%2ClongUrl%2Cstatus&key={$key}";
		
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch); // run the whole process
		
		$content= json_decode($content,TRUE);
		
		echo "<pre>";
			print_r($content);
		echo "</pre>";
		
        curl_close($ch);
	}
	
	
	
	
	function google_login(){
	
		$this->load->library('Google_login');

		echo $this->google_login->set_login_button();
		
	}
	
	
	function google_login_back(){
	
		$this->load->library('Google_login');
		$info=$this->google_login->user_details();
		
		echo "<pre>";
			print_r($info);
		echo "</pre>";
		
		
	}
	
	
	
	public function check_proxy_ip(){
		
		 $proxy_info = $this->basic->get_data("config_proxy");
		 
		 
		 foreach($proxy_info as $info){
		 	$proxy=$info['proxy'].":".$info['port'];
			
		
		$url="www.google.com/search";
			
		$ch = curl_init(); // initialize curl handle
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_AUTOREFERER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
		curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 50s
		curl_setopt($ch, CURLOPT_POST, 0); // set POST method
		
		curl_setopt($ch, CURLOPT_PROXY, $proxy);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies1.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies1.txt"); 
		
		$content = curl_exec($ch); // run the whole process	


		/****If it returns http code without 200 means caught by google, or redirect to captcha page*****/

		$get_info = curl_getinfo($ch);
		$httpcode=$get_info['http_code'];

		if($httpcode!='200'){
			echo "{$proxy}: Dead<br>";
		 }
		 
		else
			echo "{$proxy}: Live<br>"; 
		
		
	}	
		
  }
	
}



 ?>