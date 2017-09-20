<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework
 * @copyright © 2016 ProThemes.Biz
 *
 */
if(!isset($customWarn)){
if(isset($visitWarn)){
$warningMsg = '&nbsp; ' . $lang['311'] . ' <a data-toggle="modal" data-target="#signin" href="#" style="color: #b94a48;">Sign In </a> ' . $lang['312']; 
}
elseif(isset($loginWarn)){
$warningMsg = '&nbsp; ' . $lang['313'] . ' <a data-toggle="modal" data-target="#signin" href="#" style="color: #b94a48;">Sign In </a> '; 
}
else{
    $warningMsg = $lang['97'];
}
}else{
   $warningMsg = $customWarn;
}

?>