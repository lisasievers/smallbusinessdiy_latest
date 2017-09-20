<?php

require_once("home.php"); // loading home controller



class tools extends Home
{

    public $user_id;    
    public $download_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) {
            redirect('home/login_page', 'location');
        }
        $this->load->library('upload');
        $this->load->library('web_common_report');
        // $this->load->library('tools_library');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        $this->download_id=$this->session->userdata('download_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();


    }


    public function index()
    {
        $this->email_validator_list();
    }


    public function email_validator_list(){
        if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/tools/email_validator';
        $data['page_title'] = $this->lang->line("Valid Email Check");
        $this->_viewcontroller($data);
    }

    public function email_validator()
    {
        $this->load->library('web_common_report');
        $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        $total_emal=count($emails_array);
        
        $email_validator_writer=fopen("download/email_validator/email_validator_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($email_validator_writer, chr(0xEF).chr(0xBB).chr(0xBF));
        $total_valid_email=0;
        
        /*** Write header in the csv file ***/
        
        $write_validation[]="Email";
        $write_validation[]="Is Valid Pattern";
        $write_validation[]="Is MX Record Exist";
        fputcsv($email_validator_writer, $write_validation);
        
        $valid_email="";
        
        
        foreach ($emails_array as $email) {
            $result = $this->web_common_report->email_validate($email);
            $is_valid  = ($result['is_valid']) ? 'Yes':'No';
            $is_exists = ($result['is_exists']) ? "Yes" : "No";
            
            $write_validation=array();
            $write_validation[]=$email;
            $write_validation[]=$is_valid;
            $write_validation[]=$is_exists;
            fputcsv($email_validator_writer, $write_validation);
            
            /**if two validation passed then 1+1= 2**/
            if ($result['is_valid']+$result['is_exists'] == 2) {
                $valid_email.=$email."\n";
                $total_valid_email++;
            }
        }
        
        /*** Write all valid email address in text file **/
        
        $valid_email_file_writer = fopen("download/email_validator/email_validator_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($valid_email_file_writer, $valid_email);
        fclose($valid_email_file_writer);
        
        /**Display total valid email between total email***/
        
        echo "Total {$total_valid_email} valid email found of {$total_emal}";
    }
    


    public function duplicate_email_filter_list(){
        if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/tools/duplicate_email_filter';
        $data['page_title'] =  $this->lang->line("Duplicate Email Filter");
        $this->_viewcontroller($data);
    }

    public function email_unique_maker()
    {
        $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        
        $total_email=count($emails_array);
        
        $emails_array=array_unique($emails_array);
        
        $total_unique_email=count($emails_array);
        
        $unique_email_str=implode("\n", $emails_array);
        
        /*** Write all Unique email address in text file **/
        
        $unique_email_file_writer = fopen("download/unique_email/unique_email_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($unique_email_file_writer, $unique_email_str);
        fclose($unique_email_file_writer);
        
        echo "Total {$total_unique_email} unique email found of {$total_email} email";
    }



    public function url_encode_list(){
        if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/tools/url_encode_list';
        $data['page_title'] = $this->lang->line("URL Encoder/Decoder");
        $this->_viewcontroller($data);
    }


    public function url_encode_action(){

        // $this->load->library('web_common_report');
        $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        $total_emal=count($emails_array);
        
        $email_validator_writer=fopen("download/url_encode/url_encode_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($email_validator_writer, chr(0xEF).chr(0xBB).chr(0xBF));
        // $total_valid_email=0;
        
        /*** Write header in the csv file ***/
        
        $write_validation[]="URL";       
        $write_validation[]="Encoded URL";       
        fputcsv($email_validator_writer, $write_validation);
        
        $valid_email="";


        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";
        $str.="<td>URL</td><td>Encoded URL</td>";             
        $str.="</tr>";
        
        
        foreach ($emails_array as $email) {
            $result = rawurlencode($email);           
            
            $write_validation=array();

            $write_validation[]=$email;
            $write_validation[]=$result;          
            fputcsv($email_validator_writer, $write_validation);
            
            $count++;
            $str.= "<tr><td>".$count."</td><td>".$email."</td>";
            $str.="<td>".$result."</td>";
            $str.="</tr>";
        }
        
        /*** Write all encoded url address in text file **/
        
        $valid_email_file_writer = fopen("download/url_encode/url_encode_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($valid_email_file_writer, $valid_email);
        fclose($valid_email_file_writer);
        
        /**Display total encoded url***/
        
        echo $str.="</table>";

    }

    /*public function url_decode_list(){
        $data['body'] = 'admin/tools/url_decode_list';
        $data['title'] = 'URL Decoder';
        $this->_viewcontroller($data);

    }*/


    public function url_decode_action(){

        // $this->load->library('web_common_report');
        $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        $total_emal=count($emails_array);
        
        $email_validator_writer=fopen("download/url_decode/url_decode_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($email_validator_writer, chr(0xEF).chr(0xBB).chr(0xBF));
        // $total_valid_email=0;
        
        /*** Write header in the csv file ***/
        
        $write_validation[]="URL";       
        $write_validation[]="Decoded URL";       
        fputcsv($email_validator_writer, $write_validation);
        
        $valid_email="";

        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";
        $str.="<td>URL</td><td>Decoded URL</td>";             
        $str.="</tr>";
        
        
        foreach ($emails_array as $email) {
            $result = urldecode($email);           
            
            $write_validation=array();

            $write_validation[]=$email;
            $write_validation[]=$result;          
            fputcsv($email_validator_writer, $write_validation);

            $count++;
            $str.= "<tr><td>".$count."</td><td>".$email."</td>";
            $str.="<td>".$result."</td>";
            $str.="</tr>";
            
        }
        
        /*** Write all decoded url in text file **/
        
        $valid_email_file_writer = fopen("download/url_decode/url_decode_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($valid_email_file_writer, $valid_email);
        fclose($valid_email_file_writer);
        
        /**Display total decoded url***/
        
        echo $str.="</table>";

    }



    public function url_canonical_check() {

        $data['body'] = 'admin/tools/url_canonical_check';
        $data['page_title'] = $this->lang->line("URL Canonical Check");
        $this->_viewcontroller($data);

    }

    public function url_canonical_action() {

        if($_SERVER['REQUEST_METHOD'] === 'GET') :
            redirect('home/access_forbidden', 'location');     
        endif;       

        $this->load->library('web_common_report');
        $url_lists = array();
        $url_values = explode(',',$this->input->post('urlvalues'));

        if(count($url_values) <= 50) :
            foreach($url_values as $url_value) :
                $url_value = trim($url_value);
                if(is_valid_url($url_value) === TRUE) :
                    $check_data = $this->web_common_report->url_canonical_check($url_value);
                    $url_lists[] = array('url' => $url_value, 'url_canonical' => $check_data);
                endif;
            endforeach;
        endif;    

        header('Content-Type: application/json');
        echo json_encode(array('url_lists' => $url_lists));

    }



    public function gzip_check() {

        $data['body'] = 'admin/tools/gzip_check';
        $data['page_title'] = $this->lang->line("Gzip Check");
        $this->_viewcontroller($data);    

    }

    public function gzip_check_action() {

        if($_SERVER['REQUEST_METHOD'] === 'GET') :
            redirect('home/access_forbidden', 'location');     
        endif;       

        $this->load->library('web_common_report');
        $url_lists = array();
        $url_values = explode(',',$this->input->post('urlvalues'));

        if(count($url_values) <= 50) :
            foreach($url_values as $url_value) :
                $url_value = trim($url_value);
                if(is_valid_url($url_value) === TRUE) :
                    $check_data = $this->web_common_report->gzip_compression_check($url_value);
                    $url_lists[] = array('url' => $url_value, 'gzip_enable' => $check_data['is_gzip_enable'], 'gzip_page_size' => $check_data['gzip_page_size'], 'normal_page_size' => $check_data['normal_page_size']);
                endif;
            endforeach;
        endif;    

        header('Content-Type: application/json');
        echo json_encode(array('url_lists' => $url_lists));

    }



    public function base64_encode_list() {

        $data['body'] = 'admin/tools/base64_encode_list';
        $data['page_title'] = $this->lang->line("Base64 Encoder/Decoder");
        $this->_viewcontroller($data);

    }

    public function base64_encode_action() {

        $this->load->library('web_common_report');
        $base64=$this->input->post('base64', true);

        $output = $this->web_common_report->base_64_encode($base64);

        $str="<table class='table table-bordered table-hover table-striped'><tr>";
        $str.="<td>Encoded Base64</td>";             
        $str.="</tr>";
        $str.= "<tr><td><div id='copyTarget'>".$output."</div></td></tr>";
        
        echo $str.="</table>";

    }

    public function base64_decode_action() {

        $this->load->library('web_common_report');
        $base64=$this->input->post('base64', true);

        $output = $this->web_common_report->base_64_decode($base64);

        $str="<table class='table table-bordered table-hover table-striped'><tr>";
        $str.="<td>Decoded Base64</td>";             
        $str.="</tr>";
        $str.= "<tr><td><div id='copyTarget'>".$output."</div></td></tr>";
        
        echo $str.="</table>";

    }    



    public function robot_code_generator(){
        if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/tools/robot_code_generator';
        $data['page_title'] = $this->lang->line("Robot Code Generator");
        $this->_viewcontroller($data);
    }

    public function robot_code_generator_action(){           

        $all_robot = $this->input->post('all_robot',true);
        $custom_robot = $this->input->post('custom_robot',true);

        $basic_all_robots = $this->input->post('basic_all_robots',true);
        $crawl_delay = $this->input->post('crawl_delay',true);
        $site_map = $this->input->post('site_map',true);
        $custom_crawl_delay = $this->input->post('custom_crawl_delay',true);
        $custom_site_map = $this->input->post('custom_site_map',true);
        $google = $this->input->post('google',true);
        $msn_search = $this->input->post('msn_search',true);
        $yahoo = $this->input->post('yahoo',true);
        $ask_teoma = $this->input->post('ask_teoma',true);
        $cuil = $this->input->post('cuil',true);
        $gigablast = $this->input->post('gigablast',true);
        $scrub = $this->input->post('scrub',true);
        $dmoz_checker = $this->input->post('dmoz_checker',true);
        $nutch = $this->input->post('nutch',true);
        $alexa_wayback = $this->input->post('alexa_wayback',true);
        $baidu = $this->input->post('baidu',true);
        $never = $this->input->post('never',true);


        $google_image = $this->input->post('google_image',true);
        $google_mobile = $this->input->post('google_mobile',true);
        $yahoo_mm = $this->input->post('yahoo_mm',true);
        $msn_picsearch = $this->input->post('msn_picsearch',true);
        $SingingFish = $this->input->post('SingingFish',true);
        $yahoo_blogs = $this->input->post('yahoo_blogs',true);

        $restricted_dir = $this->input->post('restricted_dir',true);

        $restricted_dir = rtrim($restricted_dir,',');
        $directories = explode(',', $restricted_dir);

        if($all_robot == 1)
        {
            if($basic_all_robots == 'allowed')
            {

                $user_agent = 'User-agent: *'.PHP_EOL;
                $disallow = 'Disallow:'.PHP_EOL;
                $handle_write = fopen("download/robot/robot_{$this->user_id}_{$this->download_id}.txt", "w");

                $write_var = fwrite($handle_write, $user_agent);
                $write_var = fwrite($handle_write, $disallow);

                if(isset($crawl_delay))
                {
                    $crawl_delay = 'Crawl-delay: '.$crawl_delay.PHP_EOL;
                    $write_var = fwrite($handle_write, $crawl_delay);
                }

                if(isset($site_map))
                {
                    $site_map = 'Sitemap: '.$site_map.PHP_EOL;
                    $write_var = fwrite($handle_write, $site_map);
                }   

                if(isset($write_var)) echo $this->lang->line('your file is ready to download');
            }

            else
            {

                $user_agent = 'User-agent: *'.PHP_EOL;
                $disallow = 'Disallow: /'.PHP_EOL;
                $handle_write = fopen("download/robot/robot_{$this->user_id}_{$this->download_id}.txt", "w");

                $write_var = fwrite($handle_write, $user_agent);
        $write_var = fwrite($handle_write, $disallow);

        if(isset($crawl_delay))
        {
            $crawl_delay = 'Crawl-delay: '.$crawl_delay.PHP_EOL;
            $write_var = fwrite($handle_write, $crawl_delay);
        }

        if(isset($site_map))
        {
            $site_map = 'Sitemap: '.$site_map.PHP_EOL;
            $write_var = fwrite($handle_write, $site_map);
        }   
                if(isset($write_var)) echo $this->lang->line('your file is ready to download');
            }             



        }//End of if (all_robot) *******************************************

        if($custom_robot == 1)
        {
           $bots = array();
           $bots_disallowed = array();

/*
        if($google == 'allowed') $bots[] = 'googlebot';
           else
            $bots_disallowed[] = 'googlebot';

        if($google == 'allowed') $bots[] = 'googlebot';
           else
            $bots_disallowed[] = 'googlebot';*/

        if($google == 'allowed') $bots[] = 'googlebot';
           else
            $bots_disallowed[] = 'googlebot';

        if($msn_search == 'allowed') $bots[] = 'msnbot';
        else
            $bots_disallowed[] = 'msnbot';

        if($yahoo == 'allowed') $bots[] = 'yahoo-slurp';
        else
            $bots_disallowed[] = 'yahoo-slurp';

        if($ask_teoma == 'allowed') $bots[] = 'teoma';
        else
            $bots_disallowed[] = 'teoma';

        if($cuil == 'allowed') $bots[] = 'twiceler';
        else
            $bots_disallowed[] = 'twiceler';

        if($gigablast == 'allowed') $bots[] = 'gigabot';
        else
            $bots_disallowed[] = 'gigabot';

        if($scrub == 'allowed') $bots[] = 'scrubby';
        else
            $bots_disallowed[] = 'scrubby';

        if($dmoz_checker == 'allowed') $bots[] = 'robozilla';
        else
            $bots_disallowed[] = 'robozilla';

        if($nutch == 'allowed') $bots[] = 'nutch';
        else
            $bots_disallowed[] = 'nutch';

        if($alexa_wayback == 'allowed') $bots[] = 'ia_archiver';
        else
            $bots_disallowed[] = 'ia_archiver';

        if($baidu == 'allowed') $bots[] = 'baiduspider';
        else
            $bots_disallowed[] = 'baiduspider';

        if($never == 'allowed') $bots[] = 'naverbot';
        else
            $bots_disallowed[] = 'naverbot';


        if($google_image == 'allowed') $bots[] = 'googlebot-image';
        else
            $bots_disallowed[] = 'googlebot-image';

        if($google_mobile == 'allowed') $bots[] = 'googlebot-mobile';
        else
            $bots_disallowed[] = 'googlebot-mobile';

        if($yahoo_mm == 'allowed') $bots[] = 'yahoo-mmcrawler';
        else
            $bots_disallowed[] = 'yahoo-mmcrawler';

        if($msn_picsearch == 'allowed') $bots[] = 'psbot';
        else
            $bots_disallowed[] = 'psbot';

        if($SingingFish == 'allowed') $bots[] = 'asterias';
        else
            $bots_disallowed[] = 'asterias';

        if($yahoo_blogs == 'allowed') $bots[] = 'yahoo-blogs/v3.9';
        else
            $bots_disallowed[] = 'yahoo-blogs/v3.9';

        $handle_write = fopen("download/robot/robot_{$this->user_id}_{$this->download_id}.txt", "w");

        if(!empty($bots) || !empty($bots_disallowed) || !empty($directories) || isset($custom_crawl_delay) || isset($custom_site_map))       
        {   


            $handle_write = fopen("download/robot/robot_{$this->user_id}_{$this->download_id}.txt", "w");
            
            if( !empty($bots) )
            {
                for($i=0; $i < count($bots); $i++)
                {
                    fwrite($handle_write, "User-agent: ".$bots[$i].PHP_EOL);
                    fwrite($handle_write, "Disallow: ".PHP_EOL);                
                }           
            }

            if( !empty($bots_disallowed) )
            {
                for($j=0; $j < count($bots_disallowed); $j++)
                {
                    fwrite($handle_write, "User-agent: ".$bots_disallowed[$j].PHP_EOL);
                    fwrite($handle_write, "Disallow: /".PHP_EOL);               
                }
            }
            

            fwrite($handle_write, "User-agent: *".PHP_EOL);
            fwrite($handle_write, "Disallow: ".PHP_EOL);
        // if(empty($directories)) fwrite($handle_write, "Disallow: ".PHP_EOL);


            if(!empty($directories))
            {                   
                for($k=0; $k < count($directories); $k++)
                {           
                    fwrite($handle_write, "Disallow: ".$directories[$k].PHP_EOL);                       
                }                                       
            }

            if(isset($custom_crawl_delay))
                fwrite($handle_write, "Crawl-delay: ".$custom_crawl_delay.PHP_EOL);
            if(isset($custom_site_map))
                fwrite($handle_write, "Sitemap: ".$custom_site_map.PHP_EOL);

            fclose($handle_write);      

             echo $this->lang->line('your file is ready to download');            


        }           


    }

}

/*public function site_map_list(){
	 $data['body'] = 'admin/tools/site_map_list';
     $data['title'] = 'Sitemap Generator';
     $this->_viewcontroller($data);
}


public function sitemap_generator_action(){
		set_time_limit(0);



		$this->load->library('Tools_library');	
		$website = $this->input->post('domain'); 
		$website=rawurldecode($website);        
		$website=str_replace("____","/",$website);    
		
        $found_url = $this->tools_library->start_scrapping($website);

        print_r($found_url);
}*/


public function plagarism_check_list(){
	 if($this->session->userdata('user_type') != 'Admin' && !in_array(12,$this->module_access))
     redirect('home/login_page', 'location'); 

     $data['body'] = 'admin/tools/plagarism_check_list';
     $data['page_title'] = $this->lang->line("Plagiarism Check");
     $this->_viewcontroller($data);
}

 public function plagarism_check_action(){

 	$text = $this->input->post('emails');
 	$this->load->library('Tools_library');

 	$this->plagarism_check($text); 	

 }

public function plagarism_check($sample_text){

    //************************************************//
    $status=$this->_check_usage($module_id=12,$request=1);
    if($status=="2") 
    {
        echo 2;
        return;
    }
    else if($status=="3") 
    {
        echo 3;
        return;
    }
    //************************************************//

    //******************************//
    // insert data to useges log table
    $this->_insert_usage_log($module_id=12,$request=1);   
    //******************************//

$found =0;
$not_found = 0;

$sample_text = trim($sample_text); // trim spaces of sample text

$page_encoding =  mb_detect_encoding($sample_text);

    if(isset($page_encoding))
    {
        $utf8_text = iconv( $page_encoding, "utf-8", $sample_text );
        $sample_text = $utf8_text;
    }

// $number_of_words = $this->mb_count_words($sample_text); // find the length of string

$number_of_words = count(mb_split(' ', $sample_text));


// find the number of phrase. if number of words is less than 10 it will be considered as one and only phrase.
if($number_of_words  >= 1 && $number_of_words < 10 ) $str = $sample_text;

// setting a variable $x to find the number of phrases containing 10 words
elseif($number_of_words >= 10 && $number_of_words <= 500) 
    $x = ($number_of_words - ($number_of_words % 10)) / 10;

// explode string to array of words
// $i for number of total phrase.  $j number of adding words. $l is the length of phrase or ngram

// if $x is set i.e. the string contain 10 or more than 10 words we will run this segment of code

$str1="<table class='table table-bordered table-hover table-striped'><thead><tr><td><h4>Text</h4></td><td><h4>Search Result</h4></td></tr></thead>";
if(isset($x))

{	
	// explode sample text string to an array of words
	$word = explode(" ",$sample_text);

	// $j is the second loop variable to create the phrase of 10 words to check plagarism
	// $l is the number of words in the phrase which is initialy zero.
	$j = 0;
	$l = 0;	
  $split_word=array();

	for($i=0; $i< $x-1; $i++)
		{		
			$ngram = '';
			for($j = 0; $j < 10; $j++)		
			{    
                if(isset($word[$j+$l]))
				$ngram = $ngram." ".$word[$j+$l];			
			}
			$l = $l + 10;	
            if(isset($ngram))		
			$split_word[] = trim($ngram);			
		}

        $split_word=array_filter($split_word);

        $size_split_word = sizeof($split_word);
		// sending phases to search engine 
		for($i=0; $i < $size_split_word; $i++)
		{
			// only even numbers phares are send to search for reducing time
		 	if( $i % 2 == 0)

		 	{
			// searching on search engine.
				$keyword = $split_word[$i];
				$keyword = urlencode($keyword);	
				
				$url="www.google.com/search?q={$keyword}";	
				$ch = curl_init(); // initialize curl handle
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_VERBOSE, 0);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
				curl_setopt($ch, CURLOPT_AUTOREFERER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,7);
				curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
				curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
				curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
				curl_setopt($ch, CURLOPT_POST, 0); // set POST method
				
				/***** Proxy set for google . if lot of request gone, google will stop reponding. That's why it's should use some proxy *****/

				/**** Using proxy of public and private proxy both ****/
					if($this->web_common_report->proxy_ip!='')
						curl_setopt($ch, CURLOPT_PROXY, $this->web_common_report->proxy_ip);
					
					if($this->web_common_report->proxy_auth_pass!='')	
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->web_common_report->proxy_auth_pass);
						
			
					
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
				curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");	
					
			   $content = curl_exec($ch); // run the whole process
			   $content = preg_replace('#<input.*?>#si',"",$content);
			   $content = preg_replace('#<title>.*?</title>#si',"",$content);
               /*$content= preg_replace('#<a.*?>.*?</a>#si',"",$content);*/
               $content= preg_replace('#Your search(.*?)did not match any documents.#si',"",$content);
               $content= preg_replace('#Did you mean:#si',"",$content);
			   $content=str_replace("<b>","",$content);
			   $content=str_replace("</b>","",$content);
			   $content=str_replace("<strong>","",$content);
			   $content=str_replace("</strong>","",$content);
			  
			  // echo string position if found
				 $str_pos = mb_stripos($content, $keyword);				

				// if string found print match found or print mantch not found  
				  if($str_pos!==FALSE)
				  {	 
				  	
                    $url = 'https://www.google.com/search?q="'.$split_word[$i].'"';				  
                    $str1.= "<tr><td>{$split_word[$i]}</td><td><a href='".$url."' target = '_blank'><span class='label label-success'>Already Exist</sapn></a></td></tr>";
                    $found++; 
                  }
				  
				  else
				  {					  	
                    $str1.= "<tr><td>{$split_word[$i]}</td><td><span class='label label-warning'>Not Exist</sapn></td></tr>";
                    $not_found++;
				  }	

		}
	}
	$str1.= "</table>"; 

    $total = $found + $not_found; 
    $unique_result = ($found/$total)*100;

    $str1.= "_sep_".$unique_result;

    //******************************//
    // insert data to useges log table
    $this->_insert_usage_log($module_id=12,$request=1);   
    //******************************//

    echo $str1;		  
}
	// if string contain less than 10 words consider it as single and send it to search engine
	elseif(isset($str))
	{
		$keyword = $str;
				$keyword = urlencode($keyword);	

				$url="www.google.com/search?q={$keyword}";	
				$ch = curl_init(); // initialize curl handle
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_VERBOSE, 0);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
				curl_setopt($ch, CURLOPT_AUTOREFERER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,7);
				curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
				curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
				curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
				curl_setopt($ch, CURLOPT_POST, 0); // set POST method
				
				/***** Proxy set for google . if lot of request gone, google will stop reponding. That's why it's should use some proxy *****/

				if($this->web_common_report->proxy_ip!='')
						curl_setopt($ch, CURLOPT_PROXY, $this->web_common_report->proxy_ip);
					
				if($this->web_common_report->proxy_auth_pass!='')	
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->web_common_report->proxy_auth_pass);
						
						
					
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
				curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");	
					
			   $content = curl_exec($ch); // run the whole process
			   $content = preg_replace('#<input.*?>#si',"",$content);
			   $content = preg_replace('#<title>.*?</title>#si',"",$content);
               /*$content= preg_replace('#<a.*?>.*?</a>#si',"",$content);*/
               $content= preg_replace('#Your search(.*?)did not match any documents.#si',"",$content);
               $content= preg_replace('#<p>Searches related to.*?</p>#si',"",$content);

			  
			  // echo string position if found
				$str_pos=strpos($content, $keyword);
				

				// if string found print match found or print mantch not found  
				  if($str_pos!==FALSE)
				  {	 
				  
				  	$url = 'https://www.google.com/search?q="'.$str.'"';
                    $found++;
                    $str1.= "<tr><td>{$str}</td><td><a href='".$url."' target = '_blank'><span class='label label-success'>Already Exist</sapn></a></td></tr>";
				  }
				  
				  else
				  {	 
				  	
                    $str1.= "<tr><td>{$str}</td><td><span class='label label-warning'>Not Exist</sapn></td></tr>";
                    $not_found++;
				  }

				 $str1.= "</table>"; 

             $total = $found + $not_found; 
    $unique_result = ($found/$total)*100;

     $str1.= "_sep_".$unique_result;
    
    echo $str1;   

          
	}

// if string is more than 500 words show this message
elseif($number_of_words > 500) echo "size_error_sep_0";

// if string is blank show this message
else echo "blank_error_sep_0";



 }


 public function meta_tag_list()
 {
    if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
        redirect('home/login_page', 'location'); 

        $data['body'] = 'admin/tools/meta_tag_list';
    $data['page_title'] = $this->lang->line("Metatag Generator");
    $this->_viewcontroller($data);
 }

 public function meta_tag_action(){
   $is_google = $this->input->post('is_google');
   $is_facebook = $this->input->post('is_facebook');
   $is_twiter = $this->input->post('is_twiter');

    $google_description = $this->input->post('google_description');       
    $google_keywords = $this->input->post('google_keywords');       
    $google_copyright = $this->input->post('google_copyright');       
    $google_author = $this->input->post('google_author');       
    $google_application_name = $this->input->post('google_application_name');
           

    $facebook_title = $this->input->post('facebook_title');
    $facebook_type = $this->input->post('facebook_type');
    $facebook_image = $this->input->post('facebook_image');
    $facebook_url = $this->input->post('facebook_url');
    $facebook_description = $this->input->post('facebook_description');
    $facebook_app_id = $this->input->post('facebook_app_id');
    $facebook_localization = $this->input->post('facebook_localization');

    $twiter_card = $this->input->post('twiter_card');
    $twiter_title = $this->input->post('twiter_title');
    $twiter_description = $this->input->post('twiter_description');
    $twiter_image = $this->input->post('twiter_image');


    $handle_write = fopen("download/metatag/metatag_{$this->user_id}_{$this->download_id}.txt", "w");

    if($is_google == 1){    

        fwrite($handle_write,'<meta name="description" content="'.$google_description.'" />'.PHP_EOL);
        fwrite($handle_write,'<meta name="keywords" content="'.$google_keywords.'" />'.PHP_EOL);
        fwrite($handle_write,'<meta name="author" content="'.$google_copyright.'" />'.PHP_EOL);
        fwrite($handle_write,'<meta name="copyright" content="'.$google_author.'" />'.PHP_EOL);
        fwrite($handle_write,'<meta name="application-name" content="'.$google_application_name.'" />'.PHP_EOL);
        
    } 

    if($is_facebook == 1){

        fwrite($handle_write, '<meta property="og:title" content="'.$facebook_title.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="og:type" content="'.$facebook_type.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="og:image" content="'.$facebook_image.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="og:url" content="'.$facebook_url.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="og:description" content="'.$facebook_description.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="fb:app_id" content="'.$facebook_app_id.'" />'.PHP_EOL);
        fwrite($handle_write, '<meta property="og:locale" content="'.$facebook_localization.'" />'.PHP_EOL);
    }


    if($is_twiter == 1){

       fwrite($handle_write, '<meta name="twitter:card" content="'.$twiter_card.'" />'.PHP_EOL);
       fwrite($handle_write, '<meta name="twitter:title" content="'.$twiter_title.'" />'.PHP_EOL);
       fwrite($handle_write, '<meta name="twitter:description" content="'.$twiter_description.'" />'.PHP_EOL);
       fwrite($handle_write, '<meta name="twitter:image" content="'.$twiter_image.'" />'.PHP_EOL);
    }

    echo $this->lang->line('your file is ready to download');
  
 }


 public function email_encoder_decoder(){
    if($this->session->userdata('user_type') != 'Admin' && !in_array(13,$this->module_access))
    redirect('home/login_page', 'location'); 

    $data['body'] = 'admin/tools/email_encoder_decoder';
    $data['page_title'] = $this->lang->line('email encoder/decoder');
    $this->_viewcontroller($data);
 }


 public function email_encoder_action(){
        $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        $total_emal=count($emails_array);
        
        $email_validator_writer=fopen("download/email_encode_decode/email_encode_decode_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($email_validator_writer, chr(0xEF).chr(0xBB).chr(0xBF));
        // $total_valid_email=0;
        
        /*** Write header in the csv file ***/
        
        $write_validation[]="Email";       
        $write_validation[]="Encoded Email";       
        fputcsv($email_validator_writer, $write_validation);
        
        $valid_email="";


        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";
        $str.="<td>Email</td><td>Encoded Email</td>";             
        $str.="</tr>";
        
        
        foreach ($emails_array as $email) {
            $result = $this->email_encoder($email);           
            
            $write_validation=array();

            $write_validation[]=$email;
            $write_validation[]=$result;          
            fputcsv($email_validator_writer, $write_validation);
            
            $count++;
            $str.= "<tr><td>".$count."</td><td>".$email."</td>";
            $str.="<td>".$result."</td>";
            $str.="</tr>";
        }
        
        /*** Write all encoded url address in text file **/
        
        /*$valid_email_file_writer = fopen("download/url_encode/url_encode_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($valid_email_file_writer, $valid_email);*/
        /*fclose($valid_email_file_writer);*/
        
        /**Display total encoded url***/
        
        echo $str.="</table>";
 }


 public function email_decoder_action(){

    $emails=$this->input->post('emails', true);
        $emails=str_replace("\n", ",", $emails);
        $emails_array=explode(",", $emails);
        $total_emal=count($emails_array);
        
        $email_validator_writer=fopen("download/email_encode_decode/email_encode_decode_{$this->user_id}_{$this->download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($email_validator_writer, chr(0xEF).chr(0xBB).chr(0xBF));
        // $total_valid_email=0;
        
        /*** Write header in the csv file ***/
        
        $write_validation[]="Email";       
        $write_validation[]="Decoded Email";       
        fputcsv($email_validator_writer, $write_validation);
        
        $valid_email="";

        $count=0;
        $str="<table class='table table-bordered table-hover table-striped'><tr><td>SL</td>";
        $str.="<td>Email</td><td>Decoded Email</td>";             
        $str.="</tr>";
        
        
        foreach ($emails_array as $email) {
            $result = $this->email_decoder($email);           
            
            $write_validation=array();

            $write_validation[]=$email;
            $write_validation[]=$result;          
            fputcsv($email_validator_writer, $write_validation);

            $count++;
            $str.= "<tr><td>".$count."</td><td>".htmlspecialchars($email)."</td>";
            $str.="<td>".$result."</td>";
            $str.="</tr>";
            
        }
        
        /*** Write all decoded url in text file **/
        
        /*$valid_email_file_writer = fopen("download/url_decode/url_decode_{$this->user_id}_{$this->download_id}.txt", "w");
        fwrite($valid_email_file_writer, $valid_email);*/
        /*fclose($valid_email_file_writer);*/
        
        /**Display total decoded url***/
        
        echo $str.="</table>";

 }



 public function email_encoder($email){
    $output = '';
    for($i=0;$i< strlen($email); $i++){
        $output .= '&#'.ord($email[$i]).';';
    }

    return htmlspecialchars($output);
 }

public function email_decoder($email){
    for($i=33;$i<127;$i++){
        $html_encoded = "&#".$i.";";
        $html_decode = chr($i);
        $email =str_replace($html_encoded,$html_decode,$email);
    }

    return $email;
} 

/****This is for counting utf-8 words***/
public function mb_count_words($string) {
    preg_match_all('/[\pL\pN\pPd]+/u', $string, $matches);
    return count($matches[0]);
}



}
