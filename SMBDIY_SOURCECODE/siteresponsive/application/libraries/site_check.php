<?php 
	require_once('simple_html_dom.php');
	
class Site_check{

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
	}
	
	
	
	function get_general_content($url){
			$ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
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
			$curl_info= curl_getinfo($ch);
            curl_close($ch);
			$response['content']=$content;
			$response['curl_info']=$curl_info;
			
			return $response;
		}
		
	function get_gzip_response($url){
			$ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
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
			curl_setopt($ch, CURLOPT_ENCODING , "gzip");
            $content = curl_exec($ch); // run the whole process
			$curl_info= curl_getinfo($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($content, 0, $header_size);
			$body = substr($content, $header_size);
			$response['curl_info']=$curl_info;
			$response['header']=$header;
			return	$response;
			
		}	
		
		
		
		
		
		
	function get_meta_tag($html){
	
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$nodes = $doc->getElementsByTagName('title');
		
		if(isset($nodes->item(0)->nodeValue))
			$title = $nodes->item(0)->nodeValue;
		else
			$title="";
		
		$response=array();
		$response['title']=$title;

		$metas = $doc->getElementsByTagName('meta');

		for ($i = 0; $i < $metas->length; $i++)
		{
			$meta = $metas->item($i);
			if($meta->getAttribute('name')!='')
				$response[$meta->getAttribute('name')] = $meta->getAttribute('content');
		}

		return $response;
	}

	
		
function strip_html_tags( $text )
{
	// PHP's strip_tags() function will remove tags, but it
	// doesn't remove scripts, styles, and other unwanted
	// invisible text between tags.  Also, as a prelude to
	// tokenizing the text, we need to insure that when
	// block-level tags (such as <p> or <div>) are removed,
	// neighboring words aren't joined.
	$text = preg_replace(
		array(
			// Remove invisible content
			'@<head[^>]*?>.*?</head>@siu',
			'@<style[^>]*?>.*?</style>@siu',
			'@<script[^>]*?.*?</script>@siu',
			'@<object[^>]*?.*?</object>@siu',
			'@<embed[^>]*?.*?</embed>@siu',
			'@<applet[^>]*?.*?</applet>@siu',
			'@<noframes[^>]*?.*?</noframes>@siu',
			'@<noscript[^>]*?.*?</noscript>@siu',
			'@<noembed[^>]*?.*?</noembed>@siu',

			// Add line breaks before & after blocks
			'@<((br)|(hr))@iu',
			'@</?((address)|(blockquote)|(center)|(del))@iu',
			'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
			'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
			'@</?((table)|(th)|(td)|(caption))@iu',
			'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
			'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
			'@</?((frameset)|(frame)|(iframe))@iu',
		),
		array(
			' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
			"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
			"\n\$0", "\n\$0",
		),
		$text );

	// Remove all remaining tags and comments and return.
	return strip_tags( $text );
}

/****This is for counting utf-8 words***/
function mb_count_words($string) {
    preg_match_all('/[\pL\pN\pPd]+/u', $string, $matches);
    return count($matches[0]);
}


public function get_email($content){
        preg_match_all('/([\w+\.]*\w+@[\w+\.]*\w+[\w+\-\w+]*\.\w+)/is', $content, $results);
        return $results[1];
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




	
	
	public function content_analysis($content){
	
		$html = new simple_html_dom();
		$html->load($content);
		
		$response=array();			
		
		/****Get all meta tags *****/	
		
		$meta_tag_information=$this->get_meta_tag($content);
		
		/***Check meta robot****/
		
		if(isset($meta_tag_information['robots'])){
			if(stripos($meta_tag_information['robots'], "index") !== false){
				$blocked_by_meta_robot="No";
			}
				
			else if(stripos($meta_tag_information['robots'], "noindex") !== false)
				$blocked_by_meta_robot="Yes";
				
			else
				$blocked_by_meta_robot="No";
				
		}
		
		else{
			$blocked_by_meta_robot="No";
		}
		
		
		if(isset($meta_tag_information['robots'])){
			if(stripos($meta_tag_information['robots'], "follow") !== false)
				$nofollowed_by_meta_robot="No";
			else if(stripos($meta_tag_information['robots'], "nofollow") !== false)
				$nofollowed_by_meta_robot="Yes";
			else
				$nofollowed_by_meta_robot="No";
		}
		
		else{
			$nofollowed_by_meta_robot="No";
		}
		
		
		/*****Extract all headings *******/
		
		
		for($i=1;$i<=6;$i++){
		
			 $header_name="h{$i}";
			$header_name_result=array();
			
			$headers= $html->find($header_name);
			
			if(isset($headers)){
				foreach($headers as $header){
				 $header_name_result[] = $header->plaintext;
			 }
		  }
		 
		  $response[$header_name]=$header_name_result;
		  	
		}
		
// keyword research
		// get 
		$page_encoding =  mb_detect_encoding($content);

		if(isset($page_encoding))
		{
			$utf8_text = iconv( $page_encoding, "utf-8", $content );
			$raw_text = $utf8_text;
		} 
		else $raw_text = $content;


		$raw_text=$this->strip_html_tags($raw_text);
		$raw_text=str_replace("&nbsp;"," ",$raw_text);	

		$raw_text=str_replace("  "," ",$raw_text);		
		
		$total_number_of_words = str_word_count($raw_text);	
		
		$raw_text = preg_replace('~\h*\[(?:[^][]+|(?R))*+]\h*~', ' ', $raw_text); // replacing chars between brackets
		
		$raw_text = html_entity_decode( $raw_text, ENT_QUOTES, "UTF-8" ); /* Decode HTML entities */
		// keeping raw text into a different variable $raw_text_for_2_words for phrase keyword extract
		$raw_text_for_2_words = $raw_text;

		$punc_marks = array('!','@','#','$','%','^','&','*','-','+','/','"',':','|',',','.',';','(',')','{','}','[',']');	

			$raw_text = str_replace($punc_marks, "", $raw_text);

			$raw_text = preg_replace( "/\r|\n/", " ", $raw_text );

		// $raw_text = preg_replace('/[^A-Za-z0-9\-]/', " ", $raw_text); // deleting all special chars 
		$raw_text =  trim($raw_text); // trimming text

		$array_preposition = array(
"a's",'accordingly','again','allows','also','amongst','anybody','anyways','appropriate','aside',
'available','because','before','below','between','by', "can't",'certain','com','consider',
'corresponding','definitely','different',"don't",'each','else','et','everybody','exactly',
'fifth','follows','four','gets','goes','greetings','has','he', 'her','herein','him','how',"i'm",
'immediate','indicate','instead','it','itself','know','later','lest','likely','ltd', 'me','more','must',
'nd','needs','next','none','nothing','of','okay','ones','others','ourselves','own','placed','probably',
'rather','regarding','right','saying','seeing','seen','serious','she','so','something','soon',
'still',"t's",'th','that','theirs','there','therein',"they'd",'third','though','thus','toward',
'try','under','unto','used','value','vs','way',"we've","weren't",'whence','whereas','whether',"who's",
'why','within',"wouldn't","you'll",'yourself','able','across','against','almost','although',
'an','anyhow','anywhere', 'are','ask','away','become','beforehand','beside','beyond',
"c'mon",'cannot','certainly','come','considering','could','described','do','done',
'edu','elsewhere','etc','everyone','example','first','for','from','getting','going','had',"hasn't",
"he's",'here','hereupon','himself','howbeit',"i've",'in','indicated','into',"it'd",'just','known',
'latter','let','little','mainly','mean','moreover','my','near','neither','nine','noone','novel','off',
'old','only','otherwise','out','particular','please','provides','rd','regardless','said','says','seem',
'self','seriously', 'should','some','sometime','sorry','sub','take','than',"that's",'them',
"there's",'theres',"they'll",'this','three','to','towards','trying','unfortunately','up',
'useful','various','want','we','welcome','what','whenever','whereby','which','whoever',
'will','without','yes',"you're",'yourselves','about','actually',"ain't",'alone','always', 'and','anyone',
'apart',"aren't",'asking','awfully','becomes','behind','besides','both',"c's",'cant','changes','comes',
'contain',"couldn't",'despite','does','down','eg','enough','even','everything','except', 'five',
'former','further','given','gone',"hadn't",'have','hello',"here's",'hers','his','however',
'ie','inasmuch','indicates','inward',"it'll",'keep','knows','latterly',"let's",'look','many','meanwhile',
'most','myself','nearly','never','no','nor','now','often','on', 'onto','ought','outside','particularly',
'plus','que','re','regards','same','second','seemed','selves', 'seven',"shouldn't",'somebody',
'sometimes','specified','such','taken', 'thank','thats','themselves','thereafter','thereupon',"they're",
'thorough','through','together','tried','twice','unless','upon','uses','very','wants',"we'd",
'well',"what's",'where','wherein','while','whole','willing',"won't",'yet',"you've",'zero',
'above','after','all','along','am','another','anything','appear','around','associated','be','becoming',
'being','best','brief','came','cause','clearly','concerning','containing','course','did',"doesn't",
'downwards','eight','entirely','ever','everywhere','far','followed','formerly','furthermore','gives',
'got','happens', "haven't",'help','hereafter','herself','hither',"i'd",'if','inc','inner', 'is',"it's",
'keeps','last','least','like','looking','may', 'merely','mostly','name','necessary','nevertheless',
'nobody','normally','nowhere','oh','once','or','our','over','per','possible','quite', 'really',
'relatively','saw','secondly', 'seeming','sensible','several','since','somehow','somewhat',
'specify','sup','tell','thanks','the','then','thereby','these',"they've",'thoroughly','throughout','too',
'tries','two','unlikely','us','using','via','was',"we'll",'went','whatever',"where's",'whereupon',
'whither','whom','wish','wonder','you','your','according','afterwards','allow','already','among','any',
'anyway','appreciate','as','at','became','been','believe','better','but','can', 'causes','co',
'consequently','contains', 'currently',"didn't",'doing','during', 'either','especially','every','ex',
'few','following','forth','get','go','gotten','hardly','having','hence', 'hereby','hi','hopefully',
"i'll",'ignored','indeed','insofar',"isn't",'its','kept','lately', 'less','liked','looks','maybe',
'might','much','namely','need','new','non','not','obviously','ok','one','other', 'ours','overall',
'perhaps','presumably', 'qv','reasonably','respectively','say','see', 'seems','sent','shall','six',
'someone','somewhere','specifying','sure','tends','thanx','their','thence','therefore',
'they','think','those','thru','took','truly','un', 'until','use','usually','viz',"wasn't","we're",
'were','when','whereafter','wherever','who','whose','with','would',"you'd",'yours','a','b','c','d','e',
'f','g','h','i','j','k','l','m','n', 'o','p','q','r','s','t','u','v','w','x','y','z','A','B','D','E',
'F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','provide','1','2','3','4','5','6','7','8','9','0','1st',
'2nd','3rd','000','10',0,'11'
);

/****** Get one word Keyword *****/
		// uppercasing $array_preposition values for delete from final array
		
		$one_keyword=array();
		
		$array_uppercase = array();
		foreach ($array_preposition as $value) $array_uppercase[] = ucfirst($value);	
		
		$sample_array = explode(" ", $raw_text);  // exploding raw text into array

		$sample_array = array_map('trim', $sample_array);
		
		$sample_array = array_filter($sample_array); // deleting blank values
		
		$sample_array = array_slice($sample_array, 0);  // recreating index for no gap in array


		// deleting stop words and prepositions from array
		$final_array_first_diff = array_udiff($sample_array, $array_preposition,'strcasecmp');

		$one_keyword_filter=array();
		foreach ($final_array_first_diff as $w) {
			
				preg_match("#\d*#", $w,$matches);
				if(empty($matches[0]))
					$one_keyword_filter[]=$w;

		}

		// creating an array of keywords as key and its occurence as value
		$one_keyword = array_count_values($one_keyword_filter);
		
		arsort($one_keyword); // sorting from top to bottom 
		
		$one_keyword = array_slice($one_keyword, 0,20); // reduece array to 20 elements 

	$two_keyword=array();
	
	$number_of_words =$this->mb_count_words($raw_text); // find the number of total words in raw text

	$word = explode(" ",$raw_text); 	// exploding raw text to an array of words

	$word = array_map('trim', $word);

	$sample_array_2_words = $word; 

	$sample_array_2_words = array_filter($sample_array_2_words); 	// filter array
	
	$sample_array_2_words = array_slice($sample_array_2_words, 0);	// slicing array	
	
	$half = 2; // length of phrase
    
	for($i = 0; $i < $number_of_words - 1 ; $i++) // first for loop for total number of words
	{	
		$ingram=""; // a blank string		
		
		for($j=$i; $j < $half+$i; $j++) // 2nd for loop for creating all the phrases
		{
			if(isset($sample_array_2_words[$j]))
				$ingram = $ingram." ".$sample_array_2_words[$j];			
		}		

		if($ingram!="")	
			$two_keyword[]=$ingram;		// saving phrases to an array
	}

		$two_keyword = array_count_values($two_keyword);
		arsort($two_keyword);
		$two_keyword = array_slice($two_keyword, 0,20);  // reduce array to first 20 elements

	/****** Three Words ********/

	// $half=(int) count($word)/2; 
	
	$three_keyword=array();
	
		$half = 3;

		for($i = 0; $i < $number_of_words - 1 ; $i++)
		{	
			$ingram="";
			
			for($j=$i; $j < $half+$i; $j++)
			{
				if(isset($sample_array_2_words[$j]))
					$ingram = $ingram." ".$sample_array_2_words[$j];			
			}
			if($ingram!="")	
				$three_keyword[]=$ingram;		
		}
	
		
	 $three_keyword = array_count_values($three_keyword);
	 arsort($three_keyword);
	 $three_keyword = array_slice($three_keyword, 0,20);

	/***** Get 4 phrase keyword ***********/

	// $half=(int) count($word)/2; 
	$four_keyword=array();
	$half = 4;
	for($i = 0; $i < $number_of_words - 1 ; $i++)
	{	
		$ingram="";
		for($j=$i; $j < $half+$i; $j++)
		{
			if(isset($sample_array_2_words[$j]))
				$ingram = $ingram." ".$sample_array_2_words[$j];			
		}
		if($ingram!="")		
			$four_keyword[]=$ingram;		
	}

	$four_keyword = array_count_values($four_keyword);
	arsort($four_keyword);
	$split_word = array_slice($four_keyword, 0,20);

		$response['meta_tag_information']=$meta_tag_information;
		$response['blocked_by_meta_robot']=$blocked_by_meta_robot;
		$response['nofollowed_by_meta_robot']=$nofollowed_by_meta_robot;
		
		$response['one_phrase']=$one_keyword;
		$response['two_phrase']=$two_keyword;
		$response['three_phrase']=$three_keyword;
		$response['four_phrase']=$split_word;
		
		$response['total_words']=$total_number_of_words;

		return $response;	 
		
	}
	
	
	function link_statistics($content,$url){
		
		$internal_link_count=0;
		$external_link_count=0;
		$nofollow_link_count=0;
		$dofollow_link_count=0;
		
		$nofollow_internal_link=array();
		
		$internal_link=array();
		$external_link=array();
		
		$analyzed_url_domain=get_domain_only($url);
		preg_match_all("#<a(.*?)>#si",$content,$links);
		$i=0;
		
		$links[1]=array_unique($links[1]);
		
		foreach($links[1] as $link_info){
			 
			preg_match('#href=[",\'](.*?)[",\']#',$link_info,$matches);
			$link=isset($matches[1])?$matches[1]:"";
			$link=trim($link, "'");
			
			/********/
			if($link=="" || substr($link, 0, 1) == '#' || stripos($link,"javascript:")!==FALSE || stripos($link,"tel:")!==FALSE){
				continue;
			}
			
			$link_domain=get_domain_only($link);
			
			/**** If domain is get as web page, then simply it is internal link ***/
			
			if(is_web_page($link_domain) || $link_domain==$analyzed_url_domain || $link_domain==""){
			
				$internal_link_count++;
				$internal_link[$i]['link']=$link;
				if(stripos($link_info,"nofollow")!==FALSE){
					$nofollow_link_count++;
					$internal_link[$i]['type']="nofollow";
					$nofollow_internal_link[]=$link_info;
				}
				else{
					$dofollow_link_count++;
					$internal_link[$i]['type']="dofollow";
				}	
			}
			
			else{
			
				$external_link_count++;
				$external_link[$i]['link']=$link;
				
				if(stripos($link_info,"nofollow")!==FALSE){
					$nofollow_link_count++;
					$external_link[$i]['type']="nofollow";
				}
				else{
					$dofollow_link_count++;
					$external_link[$i]['type']="dofollow";
				}	
				
			}
			
			$i++;
		}
		
		
		$response=array();
		
		$response['external_link_count']=$external_link_count;
		$response['internal_link_count']=$internal_link_count;
		$response['nofollow_count']=$nofollow_link_count;
		$response['do_follow_count']=$dofollow_link_count;
		$response['external_link']=$external_link;
		$response['internal_link']=$internal_link;
		$response['nofollow_internal_link']=$nofollow_internal_link;
		
		return $response;
		
		
	}


	function site_statistic_check($url){
		
		/**** Get Sites General Content ******/
		$warning_count=0;
		$success_count=0;
		
	
		$analysis_response=array();
		
		$general_content_information= $this->get_general_content($url);
		$general_content= $general_content_information['content'];
		$meta_information= $this->content_analysis($general_content);
		
		$step_count=$this->CI->session->userdata('health_check_count');
		if($step_count=="") $step_count=0;
		
		$analysis_response['title']= isset($meta_information['meta_tag_information']['title'])?	$meta_information['meta_tag_information']['title']: "";
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['description']= isset($meta_information['meta_tag_information']['description'])?	$meta_information['meta_tag_information']['description']:"";	
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['meta_keyword']= isset($meta_information['meta_tag_information']['keywords'])? $meta_information['meta_tag_information']['keywords']:"" ;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['viewport']=isset($meta_information['meta_tag_information']['viewport'])? $meta_information['meta_tag_information']['viewport']:"" ;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h1']= $meta_information['h1'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h2']= $meta_information['h2'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h3']= $meta_information['h3'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h4']= $meta_information['h4'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h5']= $meta_information['h5'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['h6']= $meta_information['h6'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['noindex_by_meta_robot']= $meta_information['blocked_by_meta_robot'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['nofollowed_by_meta_robot']= $meta_information['nofollowed_by_meta_robot'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['keyword_one_phrase']= $meta_information['one_phrase'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['keyword_two_phrase']= $meta_information['two_phrase'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['keyword_three_phrase']= $meta_information['three_phrase'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['keyword_four_phrase']= $meta_information['four_phrase'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		$analysis_response['total_words']= $meta_information['total_words'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		
		
		/**	Robot.txt check	***/
		$robot_txt_info=$this->get_general_content($url."/robots.txt");
		if($robot_txt_info['curl_info']['http_code']==200)
			$analysis_response['robot_txt_exist']=1;
		else
			$analysis_response['robot_txt_exist']=0;
			
		$analysis_response['robot_txt_content']=$robot_txt_info['content'];
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		

		
		/***Sitemap check***/
		
		/*****	First Check in the robot.txt	****/
		$sitemap_location="";
		
		if($analysis_response['robot_txt_content']!=""){
			
			$robot_content_explode=explode("\n",$analysis_response['robot_txt_content']);
			
			foreach($robot_content_explode as $content_segment){
				$content_segment_explode=explode(":",$content_segment,2);
				
				if(!isset($content_segment_explode[0]))
					$content_segment_explode[0]="";
				
				if(stripos($content_segment_explode[0],"Sitemap")!==FALSE){
				
					$analysis_response['sitemap_exist']=1;
					$sitemap_location=isset($content_segment_explode[1]) ? $content_segment_explode[1]:"";
					break;
				}
					
			}		
		}
		

		if($sitemap_location==""){
			$sitemap_location=$url."/sitemap.xml";
		
		$site_map_info	=	$this->get_general_content($sitemap_location);
		if($site_map_info['curl_info']['http_code']==200)
			$analysis_response['sitemap_exist']=1;
		else
			$analysis_response['sitemap_exist']=0;	
		}
		
		$analysis_response['sitemap_location']=$sitemap_location;
		
		
		
		/***Link Statistic and SEO Friendly URL Checker****/
		$link_statistic	= $this->link_statistics($general_content,$url);
		
		$analysis_response['external_link_count']=$link_statistic['external_link_count'];
		$analysis_response['internal_link_count']=$link_statistic['internal_link_count'];
		$analysis_response['nofollow_link_count']=$link_statistic['nofollow_count'];
		$analysis_response['dofollow_link_count']=$link_statistic['do_follow_count'];
		$analysis_response['external_link']=$link_statistic['external_link'];
		$analysis_response['internal_link']=$link_statistic['internal_link'];
		$analysis_response['nofollow_internal_link']=$link_statistic['nofollow_internal_link'];
		
		foreach ($analysis_response['internal_link'] as $internal_link){
			$pos=strpos($internal_link['link'],"_");
			
			if($pos!==FALSE){
				$analysis_response['not_seo_friendly_link'][]=$internal_link['link'];
			}
		}
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		
		
		/*****	Image without ALT Text *****/
		$doc = new DOMDocument();
		@$doc->loadHTML($general_content);
		$imgs = $doc->getElementsByTagName('img');
		$img_not_alt_set=0;
		$img_not_alt_set_src=array();
		$img_alt_set=0;
		$img_alt_set_src=array();
		
		foreach($imgs as $img){
				$alt=$img->getAttribute('alt');
				if($alt){
					$img_alt_set++;
					$img_alt_set_src[]=$img->getAttribute('src');
				}		
				else{
					$img_not_alt_set++;
					$img_not_alt_set_src[]=$img->getAttribute('src');
				}
		}
		
		$analysis_response['image_without_alt_count']	=  $img_not_alt_set;
		$analysis_response['image_not_alt_list']		=  $img_not_alt_set_src;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		/*****	Inline CSS Test ******/
			
		$html = new simple_html_dom();
		$html->load($general_content);
		
		$style_element = $html->find('*[style]'); 
		$inline_style=array();
		
		foreach($style_element as $r){
			$r->innertext="";
			$inline_style[]=htmlspecialchars($r->outertext);
		}
		
		$analysis_response['inline_css']= $inline_style;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		
		
		/****	Internal CSS ***/
		
		$style_element = $html->find('style'); 
		$internal_style=array();
		
		foreach($style_element as $r){
			/*$r->innertext="";*/
			$internal_style[]=htmlspecialchars($r->outertext);
		}
		
		$analysis_response['internal_css']= $internal_style;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		
		/*****	Depreciated Tag ****/
		$depreciated_tag_list=array("applet","basefont","center","dir","font","isindex","menu","s","strike","u");
		$depreciated_tag_list_exist=array();
		
		foreach($depreciated_tag_list as $tag){
			$depreciated	= $html->find($tag); 
			$depreciated_tag_list_exist[$tag]=count($depreciated);
		}
		
		$analysis_response['depreciated_html_tag']=$depreciated_tag_list_exist;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		
		/***************************************Favicon Test**************************************************************/
		
		$favicon_element = $html->find('link[rel=shortcut icon]'); 
		$favicon_exists=0;
		$favicon_link="";
		foreach($favicon_element as $r){
			$favicon_exists=1;
			$favicon_link=$r->href;
		}
		
		$analysis_response['is_favicon_found']=$favicon_exists;
		$analysis_response['favicon_link']=$favicon_link;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		
		/********************************Paze Size and GZIP Size *************************************************/
		
		
		$gzip_response	= $this->get_gzip_response($url);
		
		$gzip_curl_info= $gzip_response['curl_info'];
		
		$gzip_header= $gzip_response['header'];
		$gzip_header_array = explode("\r\n", $gzip_header);
		foreach ($gzip_header_array as $row)
				{
				    $cutRow = explode(":", $row, 2);
				    $gzip_headers[$cutRow[0]] = isset($cutRow[1])? trim($cutRow[1]):"";
				}
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


				
		/****************************** Total Page Size in Normal Mode *****************************************/	
		
		$total_page_size=$general_content_information['curl_info']['size_download']/1024; 
		
		$gzip_page_size=$gzip_curl_info['size_download']/1024; 
		
		/***If Gzip Support is enabled or not**/
		
		 $gzip_enable=0;
		 $gzip_headers['Content-Encoding']=isset($gzip_headers['Content-Encoding'])?$gzip_headers['Content-Encoding']:"";
		 
		 $gzip_pos=stripos($gzip_headers['Content-Encoding'], "gzip");
         if ($gzip_pos!==false) {
            $gzip_enable=1;
         }
			
		$analysis_response['total_page_size_general']=$total_page_size;
		$analysis_response['page_size_gzip']=$gzip_page_size;
		$analysis_response['is_gzip_enable']=$gzip_enable;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		
		
			
		/*********************************************Doctype test**************************************************/
		
		
		$doctype_element = $html->find('unknown'); 
		$doctype="";
		$doctype_is_exist=0;
		
		foreach($doctype_element as $r){
			$doctype_pos=stripos($r,'!doctype');
			if($doctype_pos!==FALSE){
				$doctype_is_exist=1;
				$doctype=htmlspecialchars($r);
				break;
			}
		}
		
		$analysis_response['doctype']=$doctype;
		$analysis_response['doctype_is_exist']=$doctype_is_exist;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		/****** No follow tag checker ******/
		
		$nofollow_link_element=$html->find('*[rel=nofollow]');
		$nofollow_link_list=array();
		foreach($nofollow_link_element as $l){
			$nofollow_link_list[]=htmlspecialchars($l);
		}
		
		$analysis_response['nofollow_link_list']=$nofollow_link_list;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		
		
		/*** Canonical Tag Checker ***/
		
		$canonical_link_element=$html->find('*[rel=canonical]');
		$canonical_link_list=array();
		foreach($canonical_link_element as $l){
			$canonical_link_list[]=htmlspecialchars($l);
		}
		
		$analysis_response['canonical_link_list']=$canonical_link_list;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		/**** Noindex tag checker 
			<meta name="robots" content="noindex">
				To prevent only Google web crawlers from indexing a page:
			<meta name="googlebot" content="noindex"> 
		*****/
		
		$noindex_element=$html->find('*[content=noindex]');
		$noindex_list=array();
		foreach($noindex_element as $l){
			$noindex_list[]=htmlspecialchars($l);
		}
		
		$analysis_response['noindex_list']=$noindex_list;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		
		/***	Micro Data Schema Test	****/		
		$schema_element=$html->find('*[itemtype]');
		$schema_list=array();
		foreach($schema_element as $l){
			$schema_list[]=htmlspecialchars($l);
		}		
		$analysis_response['micro_data_schema_list']=$schema_list;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);


		
		/**********DNS Record IPV6 Check ***************/
		
		//$dns_result = @dns_get_record($url,DNS_ALL);
		$url_parse= @parse_url($url);
  		$url_hostname=$url_parse['host'];
		$dns_result = @dns_get_record($url_hostname,DNS_A + DNS_CNAME + DNS_MX + DNS_NS + DNS_AAAA + DNS_A6);	
		
		$ipv6_support=0;
		$ipv6="";
		$site_ip="";
		
		if(!is_array($dns_result))
			$dns_result=array();
		
		foreach($dns_result as $dns_rec){
			if($dns_rec['type']=='AAAA'){
				$ipv6_support=1;
				$ipv6=$dns_rec['ipv6'];
			}
			
			if($dns_rec['type']=='A'){
				$site_ip=$dns_rec['ip'];
			}
		}		
		
		$analysis_response['is_ipv6_compatiable']=$ipv6_support;
		$analysis_response['ipv6']=$ipv6;
		$analysis_response['ip']=$site_ip;		
		$analysis_response['dns_report']=$dns_result;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		
		
		
		/*****For Canonicalization test of IP******/
		$ip_response=$this->get_general_content($site_ip);
		$ip_url=$this->clean_domain_name($ip_response['curl_info']['url']);
		
		$ip_canonical=0;
		
		if($site_ip==$ip_url)
			$ip_canonical=0;
		else
			$ip_canonical=1;
			
		$analysis_response['is_ip_canonical']=$ip_canonical;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
	
	
		/**** Email Found and email list ****/
		
		$email_list=array();
		$email_list=$this->get_email($general_content);
		
		$analysis_response['email_list']= $email_list;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);

		

		/***Url Canonicaliztion test **/	
		$url_without_http=$this->clean_domain_name($url);
		
		$www_pos=strpos($url,'www.');
		
		if($www_pos!==FALSE)
			$canonicalization_test_url=$url_without_http;
		else
			$canonicalization_test_url="www.".$url_without_http;
	
		$canonicalization_test_url;		
		$canonicalization_content= $this->get_general_content($canonicalization_test_url);
			
		if($general_content_information['curl_info']['url']== $canonicalization_content['curl_info']['url'])
			$url_canonicalization=1;
		else
			$url_canonicalization=0;
		
		$analysis_response['is_url_canonicalized']=$url_canonicalization;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
		
		

		/********HTML TO TEXT RATIO***********/		
		$html_text_length=mb_strlen($general_content,'UTF-8');
		$text_conent=$this->strip_html_tags($general_content);
		$text_content_lenght=mb_strlen($text_conent,'UTF-8');		
		$text_ration= @($text_content_lenght/$html_text_length)*100;		
		$analysis_response['text_to_html_ratio']=$text_ration;	
		
		$general_curl_response=$general_content_information['curl_info'];
		$analysis_response['general_curl_response']=$general_curl_response;
		$step_count++;
		$this->CI->session->set_userdata('health_check_count',$step_count);
			
		
		
		
		
		/***********	Warning Error Count ***************/
		
		/**	Title **/
		$warning_count	+= $this->title_check($analysis_response['title']);
		
		/****	Description **/
		$warning_count += $this->description_check($analysis_response['description']);
		
		
		/***** 	Meta Tag Uses	******/
		
		$warning_count	+= $this->keyword_usage_check($analysis_response['meta_keyword'],$analysis_response['keyword_one_phrase'],$analysis_response['keyword_two_phrase'],$analysis_response['keyword_three_phrase'],$analysis_response['keyword_four_phrase']);
		
		
		if(empty($analysis_response['meta_keyword']))
			$warning_count++;

		if(empty($analysis_response['h1']))
			$warning_count++;
		
		if(empty($analysis_response['h2']) || count($analysis_response['h2'])>10)
			$warning_count++;
			
			
		/**Robot.txt check**/
		
		
		if(!$analysis_response['robot_txt_exist'])
			$warning_count++;
			
		/***Site Map Check ****/
		if(!$analysis_response['sitemap_exist'])
			$warning_count++;
			
		/***SEO Friendly Url Check***/
		
		if(isset($analysis_response['not_seo_friendly_link']) && count($analysis_response['not_seo_friendly_link'])==0)
			$warning_count++;
		
		if($analysis_response['image_without_alt_count']>0)
			$warning_count++;
		
		if(count($analysis_response['inline_css'])>0)
			$warning_count++;
		
		if(count($analysis_response['internal_css'])>0)
			$warning_count++;
		
			
		/***	Site Loading Time  ***/
		
		if($analysis_response['general_curl_response']['total_time']>5)
			$warning_count++;
			
		$warning_count +=$this->depereciated_tag_check($analysis_response['depreciated_html_tag']);
		
		/** Favicon warning test ***/
		
		if(!$analysis_response['is_favicon_found'])
			$warning_count++;
			
		/***HTML Page Size*****/
		if($analysis_response['page_size_gzip']>33)
			$warning_count++;
		
		if(!$analysis_response['is_gzip_enable'])
			$warning_count++;
		
		if(!$analysis_response['doctype_is_exist'])
			$warning_count++;
			
		if(count($analysis_response['micro_data_schema_list'])==0)
			$warning_count++;
			
		if(!$analysis_response['is_ipv6_compatiable'])
			$warning_count++;
		
		
		if(!$analysis_response['is_ip_canonical'])
			$warning_count++;
		
		if(!$analysis_response['is_url_canonicalized'])
			$warning_count++;
			
		if(count($analysis_response['email_list'])==0)
			$warning_count++;	

		if(round($analysis_response['text_to_html_ratio'])<20)
			$warning_count++;	
			
			
	 	$analysis_response['warning_count']	= $warning_count;
	 
	
		return $analysis_response;
		
	}
	
	
	
	function title_check($title)
	{
			
		$title_lenght	=	mb_strlen($title);
		
		if($title_lenght>60 || $title_lenght==0) 
			return 1;
		else
			return 0;
			
	}

	function description_check($description)
	{
		$description_lenght=mb_strlen($description);
		if($description_lenght>150 || $description_lenght==0)		
			return 1;
		else
			return 0;
	}
	
	function keyword_usage_check($meta_keyword,$one,$two,$three,$four)
	{		

		$one=array_keys($one);
		$two=array_keys($two);
		$three=array_keys($three);
		$four=array_keys($four);

		$meta_keyword_array=explode(",",$meta_keyword);
		$one=array_slice($one,0,5);
		
		$one_intersect	=	array_intersect($meta_keyword_array,$one);
		
		if(!empty($one_intersect))
			return 0;
		
		$two_intersect	=	array_intersect($meta_keyword_array,$two);
		
		if(!empty($two_intersect))
			return 0;
			
		$three_intersect	=	array_intersect($meta_keyword_array,$three);
		
		if(!empty($three_intersect))
			return 0;
			
		$four_intersect	=	array_intersect($meta_keyword_array,$four);
		
		if(!empty($four_intersect))
			return 0;
		
		return 1; 
		
	}
	
	
	function depereciated_tag_check($tags_array){
	
		foreach($tags_array as $i=>$v){
			if($v>0)
				return 1;
		}
	
	return 0; 
		
	}



	
	
}

?>