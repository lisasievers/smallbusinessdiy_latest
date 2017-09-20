<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
$fullLayout = 1;
$p_title = "Manage Interface";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
   $selTheme = raino_trim($_POST['theme']);
   $selLang = raino_trim($_POST['lang']);
   
   if(setTheme($con,$selTheme)){
        $msg = '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Interface settings saved successfully!
                                    </div>';
                                    
   }else{
   $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Unable to save settings!
   </div>';
    
   }
   
   if(setLang($con,$selLang)){
    
   }else{
    
   }
}

$themeList = getThemeList($con);
$langList = getLangList($con);

$activeTheme = getTheme($con);
$activeLang = getLang($con);

?>