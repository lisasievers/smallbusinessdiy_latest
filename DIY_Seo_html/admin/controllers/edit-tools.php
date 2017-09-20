<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */

if(!isset($_GET['id'])){
    header('Location: /admin/');
    exit();
}

$dID = raino_trim($_GET['id']);

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $tool_name = escapeTrim($con,$_POST['tool_name']);
    $tool_url = escapeTrim($con,$_POST['tool_url']);
    $tool_show = escapeTrim($con,$_POST['tool_show']);
    $tool_login = escapeTrim($con,$_POST['tool_login']);
    $tool_no = escapeTrim($con,$_POST['tool_no']);
    $meta_title = escapeTrim($con,$_POST['meta_title']);
    $meta_des = escapeTrim($con,$_POST['meta_des']);
    $meta_tags = escapeTrim($con,$_POST['meta_tags']);
    $captcha = escapeTrim($con,$_POST['captcha']);
    $about_tool = escapeTrim($con,$_POST['about_tool']);
    
    $query = "UPDATE seo_tools SET tool_name='$tool_name', tool_url='$tool_url', tool_no='$tool_no', tool_show='$tool_show', meta_title='$meta_title', meta_des='$meta_des', meta_tags='$meta_tags', captcha='$captcha', about_tool='$about_tool', tool_login='$tool_login' WHERE id='$dID'";
    if (!mysqli_query($con,$query))
    {
    $msg = '<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Something Went Wrong!
    </div>';
    }else{
    $msg = '<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> SEO Tool information saved successfully!
    </div>
    <meta http-equiv="refresh" content="1;url=/admin/?route=manage-tools">
    ';
    }
}


$query =  "SELECT * FROM seo_tools WHERE id='$dID'";
$result = mysqli_query($con,$query);

while($row = mysqli_fetch_array($result)){
  extract($row);
}

$tool_show = filter_var($tool_show, FILTER_VALIDATE_BOOLEAN);
$tool_login = filter_var($tool_login, FILTER_VALIDATE_BOOLEAN);
$captcha = filter_var($captcha, FILTER_VALIDATE_BOOLEAN);  

if($tool_show){
   $tool_show_text = "Active";
   $tool_show_color = "27ae60";
   $tool_show_yes = "selected=''";
}else{
   $tool_show_text = "Inactive";
   $tool_show_color = "c0392b";
   $tool_show_no = "selected=''";
}

if($tool_login){
   $tool_login_text = "Needed";
   $tool_login_color = "27ae60";
   $tool_login_yes = "selected=''";
}else{
   $tool_login_text = "Not Needed";
   $tool_login_color = "c0392b";
   $tool_login_no = "selected=''";
}

if($captcha){
    $captcha_text = "Active";
    $captcha_color = "27ae60";
    $captcha_yes = "selected=''";
}else{
    $captcha_text = "Inactive";
    $captcha_color = "c0392b";
    $captcha_no = "selected=''";
}

$fullLayout = 1;
$p_title = "$tool_name - SEO Tool";
$editPage =1 ;
?>