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
$browser = getUA();

//Get Base URL
$server_path = baseURL();
//$baseURL = $server_path;
$baseURL ="/admin/";
$project_url ="https://smallbusinessdiy.com";
$theme_path= $baseURL.'theme' . '/' . $admin_theme . '/';

$fullLayout = 0;

if(isset($_SESSION['adminToken'])){
    
$adminID = $_SESSION['adminID'];

$query =  "SELECT * FROM admin where id='$adminID'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
$adminUser =  Trim($row['user']);
$adminPssword = Trim($row['pass']);
$adminName =   Trim($row['admin_name']);
$adminLogo =   Trim($row['admin_logo']);
$admin_logo_path = $theme_path.$adminLogo;
} 

    $query =  "SELECT @last_id := MAX(id) FROM admin_history";
    
    $result = mysqli_query($con,$query);
    
    while($row = mysqli_fetch_array($result)) {
    $last_id =  $row['@last_id := MAX(id)'];
    }
    
    $query =  "SELECT * FROM admin_history WHERE id=".Trim($last_id);
    $result = mysqli_query($con,$query);
        
    while($row = mysqli_fetch_array($result)) {
    $last_date =  $row['last_date'];
    $last_ip =  $row['ip'];
    }

    if($last_ip == $ip )
    {
    if($last_date == $date)
    {
        
    }
    else
    {
    $query = "INSERT INTO admin_history (last_date,ip,browser) VALUES ('$date','$ip','$browser')"; 
    mysqli_query($con,$query);
    }  
    }
    else
    {
    $query = "INSERT INTO admin_history (last_date,ip,browser) VALUES ('$date','$ip','$browser')"; 
    mysqli_query($con,$query);
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
    
}else{
    $controller = "dashboard";  
}
}else{
$controller = "login";  
}
$interfaceSelected = $adminSelected = $userSelected = $adsSelected = $seotoolstSelected = $manageSiteSelected = $dasSelected = $reportSelected ='';
$pagesSelected = $sitemapSelected = $miscellaneousSelected = $blogSelected = '';
$manageSiteLinks = array("manage-site","site-logo","ban-user-ip","image-verification","mail-settings","maintenance");
$seotoolsLinks = array("manage-tools","manage-addons","shop-addons","edit-tools","additional-option");
$userLinks = array("manage-users","user-settings");
$blogLinks = array("manage-blog","blog-settings","new-post");
            
if (in_array($controller,$manageSiteLinks)){
    $manageSiteSelected = "active";
}elseif($controller == "reports"){
    $reportSelected = "active";
}elseif(in_array($controller,$seotoolsLinks)){
    $seotoolstSelected = "active";
}elseif($controller == "site-ads"){
    $adsSelected = "active";
}elseif(in_array($controller,$userLinks)){
    $userSelected = "active";
}elseif($controller == "admin-accs"){
    $adminSelected = "active";
}elseif($controller == "interface"){
    $interfaceSelected = "active";
}elseif($controller == "manage-pages"){
    $pagesSelected = "active";
}elseif($controller == "sitemap"){
    $sitemapSelected = "active";
}elseif($controller == "miscellaneous"){
    $miscellaneousSelected = "active";
}elseif(in_array($controller,$blogLinks)){
    $blogSelected = "active";
}
else{
    $dasSelected = "active";
}

$path = ADMIN_CON_DIR . $controller . '.php';
   	if(file_exists($path)){
        require($path);
	} else {
        $controller = "error";
        require(ADMIN_CON_DIR. $controller . '.php');
	}

define('VIEW', $controller);
?>