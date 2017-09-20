<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "User Account Settings";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $enable_reg = escapeTrim($con, $_POST['enable_reg']);
    $enable_oauth = escapeTrim($con, $_POST['enable_oauth']);
    $visitors_limit = escapeTrim($con, $_POST['visitors_limit']);
    $fb_app_id = escapeTrim($con, $_POST['fb_app_id']);
    $fb_app_secret = escapeTrim($con, $_POST['fb_app_secret']);
    $g_client_id = escapeTrim($con, $_POST['g_client_id']);
    $g_client_secret = escapeTrim($con, $_POST['g_client_secret']);
    $g_redirect_uri = escapeTrim($con, $_POST['g_redirect_uri']);
    if($g_redirect_uri == ""){
    $g_redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . "/?route=google";
    }
    $query = "UPDATE user_settings SET enable_reg='$enable_reg', enable_oauth='$enable_oauth', visitors_limit='$visitors_limit', fb_app_id='$fb_app_id', fb_app_secret='$fb_app_secret', g_client_id='$g_client_id', g_client_secret='$g_client_secret', g_redirect_uri='$g_redirect_uri' WHERE id='1'";
    mysqli_query($con, $query);

    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> ' . mysqli_error($con) . '
                                    </div>';
    } else
    {
        $msg = '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> User settings saved successfully
                                    </div>';
    }

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

if($g_redirect_uri == ""){
    $g_redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . "/?route=google";
}
?>