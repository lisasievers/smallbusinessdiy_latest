<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */

//Enable SEO ADDON TOOLS
define('SEO_ADDON_TOOLS', true);

//Load Meta Tags
$p_title = $data['meta_title'];
$des = $data['meta_des'];
$keyword = $data['meta_tags'];
$data['about_tool'] = htmlspecialchars_decode($data['about_tool']);

//Tool Path
$toolURL = $baseURL.$data['tool_url'];
$toolOutputURL = $baseURL.$data['tool_url'].'/output';
    
//Load Capthca
$toolCap = false;
$query =  "SELECT * FROM capthca where id='1'";
$result = mysqli_query($con,$query);
       	 	 	 	   
while($row = mysqli_fetch_array($result)) {
$color =  Trim($row['color']);
$mode =   Trim($row['mode']);
$mul =  Trim($row['mul']);
$allowed =   Trim($row['allowed']);
$cap_c = Trim($row['cap_c']);
$cap_e = filter_var(Trim($row['cap_e']), FILTER_VALIDATE_BOOLEAN);
}
$isCap = filter_var(Trim($data['captcha']), FILTER_VALIDATE_BOOLEAN);
if ($isCap){ 
    $toolCap = true;
}
else {
    if ($cap_e) {  
    $toolCap = true;
    }
}

//Check Image Verification
if($toolCap){
if ($pointOut != 'output') {
    //Generate Image Verification
    $_SESSION['captcha'] = elite_captcha($color,$mode,$mul,$allowed);  
    $captchaCode = '<center id="capCode">
            <h4>'.$lang['7'].'</h4>
            <img id="capImg" src="' . $_SESSION['captcha']['image_src'] . '" alt="Captcha" class="imagever" style="border: 2px solid #ffffff !important;">
            
            <div class="input-group" style="width: 20% !important;">
              <input type="text" class="form-control" id="scode" name="scode" style="box-shadow: none !important;">
              <span onclick="reloadCap()" class="input-group-addon" style="cursor: pointer;"><i class="fa fa-refresh"></i></span>
            </div>
            <style>.fa.fa-refresh:hover {  transform: rotate(90deg); }.fa.fa-refresh { transition: transform 0.5s ease 0s; }</style>
             </center>
            <br>';  
}else{
    //Verify Image Verification
    if(isset($_POST['scode'])) {
    $scode = strtolower(raino_trim($_POST['scode']));
    $cap_code = strtolower($_SESSION['captcha']['code']);
    if ($cap_code != $scode) {
    $error = $lang['5']; // "Your image verification code is wrong!";
    }
    }else{
    die($lang['4']);  //Malformed Request!
    }  
    }
}

