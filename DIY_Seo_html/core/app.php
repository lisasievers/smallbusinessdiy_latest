<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */

// Current Date & User IP
$date = date('jS F Y');
$ip = getUserIP();
$data_ip = getMyData( APP_DIR .'temp_cal.tdata');

//Get Base URL
$server_path = baseURL();
//$baseURL = $server_path;
$baseURL ="/";

if(isset($_GET['logout'])){
    unset($_SESSION['token']);
    unset($_SESSION['oauth_uid']);
    unset($_SESSION['username']);
    unset($_SESSION['pic']);
    unset($_SESSION['userToken']);
    session_destroy();
    header("Location: ../");
    echo '<meta http-equiv="refresh" content="1;url=../">';
}

//Get site Info
$query =  "SELECT * FROM site_info";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
    $title =  Trim($row['title']);
    $des =   Trim($row['des']);
    $keyword =  Trim($row['keyword']);
    $site_name =   Trim($row['site_name']);
    $email =   Trim($row['email']);
    $twit =   Trim($row['twit']);
    $face =   Trim($row['face']);
    $gplus =   Trim($row['gplus']);
    $ga  =   Trim($row['ga']);
    $copyright =  htmlspecialchars_decode(Trim($row['copyright']));
    $footer_tags =  explode(',',Trim($row['footer_tags']));
    $forceHttps = $forceWww = false;
    $doForce = unserialize($row['doForce']);
    $project_url = Trim($row['project_url']);
    $forceHttps = filter_var($doForce[0], FILTER_VALIDATE_BOOLEAN);
    $forceWww = filter_var($doForce[1], FILTER_VALIDATE_BOOLEAN);
}

if($forceWww){
    if ((strpos($_SERVER['HTTP_HOST'], 'www.') === false)) {
        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
        header('Location: '.$protocol.'www.'.$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"],true,301);
        exit();
    }
}

if($forceHttps){
    if (!isset($_SERVER["HTTPS"])) {
        header('Location: '.'https://'.$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"],true,301);
        exit();
    }
}

//Get AD Settings
$query = "SELECT * FROM ads";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $ads_720x90 = htmlspecialchars_decode($row['ad720x90']);
    $ads_250x300 = htmlspecialchars_decode($row['ad250x300']);
    $ads_250x125 = htmlspecialchars_decode($row['ad250x125']);
    $ads_468x70 = htmlspecialchars_decode($row['ad480x60']);
    $text_ads = htmlspecialchars_decode($row['text_ads']);
}

//Blog Settings
if(file_exists(CON_DIR."blog.php")){
    $sql = "SELECT * FROM blog where id='1'";
    $result = mysqli_query($con, $sql);
    
    while ($row = mysqli_fetch_array($result))
    {
    $blog_status = filter_var($row['blog_enable'], FILTER_VALIDATE_BOOLEAN);
    }
}else{
    $blog_status = false;
}

//Load User Settings
$query =  "SELECT * FROM user_settings WHERE id='1'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
    $enable_reg =  filter_var(Trim($row['enable_reg']), FILTER_VALIDATE_BOOLEAN);
    $enable_oauth =  filter_var(Trim($row['enable_oauth']), FILTER_VALIDATE_BOOLEAN);
    $visitors_limit =  Trim($row['visitors_limit']);
    $fb_app_id =   Trim($row['fb_app_id']);
    $fb_app_secret =   Trim($row['fb_app_secret']);
    $g_client_id =   Trim($row['g_client_id']);
    $g_client_secret =   Trim($row['g_client_secret']);
    $g_redirect_uri =   Trim($row['g_redirect_uri']);
}

//Check User IP is banned
banCheck($con,$ip,$site_name);

//Get the default theme
$default_theme = getTheme($con);
$theme_path = $baseURL.'theme' . '/' . $default_theme . '/';
define('THEME_DIR', ROOT_DIR .'theme' . D_S . $default_theme . D_S);

$query = "SELECT * FROM image_path where id='1'";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $logo_path = "/".Trim($row['logo_path']);
    $fav_path = "/".Trim($row['fav_path']);
}
if ($logo_path == '')
    $logo_path = '/theme/default/img/logo.png';
if ($fav_path == '')
    $fav_path = '/theme/default/img/favicon.ico';

