<?php

/*
* @author Balaji
* @name Rainbow PHP Framework
* @copyright © 2016 ProThemes.Biz
*
*/

function helper(){
    return "I am Helper!";
}

function regRecentHistory($con,$ip,$toolname,$user,$date,$intDate){
    $query = "INSERT INTO recent_history (visitor_ip,tool_name,user,date,intDate) VALUES ('$ip','$toolname','$user','$date','$intDate')"; 
    mysqli_query($con,$query);
    return true;
}

function getGuestUserCount($con,$ip){
$user_count = 0;
$intDate = date('m/d/Y');
$query =  "SELECT * FROM recent_history WHERE visitor_ip='$ip' AND intDate='$intDate' AND user='Guest'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
   $user_count= (int)$user_count+1;
}
return $user_count; 
}

function getMaintenanceMode($con){

$query =  "SELECT * FROM maintenance WHERE id='1'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
    $maintenance_mode =  filter_var(Trim($row['maintenance_mode']), FILTER_VALIDATE_BOOLEAN);
    $maintenance_mes =   Trim($row['maintenance_mes']);
}
    return array($maintenance_mode,$maintenance_mes);
}

function regUserInputHistory($con,$ip,$toolname,$user,$date,$regUserInput){
    $query = "INSERT INTO user_input_history (visitor_ip,tool_name,user,date,user_input) VALUES ('$ip','$toolname','$user','$date','$regUserInput')"; 
    mysqli_query($con,$query);
    return true;
}

function getMenuBarLinks($con,$userPageUrl=""){
    $query = "SELECT * FROM pages"; 
    $result = mysqli_query($con, $query);

    while($row = mysqli_fetch_array($result)) {
        $header_show = filter_var($row['header_show'], FILTER_VALIDATE_BOOLEAN);
        $footer_show = filter_var($row['footer_show'], FILTER_VALIDATE_BOOLEAN);
        $page_url = Trim($row['page_url']);
        $page_name = Trim($row['page_name']);
    
        if($header_show){
            if($page_url == $userPageUrl)
            $headerLinks[] = '<li class="active"><a href="/page/'.$page_url.'">'. $page_name .'</a></li>';
            else
            $headerLinks[] = '<li><a href="/page/'.$page_url.'">'.$page_name.'</a></li>';
        }
        
        if($footer_show){
            if($page_url == $userPageUrl)
            $footerLinks[] = '<li class="active"><a href="/page/'.$page_url.'">'. $page_name .'</a></li>';
            else
            $footerLinks[] = '<li><a href="/page/'.$page_url.'">'.$page_name.'</a></li>';
        }
    }
    return array($headerLinks,$footerLinks);
}

function getSidebarWidgets($con){
    $leftWidgets = $rightWidgets = $footerWidgets = array();
    $result = mysqli_query($con,"SELECT * FROM widget ORDER BY CAST(sort_order AS UNSIGNED) ASC");
    while ($row = mysqli_fetch_array($result))
    {
        $widgetType = strtolower(Trim($row['widget_type']));
        $showWidget = filter_var($row['widget_enable'], FILTER_VALIDATE_BOOLEAN);
        if($showWidget) {
            
            $widgetCode = htmlspecialchars_decode($row['widget_code']);
            $widgetName = htmlspecialchars_decode($row['widget_name']);
            
            if(str_contains($widgetCode,"shortCode")){
                $shortCode = explode("shortCode(",$widgetCode);
                $shortCode = explode(")",$shortCode[1]);
                $shortCode = Trim($shortCode[0]);
                if(defined($shortCode))
                    $widgetCode = str_replace("shortCode(".$shortCode.")",constant($shortCode),$widgetCode);
                else
                    $widgetCode = "SHORT CODE NOT FOUND!"; 
            }
            
            if($widgetType=="left")
                $leftWidgets[] = array($widgetName,$widgetCode);  
            elseif($widgetType=="right")
                $rightWidgets[] = array($widgetName,$widgetCode);  
            else
                $footerWidgets[] = array($widgetName,$widgetCode);  
        }
    }
    return array($leftWidgets,$rightWidgets,$footerWidgets);
}

function getTheme($con){
    $query =  "SELECT * FROM interface where id='1'";
    $result = mysqli_query($con,$query);
        
    while($row = mysqli_fetch_array($result)) {
    $default_theme =   Trim($row['theme']);
    }
    return $default_theme;
}

function getLang($con){
    $query =  "SELECT * FROM interface where id='1'";
    $result = mysqli_query($con,$query);
        
    while($row = mysqli_fetch_array($result)) {
    $default_lang =  Trim($row['lang']);
    }
    return $default_lang;
}

function getThemeList(){
    $dir = ROOT_DIR."theme";
    $themelist = array();
    $files1 = scandir($dir);
                        
    $dircount = count($files1);
    for ($loop=2;$loop<=$dircount-1;$loop++)
    {
    $fname = explode('.php',$files1[$loop]);$fname = $fname[0];
    $ffname = $files1[$loop];  
    if (is_dir($dir.D_S.$ffname))
    {
    $themelist[] = $fname;
    }
    }
    return $themelist;
}

function getLangList(){
    $dir    = ROOT_DIR.'lang';
    $langlist = array();
    $files1 = scandir($dir);
    $dircount = count($files1);
    for ($loop=2;$loop<=$dircount-1;$loop++)
    {
    $fname = explode('.php',$files1[$loop]);$fname = $fname[0];
    $ffname = $files1[$loop];   
    if ($ffname == "index.php"){
    }else{
    $langlist[] = $ffname;
    }
    }
    return $langlist;
}

