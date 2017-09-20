<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Mail Settings";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $smtp_host =   escapeTrim($con,$_POST['smtp_host']);
    $smtp_port =  escapeTrim($con,$_POST['smtp_port']);
    $smtp_username =  escapeTrim($con,$_POST['smtp_user']);
    $smtp_password =  escapeTrim($con,$_POST['smtp_pass']);
    $socket =  escapeTrim($con,$_POST['socket']);
    $auth =  escapeTrim($con,$_POST['auth']);
    $protocol =  escapeTrim($con,$_POST['protocol']);
    
    $query = "UPDATE mail SET smtp_host='$smtp_host', smtp_port='$smtp_port', smtp_username='$smtp_username', smtp_password='$smtp_password', socket='$socket', protocol='$protocol', auth='$auth' WHERE id='1'"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
        $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> ' . mysqli_error($con) . '
                                    </div>';
     
    }
    else
    {
                $msg = '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Mail information saved successfully
                                    </div>';
        
    }
}   
$query = "SELECT * FROM mail WHERE id='1'";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $smtp_host = Trim($row['smtp_host']);
    $smtp_username = Trim($row['smtp_username']);
    $smtp_password = Trim($row['smtp_password']);
    $smtp_port = Trim($row['smtp_port']);
    $protocol = Trim($row['protocol']);
    $auth = Trim($row['auth']);
    $socket = Trim($row['socket']);
}

?>