//Get the default lang
$default_lang = getLang($con); 
$langPath = ROOT_DIR.'lang'.D_S.$default_lang;
if(file_exists($langPath)){
    require_once($langPath);
}

if(isset($_GET['route'])) {
    $route = escapeTrim($con,$_GET['route']); 
    if(str_contains($route,'/')) {
    $route = explode('/',$route);
    $controller = $route[0];
    $pointOut = $route[1];
    }
    else {
     $controller = $route;
     $pointOut = "";
    }
    $query = mysqli_query($con, "SELECT * FROM seo_tools WHERE tool_url='$controller'");
    if (mysqli_num_rows($query) > 0)
        {
        $data = mysqli_fetch_array($query);
        $tool_show = filter_var($data['tool_show'], FILTER_VALIDATE_BOOLEAN);
        if($tool_show) {
        $controller = "seotools";
        $toolUid = $data['uid'];
        $tool_login = filter_var($data['tool_login'], FILTER_VALIDATE_BOOLEAN);
            
            //Visitors Limit - START
            if($enable_reg){
                if(!isset($_SESSION['userToken'])){
                    if($visitors_limit != '0'){
                        $user_count = getGuestUserCount($con,$ip);
                        if($visitors_limit<$user_count){
                        $visitWarn = true;   
                        $controller = "warning";
                        }
                    }
                    if($tool_login){
                        $loginWarn = true;   
                        $controller = "warning";
                    }
                }
            }
            //Visitors Limit - END
            
        }
        }
}else{
    $controller = "main";
}

//Maintenance Mode
$maintenanceRes = getMaintenanceMode($con);
if($maintenanceRes[0]){
    if(!isset($_SESSION['adminToken'])){
    $controller = "maintenance";
    }
}

$path = CON_DIR . $controller . '.php';
   	if(file_exists($path)){
        require($path);
	} else {
        $controller = "error";
        require(CON_DIR. $controller . '.php');
	}
    
    // Page View  
    $query =  "SELECT @last_id := MAX(id) FROM page_view";
    
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result)) {
    $last_id =  $row['@last_id := MAX(id)'];
    }
    
    $query =  "SELECT * FROM page_view WHERE id=".Trim($last_id);
    $result = mysqli_query($con,$query);
        
    while($row = mysqli_fetch_array($result)) {
    $last_date =  $row['date'];
    }
    
    //Mail Check
    mailDebugCheck($con);
    
    if($last_date == $date)
    {
        if (str_contains($data_ip, $ip)) 
        {
          $query =  "SELECT * FROM page_view WHERE id=".Trim($last_id);
          $result = mysqli_query($con,$query);
        
        while($row = mysqli_fetch_array($result)) {
        $last_tpage =  Trim($row['tpage']);
        }

        $last_tpage = $last_tpage + 1;

        // Already IP is there!  So update only page view.
        $query = "UPDATE page_view SET tpage='$last_tpage' WHERE id=".Trim($last_id);
        mysqli_query($con,$query);

        }
        else
        {
        
        $query =  "SELECT * FROM page_view WHERE id=".Trim($last_id);
        $result = mysqli_query($con,$query);
        
        while($row = mysqli_fetch_array($result)) {
        $last_tpage =  Trim($row['tpage']);
        $last_tvisit =  Trim($row['tvisit']);
        }
        $last_tpage = $last_tpage +1;
        $last_tvisit = $last_tvisit +1;
        
        // Update both tpage and tvisit.
        $query = "UPDATE page_view SET tpage=$last_tpage,tvisit=$last_tvisit WHERE id=".Trim($last_id);
        mysqli_query($con,$query);
        putMyData(APP_DIR .'temp_cal.tdata',$data_ip."\r\n".$ip); 
        }
    }
    else
    { 
    //Delete the file and clear data_ip
    delFile(APP_DIR ."temp_cal.tdata");
    $data_ip ="";
    
    // New date is created!
    $query = "INSERT INTO page_view (date,tpage,tvisit) VALUES ('$date','1','1')"; 
    mysqli_query($con,$query);
    
    //Update the IP!
    putMyData(APP_DIR .'temp_cal.tdata',$data_ip."\r\n".$ip); 
    
}
$menuBarLinks = getMenuBarLinks($con, $pointOut);
$headerLinks = $menuBarLinks[0];
$footerLinks = $menuBarLinks[1];

define('VIEW', $controller);
?>