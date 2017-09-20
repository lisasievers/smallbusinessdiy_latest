<?php

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.1.2
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
//Define Directories
define('D_S',DIRECTORY_SEPARATOR);
define('CON_DIR',APP_DIR.'controllers'.D_S);
define('HEL_DIR',APP_DIR.'helpers'.D_S);
define('LIB_DIR',APP_DIR.'library'.D_S);
define('MOD_DIR',APP_DIR.'models'.D_S);
define('PLG_DIR',APP_DIR.'plugins'.D_S);
define('HASH_CODE',md5($item_purchase_code));

function dbConncet($mysql_host,$mysql_user,$mysql_pass,$mysql_database){
    $con = mysqli_connect($mysql_host,$mysql_user,$mysql_pass,$mysql_database);
    if (mysqli_connect_errno())
    {
    die("Unable to connect to Mysql Server");
    }
    return $con; 
}

function isValidUsername($str){
    return !preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
}

function isValidEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidSite($site) {
    return !preg_match('/^[a-z0-9\-]+\.[a-z]{2,100}(\.[a-z]{2,14})?$/i', $site);
}

function str_contains($haystack, $needle, $ignoreCase = false){
    if ($ignoreCase)
    {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
    }
    $needlePos = strpos($haystack, $needle);
    return ($needlePos === false ? false : ($needlePos + 1));
}

function raino_trim($str){
    $str = Trim(htmlspecialchars($str));
    return $str;
}

function randomPassword(){
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 9; $i++)
    {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

function escapeMe($con,$data){
     return mysqli_real_escape_string($con, $data);
}

function escapeTrim($con,$data){
     $data = Trim(htmlspecialchars($data));
     return mysqli_real_escape_string($con, $data);
}

function roundSize($size){
    $i = 0;
    $iec = array(
        "B",
        "Kb",
        "Mb",
        "Gb",
        "Tb");
    while (($size / 1024) > 1)
    {
        $size = $size / 1024;
        $i++;
    }
    return (round($size, 1) . " " . $iec[$i]);
}

function encrypt($value,$secret){
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $val = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret, $value, MCRYPT_MODE_ECB, $iv);
    return base64_encode($val);
}

function decrypt($value,$secret){
    $value = base64_decode($value);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret, $value, MCRYPT_MODE_ECB, $iv);
}

function truncate($input, $maxWords, $maxChars){
    $words = preg_split('/\s+/', $input);
    $words = array_slice($words, 0, $maxWords);
    $words = array_reverse($words);

    $chars = 0;
    $truncated = array();

    while(count($words) > 0)
    {
        $fragment = trim(array_pop($words));
        $chars += strlen($fragment);

        if($chars > $maxChars) break;

        $truncated[] = $fragment;
    }

    $result = implode($truncated, ' ');

    return $result . ($input == $result ? '' : '...');
}

function makeUrlFriendly($input){
        $output = preg_replace("/\s+/" , "_" , raino_trim($input));
        $output = preg_replace("/\W+/" , "" , $output);
        $output = preg_replace("/_/" , "-" , $output);
        return strtolower($output);
}
/*
function rgb2hex($rgb){
    $hex = "#";
    $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
    return $hex;
}

function hex2rgb($hex){
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3)
    {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else
    {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb ="$r,$g,$b";
    return $rgb;
}
*/
function getFrameworkVersion() {
		return '1.1.2';
}

function getServerMemoryUsage(){

    $free = shell_exec('free');
    $free = (string )trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage = $mem[2] / $mem[1] * 100;

    return $memory_usage;
}

function getServerCpuUsage() {
    if (function_exists('sys_getloadavg'))
    {
    $load = sys_getloadavg();
    return $load[0];
    }
    else
    {
    return "Not Available";
    }
}

function clean_url($site) {
    $site = strtolower($site);
    $site = str_replace(array(
        'http://',
        'https://',
        'www.'), '', $site);
    return $site;
}

function clean_with_www($site) {
    $site = strtolower($site);
    $site = str_replace(array(
        'http://',
        'https://'), '', $site);
    return $site;
}

function getTimeZone(){
    return date_default_timezone_get();
}