//PR01 - Article Rewriter
if($toolUid == 'PR01') {
    $controller = 'output'.D_S.'article_rewriter';
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $userInput = stripslashes($_POST['data']);
        $regUserInput = truncate($userInput,30,150);
        $spin=new spin_my_data;
        $spinned=$spin->spinMyData($userInput,'en');
        $spinned_data=$spin->randomSplit($spinned); 
        }
    }
}
//PR02 - Plagiarism Checker
elseif($toolUid == 'PR02') {
    $controller = 'output'.D_S.'plagiarism_checker';
    
    $query =  "SELECT * FROM pr02 where id='1'";
    $result = mysqli_query($con,$query);
    $resArr = mysqli_fetch_array($result);
    extract($resArr);

    if ($pointOut == 'output') {
        die($lang['4']); //Malformed Request!
    }
}
//PR03 - Backlink Maker
elseif($toolUid == 'PR03') {
    
    $controller = 'output'.D_S.'backlink_maker';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
}
//PR04 - Meta Tag Generator
elseif($toolUid == 'PR04') {
    $controller = 'output'.D_S.'meta_tag_generator';
    
    if ($pointOut == 'output') {
    if (!isset($_POST['keywords']) && !isset($_POST['description']))
    die($lang['4']); //Malformed Request!
   	$metaTitle = raino_trim($_POST['title']);
   	$metaDescription = raino_trim($_POST['description']);
    $metaKeywords = raino_trim($_POST['keywords']);
	$robotsIndex = raino_trim($_POST['robotsIndex']);
	$robotsLinks = raino_trim($_POST['robotsLinks']);
	$contentType = raino_trim($_POST['contentType']);
	$metaLang = raino_trim($_POST['language']); 
	$revisitdays = raino_trim($_POST['revisitdays']);
	$authorname = raino_trim($_POST['authorname']); 
	
    $checkRevisit = raino_trim($_POST['revisit']);
    $checkRevisit = filter_var($checkRevisit, FILTER_VALIDATE_BOOLEAN);
	$checkAuthor = raino_trim($_POST['author']); 
    $checkAuthor = filter_var($checkAuthor, FILTER_VALIDATE_BOOLEAN);
        
    $outData = genMeta($metaTitle,$metaDescription,$metaKeywords,$robotsIndex,$robotsLinks,$contentType,$metaLang,$revisitdays,$authorname,$checkRevisit,$checkAuthor);
    }
}
//PR05 - Meta Tags Analyzer
elseif($toolUid == 'PR05') {
    
    $controller = 'output'.D_S.'meta_tags_analyzer';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $my_url = "http://".clean_url(raino_trim($_POST['url']));
        if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
        $error = $lang['327'];
        }else {
        $regUserInput = $my_url;
        $my_url = parse_url($my_url);
        $myUrl = $my_url['host'];
        $my_url = "http://www.$myUrl";
        $arr_meta = getMyMeta($my_url);
        $site_title  = $arr_meta[0];
        $site_description = $arr_meta[1];
        $site_keywords = $arr_meta[2];
        $myUrl = ucfirst($myUrl);
        }
        }
    }
    
}
//PR06 - Keyword Position Checker
elseif($toolUid == 'PR06') {
    
    $controller = 'output'.D_S.'keyword_position';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    } 
}
//PR07 - Robots.txt Generator
elseif($toolUid == 'PR07') {
    
    $controller = 'output'.D_S.'robots_generator';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }     
}
//PR08 - XML Sitemap Generator
elseif($toolUid == 'PR08') {

    $controller = 'output'.D_S.'xml_sitemap';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
}
//PR09 - Backlink Checker
elseif($toolUid == 'PR09') {

    $controller = 'output'.D_S.'backlink_checker';

    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst(str_replace('www.','',$host));
            $alexa = alexaRank($host);
            $alexa_back = $alexa[3];
            $google_back = googleBack($host);
            $bing_back = bingBack($host);
            }
      }
      }
}
//PR10 - Alexa Rank Checker
elseif($toolUid == 'PR10') {
    
    $controller = 'output'.D_S.'alexa_rank_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_url($my_url); $my_url = "http://www.$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url(Trim($my_url));
            $host = $my_url['host'];
            $myHost = ucfirst(str_replace('www.','',$host));
            $alexa = alexaRank($host);
            $alexa_rank = $alexa[0];
            $alexa_pop = $alexa[1];
            $regional_rank = $alexa[2];
            $alexa_back = $alexa[3];
            }
        }
    }
    
}
//PR11 - Word Counter
elseif($toolUid == 'PR11') {

    $controller = 'output'.D_S.'word_counter';

    if ($pointOut == 'output') {
        die($lang['4']); //Malformed Request!
    }
}
//PR12 - Online Blog Ping Website Tool
elseif($toolUid == 'PR12') {
        
    $controller = 'output'.D_S.'blog_ping_tool';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
}
//PR13 - Link Analyzer
elseif($toolUid == 'PR13') {
    
    $controller = 'output'.D_S.'link_analyzer';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = Trim("http://$my_url");
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $uriData=doLinkAnalysis($my_url);	
            $internal_links = $uriData[0];
            $internal_links_count = $uriData[1];
            $internal_links_nofollow = $uriData[2];
            $external_links = $uriData[3];
            $external_links_count = $uriData[4];
            $external_links_nofollow = $uriData[5];
            $total_links = $uriData[6]; 
            $total_nofollow_links = (int)$internal_links_nofollow + (int)$external_links_nofollow;
            }
        }
    }
    
}
//PR14 - PageRank Checker
elseif($toolUid == 'PR14') {

    $controller = 'output'.D_S.'pageRank_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $userInput = raino_trim($_POST['data']);
        $regUserInput = truncate($userInput,30,150);
        $arrayLinks = explode("\n", $userInput);
        $count = 0;
        foreach ($arrayLinks as $url) {
            $url = clean_with_www($url); $url = Trim("http://$url");
            if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $count++;
            $url = parse_url(Trim($url));
            $host = $url['host'];
            $myHost[] = ucfirst(str_replace('www.','',$host));
            $rank = google_page_rank($host);
            $pr[] = ($rank == '' ? '0' : $rank);
            }
        }
        }
    }
    
}
//PR15 - My IP Address
elseif($toolUid == 'PR15') {

    $controller = 'output'.D_S.'my_ip_address';
    
        if ($pointOut == 'output')
        die($lang['4']); //Malformed Request!
        
        if (!isset($error)) {
            $ip = getUserIP();
            $ip_info = getMyGeoInfo($ip,$item_purchase_code);
            $city = $ip_info[0];
            $region = $ip_info[1];
            $country = $ip_info[2];
            $country_code = $ip_info[3];
            $isp = $ip_info[4];
            $latitude = $ip_info[5];
            $longitude = $ip_info[6];
        }
}
//PR16 - Keyword Density Checker
elseif($toolUid == 'PR16') {
        
    $controller = 'output'.D_S.'keyword_density_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $my_url = "http://".clean_with_www(raino_trim($_POST['url']));
        if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
        $error = $lang['327'];
        }else {
        $regUserInput = $my_url;
        $my_url = parse_url($my_url);
        $myUrl = $my_url['host'];
        
        $obj = new KD();
        $obj->domain = 'http://'.$myUrl;
        $resdata = $obj->result(); 

        foreach($resdata as $outData){
            $outData['keyword'] = Trim($outData['keyword']);
            if($outData['keyword'] != null || $outData['keyword'] != "") {
                
                $blockChars = array('~','=','+','?',':','_','[',']','"','.','!','@','#','$','%','^','&','*','(',')','<','>','{','}','|','\\','/',',');
                $blockedStr = false;
                foreach($blockChars as $blockChar){
                    if(str_contains($outData['keyword'],$blockChar))
                    {
                        $blockedStr = true;
                        break;
                    }
                }
                //if (ctype_alnum($outData['keyword'])) {
                if (!preg_match('/[0-9]+/', $outData['keyword'])){
                    if(!$blockedStr)
                    $outArr[] = array($outData['keyword'], $outData['count'], $outData['percent']);
                }   
             }
        }
        $outCount = count($outArr);
        if($outCount == 0){
            $error = $lang['183'];
        }
        $myUrl = ucfirst(str_replace('www.','',$myUrl));
        }
        }
    }
}
//PR17 - Google Malware Checker
elseif($toolUid == 'PR17') {
        $controller = 'output'.D_S.'google_malware';
    
    if ($pointOut == 'output') {
        die($lang['4']); //Malformed Request!
    }
}
//PR18 - Domain Age Checker
elseif($toolUid == 'PR18') {
    
    $controller = 'output'.D_S.'domain_age';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_url($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $whois= new whois;
            $site = $whois->cleanUrl($host);
            $whois_data = $whois->whoislookup($site);
            $domainAge = $whois_data[1];
            $createdDate = $whois_data[2];
            $updatedDate = $whois_data[3];
            $expiredDate = $whois_data[4];
            }
        }
    }
    
}
//PR19 - Whois Checker
elseif($toolUid == 'PR19') {
        
    $controller = 'output'.D_S.'whois_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $whois= new whois;
            $site = $whois->cleanUrl($host);
            $whois_data = $whois->whoislookup($site);
            $whoisData = $whois_data[0];
            }
        }
    }
    
}
//PR20 - Domain into IP
elseif($toolUid == 'PR20') {

    $controller = 'output'.D_S.'domain_ip';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_url($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $getHostIP = gethostbyname($host);
            $data_list = host_info($host);
            $domain_ip = $data_list[0];
            $domain_country =  $data_list[1];
            $domain_isp = $data_list[2];
            }
        }
    }
}
//PR21 - Dmoz Listing Checker
elseif($toolUid == 'PR21') {

    $controller = 'output'.D_S.'dmoz_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $outData = raino_trim($_POST['data']);
        $regUserInput = truncate($outData,30,150);
        $array = explode("\n", $outData);
        $count = 0;
        foreach ($array as $url) {
        $url = clean_with_www($url); $url = Trim("http://$url");
            if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
                $count++;
                $my_url[] = Trim($url);
                $url = parse_url(Trim($url));
                $host = $url['host'];
                $myHost[] = ucfirst(str_replace('www.','',$host));
                $dmozRes[] = dmozCheck($host,$lang['111'],$lang['112']);
            }
        }
        }
    }
    
}
//PR22 - URL Rewriting Tool
elseif($toolUid == 'PR22') {
    
    $controller = 'output'.D_S.'url_rewriting_tool';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $r_1 = $r_2 = $r_3 = "";    
        $my_url = "http://".clean_with_www(raino_trim($_POST['url']));
        if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
        $error = $lang['327'];
        }else {
        $regUserInput = $my_url;
        $arr = parse_url($my_url);
        $checkDyn = checkDyn($my_url);
        if ($checkDyn == '0') {
        $error = $lang['36']; //'URL entered does not seem to be a dynamic URL';        
        } else {
        $my_domain = clean_with_www($arr['host']);
        $example_url = $arr['scheme']."://".$arr['host'].$arr['path'];
        $arr_val = split_up_me($my_url);
        $filename= Trim($arr_val[0]);
        $f_without_e = Trim($arr_val[1]);
        $parsed_arg = $arr_val[2];
        $start = 1;
 
        $sht_url = str_replace($filename,"",$example_url);$sht_url=$sht_url.$f_without_e;
        $dht_ex_url = $dht_url = $sht_ex_url = $sht_url;
    
        foreach($parsed_arg as $argf => $value)
        {
            if ($start == 1){ $syb = "?";}else {$syb = "&";}
            $sht_url = $sht_url."-".$value[0]."-".$value[1];
            $dht_url = $dht_url."/".$value[0]."/".$value[1];
            $dht_ex_url = $dht_ex_url."/".$value[0]."/(Any Value)";
            $sht_ex_url = $sht_ex_url."-".$value[0]."-(Any Value)";
            $r_1 = $r_1."-$value[0]-(.*)";
            $r_2 = $r_2.$syb."$value[0]=$$start";
            $r_3 = $r_3."/$value[0]/(.*)";
            $start++;
        }
        $sht_url = Trim($sht_url).".htm";
        $dht_url = Trim($dht_url)."/";
        $sht_ex_url = Trim($sht_ex_url).".htm";
        $dht_ex_url = Trim($dht_ex_url)."/";
        $sht_data =  "Options +FollowSymLinks\r\nRewriteEngine on\r\nRewriteRule $f_without_e".Trim($r_1)."\.htm$ $filename".Trim($r_2);
        $dht_data = "Options +FollowSymLinks\r\nRewriteEngine on\r\nRewriteRule $f_without_e".Trim($r_3)."/ $filename".Trim($r_2)."\r\nRewriteRule $f_without_e".Trim($r_3)." $filename".Trim($r_2);
        }
      }
    }
    }
    
}
//PR23 - www Redirect Checker
elseif($toolUid == 'PR23') {
    
    $controller = 'output'.D_S.'redirect_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $url = raino_trim($_POST['url']);
            $myUrl = clean_with_www($url); $url = "http://$myUrl";
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $outData =  checkRedirect($url,$lang['179'],$lang['180']);
            }
        }
    }
    
}
//PR24 - Mozrank Checker
elseif($toolUid == 'PR24') {
    
    $controller = 'output'.D_S.'mozrank_checker';
    
    $query =  "SELECT * FROM pr24 where id='1'";
    $result = mysqli_query($con,$query);
    $resArr = mysqli_fetch_array($result);
    extract($resArr);
    
    if($moz_access_id == null || $moz_access_id== '')
    $error = $lang['209'];
    
    if($moz_secret_key == null || $moz_secret_key== '')
    $error = $lang['210'];
       
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst(str_replace('www.','',$host));
            $seoMoz = seoMoz($host,$moz_access_id,$moz_secret_key);
            $mozRank = $seoMoz[0];
            $pageAuth = $seoMoz[1];
            $domainAuth = $seoMoz[2];
            }
      }
      }
    
}
//PR25 - URL Encoder / Decoder
elseif($toolUid == 'PR25') {
    
    $controller = 'output'.D_S.'url_encoder_decoder';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $userInput = stripslashes($_POST['data']);
            $regUserInput = truncate($userInput,30,150);
            $out_data_e = urlencode($userInput);
            $out_data_d = urldecode($userInput);
        }
    }
}
//PR26 - Server Status Checker
elseif($toolUid == 'PR26') {
    
    $controller = 'output'.D_S.'server_status_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $userInput = raino_trim($_POST['data']);
        $regUserInput = truncate($userInput,30,150);
        $array = explode("\n", $userInput);
        $count = 0;
        foreach ($array as $url) {
            $url = clean_with_www($url); $url = Trim("http://$url");
            if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $count++;
            $my_url[] = Trim($url);
            $url = parse_url(Trim($url));
            $host = $url['host'];
            $myHost[] = ucfirst(str_replace('www.','',$host));
            $res = itIsOnline($host);
            $stats[] =($res[0] == true ? "Online" : "Offline");
            $response_time[] = $res[1]." Sec";
            $http_code[] = $res[2];
            }
        }
        }
    }
    
}
//PR27 - Webpage Screen Resolution Simulator
elseif($toolUid == 'PR27') {
    
        $controller = 'output'.D_S.'screen_simulator';
    
        if ($pointOut == 'output')
        die($lang['4']); //Malformed Request!
        
        if (!isset($error)) {
        
        }  
}
//PR28 - Page Size Checker
elseif($toolUid == 'PR28') {
    
    $controller = 'output'.D_S.'page_size_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $my_url = raino_trim($_POST['url']);
        $my_url = clean_with_www($my_url); 
        $my_url = Trim("http://$my_url");
        if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
        $error = $lang['327'];
        }else {
        $regUserInput = $my_url;
        $size = getPageSize($my_url);
        $kb_size = size_as_kb($size);
        $myUrl = ucfirst($my_url);
        }
        }
    }
    
}
//PR29 - Reverse IP Domain Checker
elseif($toolUid == 'PR29') {

    $controller = 'output'.D_S.'reverse_ip_domain';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $getHostIP = gethostbyname($host);
            $myHost = ucfirst(str_replace('www.','',$host));
            $revLink = reverseIP($getHostIP);
            $revCount = count($revLink);
            }
      }
      }
}
//PR30 - Blacklist Lookup
elseif($toolUid == 'PR30') {

    $controller = 'output'.D_S.'blacklist_lookup';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $getHostIP = gethostbyname($host);
            $myHost = ucfirst(str_replace('www.','',$host));
            $dataArr = dnsblookup($getHostIP);
            $outArr = $dataArr[0];
            $overAll = $dataArr[1];
            }
      }
      }
}
//PR31 - AVG Antivirus Checker
elseif($toolUid == 'PR31') {

    $controller = 'output'.D_S.'avg_antivirus';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $userInput = raino_trim($_POST['data']);
        $regUserInput = truncate($userInput,30,150);
        $array = explode("\n", $userInput);
        $count = 0;
        foreach ($array as $url) {
            $url = clean_with_www($url); $url = Trim("http://$url");
            if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $count++;
            $my_url[] = Trim($url);
            $url = parse_url(Trim($url));
            $host = $url['host'];
            $myHost[] = ucfirst(str_replace('www.','',$host));
            $stats = avgCheck($host);
            if($stats == '1') 
            $resOut[] = $lang['197'];
            if($stats == '2') 
            $resOut[] = $lang['198'];
            if($stats == '3') 
            $resOut[] = $lang['199'];
            if($stats == '0') 
            $resOut[] = $lang['200'];
            }
        }
        }
    }
    
}
//PR32 - Link Price Calculator
elseif($toolUid == 'PR32') {
        
    $controller = 'output'.D_S.'link_price_calculator';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $outData = raino_trim($_POST['data']);
        $regUserInput = truncate($outData,30,150);
        $array = explode("\n", $outData);
        $count = 0;
        foreach ($array as $url) {
        $url = clean_with_www($url); $url = Trim("http://$url");
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
        $count++;
        $my_url[] = Trim($url);
        $url = parse_url(Trim($url));
        $host = $url['host'];
        $myHost[] = ucfirst(str_replace('www.','',$host));
        $alexa = alexaRank($host);
        $alexa_rank = $alexa[0];
        $alexa_rank = ($alexa_rank == 'No Global Rank' ? '0' : $alexa_rank);
        $price[] = "$". number_format(calPrice($alexa_rank))." USD";
        }
        }
        }
    }
    
}
//PR33 - Website Screenshot Generator
elseif($toolUid == 'PR33') {
    
    $controller = 'output'.D_S.'website_screenshot';

    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_url($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst(str_replace('www.','',$host));
            $file = getSiteSnap($host,$item_purchase_code);
            $tokenKey = randomPassword();
            $_SESSION['getWebSnap'] = $tokenKey;
            $myImage = "../?route=ajax&getWebSnap&site=$host&token=$tokenKey";
            }
      }
      }
}
//PR34 - Domain Hosting Checker
elseif($toolUid == 'PR34') {

    $controller = 'output'.D_S.'domain_hosting';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_url($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $getHostIP = gethostbyname($host);
            $data_list = host_info($host);
            $domain_isp = $data_list[2];
            }
        }
    }
}
//PR35 - Get Source Code of Webpage
elseif($toolUid == 'PR35') {

    $controller = 'output'.D_S.'source_code_webpage';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $outData = getMyData($my_url);
            }
        }
    }
}
//PR36 - Google Index Checker
elseif($toolUid == 'PR36') {
    
    $controller = 'output'.D_S.'google_index_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_with_www($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $outData = googleIndex($host);
            }
      }
      }
    
}
//PR37 - Website Links Count Checker
elseif($toolUid == 'PR37') {

    $controller = 'output'.D_S.'links_counter';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = Trim("http://$my_url");
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $uriData=doLinkAnalysis($my_url);	
            $internal_links = $uriData[0];
            $internal_links_count = $uriData[1];
            $internal_links_nofollow = $uriData[2];
            $external_links = $uriData[3];
            $external_links_count = $uriData[4];
            $external_links_nofollow = $uriData[5];
            $total_links = $uriData[6]; 
            $total_nofollow_links = (int)$internal_links_nofollow + (int)$external_links_nofollow;
            }
        }
    }
    
}
//PR38 - Class C Ip Checker
elseif($toolUid == 'PR38') {
    
    $controller = 'output'.D_S.'class_c_ip';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $outData = raino_trim($_POST['data']);
        $regUserInput = truncate($outData,30,150);
        $array = explode("\n", $outData);
        $count = count($array);
        $dataCount = 0;
        foreach ($array as $url) {
        if($url == null || $url == ""){
            
        }else{
        $url = clean_with_www($url); $url = Trim("http://$url");
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
        $dataCount = $dataCount+1;
        $my_url[] = Trim($url);
        $url = parse_url(Trim($url));
        $host = $url['host'];
        $getHostIP = gethostbyname($host);
        $class_c = explode(".",$getHostIP);
        $class_c = $class_c[0].'.'.$class_c[1].'.'.$class_c[2];
        $ipList[] = $getHostIP;
        $classCList[] = $class_c;
        $myHost[] = ucfirst(str_replace('www.','',$host));
        }
        }
        }
        }
    }
}
//PR39 - Online Md5 Generator
elseif($toolUid == 'PR39') {
    $controller = 'output'.D_S.'online_md5';
    if ($pointOut == 'output') {
        if (!isset($_POST['data']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
        $do_hash_data = htmlspecialchars($_POST['data'], ENT_COMPAT,'ISO-8859-1', true);
        $regUserInput = truncate($do_hash_data,30,150);
        $output = MD5($do_hash_data);
        $limited_hash_data = truncate($do_hash_data, 50, 500);
        }
    } 
}
//PR40 - Page Speed Checker
elseif($toolUid == 'PR40') {
    
    $controller = 'output'.D_S.'page_speed_checker';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $outData = checkPageSpeed($my_url,$lang['97']);
            $timeTaken = $outData[0];
            $allLinks = $outData[1];
            $cssLinks = $outData[2];
            $imgLinks = $outData[3];
            $scriptLinks = $outData[4];
            $otherLinks = $outData[5];
            }
        }
    }
}
//PR41 - Code to Text Ratio Checker
elseif($toolUid == 'PR41') {
       
    $controller = 'output'.D_S.'code_to_text_ratio';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $pageData = getMyData($my_url);
            if(!empty($pageData)){
            $arr_res = calTextRatio($pageData);
            $orglen = $arr_res[0];
            $textlen = $arr_res[1];
            $per = $arr_res[2];          
            }else{
                $error = $lang['183'];
            }
            }
        }
    } 
}
//PR42 - Find DNS records
elseif($toolUid == 'PR42') {

    $controller = 'output'.D_S.'find_dns_records';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = "http://".clean_url($my_url);
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $my_url = parse_url($my_url);
            $host = $my_url['host'];
            $myHost = ucfirst($host);
            $outData = dns_get_record($host, DNS_ALL);
            }
      }
      }
}
//PR43 - What is my Browser
elseif($toolUid == 'PR43') {
    
    $controller = 'output'.D_S.'my_browser_info';
    
    $myUA = $_SERVER['HTTP_USER_AGENT'];
    $outData = parse_user_agent($myUA);
    extract($outData);
            
    if ($pointOut == 'output') {
        die($lang['4']); //Malformed Request!
    }
}
//PR44 - Email Privacy
elseif($toolUid == 'PR44') {
        
    $controller = 'output'.D_S.'email_privacy';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $content = getMyData($my_url);
            if($content==null || $content == ""){
                $error = $lang['183'];
            }else{
            preg_match_all("/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/", $content, $matches,PREG_SET_ORDER);

            if(count($matches) == 0){
            $noEmail = $lang['190'];
            }else{
            foreach($matches as $email){
            $emailList[] = $email[0];
                }
                }
            }
        }
        }
    }
}
//PR45 - Google Cache Checker
elseif($toolUid == 'PR45') {

    $controller = 'output'.D_S.'google_cache_checker';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
}
//PR46 - Broken Links Finder
elseif($toolUid == 'PR46') {
    
    $controller = 'output'.D_S.'broken_links_finder';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $brokenLinks = getBrokenLinks($my_url,$lang['200']);
            $internalLinks = $brokenLinks[0];
            $externalLinks = $brokenLinks[1];
            }
        }
    }
    
}
//PR47 - Search Engine Spider Simulator
elseif($toolUid == 'PR47') {
    
    $controller = 'output'.D_S.'spider_simulator';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
            $error = $lang['327'];
            }else {
            $regUserInput = $my_url;
            $outData = spiderView($my_url,$lang['327']);
            $sourceData = $outData[0];
            $meta_title = $outData[1];
            $meta_description = $outData[2];
            $meta_keywords = $outData[3];
            $textData = $outData[4];
            $tags = $outData[5];
            $uriData=doLinkAnalysis($my_url);	
            $internal_links = $uriData[0];
            }
        }
    }
    
}
//PR48 - Keywords Suggestion Tool
elseif($toolUid == 'PR48') {
    
    $controller = 'output'.D_S.'keywords_suggestion_tool';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
        die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $inData = raino_trim($_POST['url']);
            $regUserInput = truncate($inData,30,150);
            $count = 0;
            $outArr = getSuggestQueries($inData,$lang['97']);
        }
    }
}
//PR49 - Bulk Domain Authority Checker
elseif($toolUid == 'PR49') {
    
    $controller = 'output'.D_S.'domain_authority_checker';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
    
}
//PR50 - Bulk Page Authority Checker
elseif($toolUid == 'PR50') {
        
    $controller = 'output'.D_S.'page_authority_checker';
    
    if ($pointOut == 'output') {
    die($lang['4']); //Malformed Request!
    }
}
//SD51 - Pagespeed Insights Checker 
elseif($toolUid == 'SD51') {
        
    $controller = 'output'.D_S.'google-pagespeed';
    
    if ($pointOut == 'output') {
        if (!isset($_POST['url']))
            die($lang['4']); //Malformed Request!
        if (!isset($error)) {
            $my_url = raino_trim($_POST['url']);
            $my_url = clean_with_www($my_url); $my_url = "http://$my_url";
            if (filter_var($my_url, FILTER_VALIDATE_URL) === false) {
                $error = $lang['327'];
            }else {
                $regUserInput = $my_url;
            }
        }
    }
}

if(SEO_ADDON_TOOLS){
    $addonPath = CON_DIR . 'addontools.php';
    if(file_exists($addonPath)){
        require $addonPath;
    }
}

//Add User into Recent History
if(isset($_SESSION['userToken'])){
    $regUserName = $_SESSION['username'];
}else{
    $regUserName = "Guest";
}
$userDate = date('m/d/Y h:i:sA');  
$intDate = date('m/d/Y');  
if ($pointOut != 'output') {
    regRecentHistory($con,$ip,$data['tool_name'],$regUserName,$userDate,$intDate);
}else{
    if(!isset($regUserInput))
    $regUserInput = "NULL";
    regUserInputHistory($con,$ip,$data['tool_name'],$regUserName,$userDate,$regUserInput);
}
?>