function setTheme($con,$themeName){
    $themeName = escapeTrim($con,$themeName);
    $themeArr = getThemeList();
    if (!in_array($themeName,$themeArr))
    {
    return false;
    }
    $query = "UPDATE interface SET theme='$themeName' WHERE id='1'"; 
    mysqli_query($con,$query); 
    if (mysqli_errno($con)) {
        return false;
    }else{
        return true;
    } 
}

function setLang($con,$lang){
    $lang = escapeTrim($con,$lang);
    $langArr = getLangList();
    if (!in_array($lang,$langArr))
    {
    return false;
    }
    $query = "UPDATE interface SET lang='$lang' WHERE id='1'"; 
    mysqli_query($con,$query); 
    if (mysqli_errno($con)) {
        return false;
    }else{
        return true;
    } 
}

function banCheck($con,$ip,$site_name) {
    $query = mysqli_query($con, "SELECT * FROM ban_user WHERE ip='$ip'");
    if (mysqli_num_rows($query) > 0)
    {
    die("You have been banned from ".$site_name); 
    }
}

function getPageSize($url){ 
   $return = strlen(file_get_contents($url));
   return $return; 
}

function size_as_kb($yoursize) {
    $size_kb = round($yoursize/1024);
    return $size_kb;
}

function getSuggestQueries($userInput,$err_str="Something Went Wrong") {
    
    $googleUrl = 'http://suggestqueries.google.com/complete/search';
    $keywords = array();
    $json = getMyData($googleUrl.'?output=firefox&client=firefox&hl=en-US&q='.urlencode($userInput));
    
    if($json == "")
    die($err_str);
    
    $json = json_decode($json, true);
    $keywords = $json[1];
    return $keywords;
}

function calPrice($global_rank) {
    $monthly_inc =round((pow($global_rank, -1.008)* 104943144672)/524); 
    $monthly_inc = (is_infinite($monthly_inc)? '5' :$monthly_inc);
    $daily_inc  =round($monthly_inc/30);
    $daily_inc = (is_infinite($daily_inc)? '0':$daily_inc);
    $yearly_inc =round($monthly_inc*12);
    $yearly_inc = (is_infinite($yearly_inc)? '0':$yearly_inc);
    $yearly_inc = ($yearly_inc < 9 ? 10 : $yearly_inc);
    return $yearly_inc;
}

function calPageSpeed($myUrl,$refUrl) {
		
    $timeStart = microtime(true);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $myUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0');
    curl_setopt($ch, CURLOPT_REFERER, $refUrl);
    $html = curl_exec($ch);
    curl_close($ch);
    $timeEnd = microtime(true);
    $timeTaken = $timeEnd - $timeStart;
		
    return $timeTaken;
}
    
function getPageData($myUrl,$error) {
    $timeStart = microtime(true);
    $data = file_get_html($myUrl);
    if(empty($data)) {
    echo $error;
    die();
    }
    $timeEnd = microtime(true);
    $timeTaken = $timeEnd - $timeStart;
    return array($data, $timeTaken);
}
    
function curlGET_Text($url){
    $cookie=tempnam("/tmp","CURLCOOKIE");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);  
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_HEADER,0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_AUTOREFERER,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ("Accept: text/plain"));
	$html=curl_exec($ch);
    curl_close($ch);
    return $html;
}

function getHttp($headers) {
    $headers = explode("\r\n", $headers);
    $http_code = explode(' ', $headers[0]);
    return (int)trim($http_code[1]);
}

function cleanWWW($string) {
    $string = strtolower($string);
	if(strstr($string,"www")) {
		$urlArr=explode("www.",$string);
        $fURL=$urlArr[0].$urlArr[1];
		return $fURL;
	}
	else 
	   return $string;
}

function checkRedirect($my_url,$goodStr="Good",$badStr="Bad - Not Redirecting!") {    
    $my_url = clean_url($my_url); 
    $re301 = false;
    $url_with_www = "http://www.$my_url";
    $url_no_www = "http://$my_url";
    
    $data1 = getHeaders($url_with_www);
    $data2 = getHeaders($url_no_www);
    
    if(str_contains($data1,'301'))
    $re301 = true;
    if(str_contains($data2,'301'))
    $re301= true;
    
    $str = ($re301 == true ? $goodStr : $badStr); 
    return $str;
}

function calTextRatio($pageData) {
$orglen = strlen($pageData);
$pageData = preg_replace('/(<script.*?>.*?<\/script>|<style.*?>.*?<\/style>|<.*?>|\r|\n|\t)/ms', '', $pageData);  
$pageData = preg_replace('/ +/ms', ' ', $pageData);  
$textlen = strlen($pageData);
$per = (($textlen * 100) / $orglen);
return array($orglen,$textlen,$per);
}

function errStop()
{
    echo 'S'.'o'.'m'.'e'.'t'.'h'.'i'.'n'.'g'.' '.'W'.'e'.'n'.'t'.' '.'W'.'r'.'o'.'n'.'g'.'!';
    die();
}

function ordinal($num) {
    $num = (int)$num;
    // Special case "teenth"
    if ( ($num / 10) % 10 != 1 )
    {
        // Handle 1st, 2nd, 3rd
        switch( $num % 10 )
        {
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd'; 
        }
    }
    // Everything else is "nth"
    return $num . 'th';
}

if($dbName != null || $dbName != ""){
if (isset($item_purchase_code))
{
    if($item_purchase_code == "")
    errStop();
    if(!str_contains($item_purchase_code,'-'))
    errStop();
}
else
{
    errStop();
}
}
?>