function setTimeZone($value) {
    date_default_timezone_set($value);
    return true;
}

function getDaysOnThisMonth($month = 5, $year = '2015'){
  if ($month < 1 OR $month > 12)
  {
	  return 0;
  }

  if ( ! is_numeric($year) OR strlen($year) != 4)
  {
	  $year = date('Y');
  }

  if ($month == 2)
  {
	  if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0))
	  {
		  return 29;
	  }
  }

  $days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  return $days_in_month[$month - 1];
}
 
function getDomainName($site){
    $site = clean_url($site);
    $site = parse_url(Trim("http://".$site));
    $host = $site['host'];
    return $host;
}

function getUserIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
    $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getUA(){
    return $_SERVER ['HTTP_USER_AGENT'];
}

function getUserLang($default='en'){
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$langs=explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

		foreach ($langs as $value){
			$choice=substr($value,0,2);
            return $choice;
		}
	} 
	return $default;
}

function delDir($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file)
    {
        (is_dir("$dir/$file")) ? delDir("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
    return 1;
}

function delFile($file){
    return unlink($file);
}

function fixSpecialChar($plainTxt){
    return mb_convert_encoding($plainTxt, 'UTF-8', 'UTF-8');
}

function getLastID($con,$table) {
    $table = escapeTrim($con,$table);
    $query = "SELECT @last_id := MAX(id) FROM $table";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result))
    {
        $last_id = $row['@last_id := MAX(id)'];
        return $last_id;
    }
}

function getMyData($site){
    return file_get_contents($site);
}

function putMyData($file_name,$data){
   return file_put_contents($file_name,$data);
}

function baseURL($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
}

function passwordHash($str){
    $hash=md5(crypt(Md5($str),HASH_CODE));
    return $hash;
}

function curlPOST($url,$post_data,$ref_url = "http://www.google.com/",$agent = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0"){
    $cookie=tempnam("/tmp","CURLCOOKIE");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent );
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/html; charset=utf-8","Accept: */*"));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_REFERER, $ref_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
    $html=curl_exec($ch);
    curl_close($ch);
    return $html;
}

function curlGET($url,$ref_url = "http://www.google.com/",$agent = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0"){
    $cookie=tempnam("/tmp","CURLCOOKIE");
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent );
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/html; charset=utf-8","Accept: */*"));
    curl_setopt($ch, CURLOPT_VERBOSE, True);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_REFERER, $ref_url);
	$html=curl_exec($ch);
    curl_close($ch);
    return $html;
}

function getHeaders($site) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $site);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $headers=curl_exec($ch);
    curl_close($ch);
    return $headers;
}

function detectInstaller(){
    $filename = ROOT_DIR."admin".D_S."install".D_S."install.php";
    if (file_exists($filename)) {
    echo "Install.php file exists! <br /> <br />  Redirecting to installer panel...";
    header("Location: /admin/install/install.php");
    echo '<meta http-equiv="refresh" content="1;url=/admin/install/install.php">';
    exit();
    }
    return false; 
}

function createZip($source,$des,$filename) {
    $filename = str_replace(".zip","",$filename);
    $zip = new ZipArchive();
    $zip->open($des.$filename.".zip", ZipArchive::CREATE);
    if (is_dir($source) === true)
    {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($files as $file)
    {
        if (is_dir($file) === true)
        {}
        else if (is_file($file) === true)
        {
            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
    }
    }
    $zip->close();
    return true;
}

function extractZip($sourceFile,$desPath){
    $zip = new ZipArchive;
    $res = $zip->open($sourceFile);
    if ($res === TRUE) {
    $zip->extractTo($desPath);
    $zip->close();
    return true;
    } else {
    return false;
    }
}

function __autoload($name) {
    $filepath = MOD_DIR.$name.".php";
    $filepath1 = MOD_DIR.strtolower($name).".php";
    if(file_exists($filepath))
    {
        require $filepath;
        return true;
    }
    elseif(file_exists($filepath1))
    {
        require $filepath1;
        return true;
    }
    return false;
}

foreach (glob(HEL_DIR."*{_helper,_help}.php",GLOB_BRACE) as $filename) {
    if(file_exists($filename))
    {
        require $filename;
    }
}
?>