<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
//Page Title
$p_title = $lang['265'];


if(!$enable_reg){
    header("Location: ../");
    exit();  
}

if(!$enable_oauth){
    header("Location: ../");
    exit();  
}

if(isset($_GET['new_user']))
{
    $new_user = 1;
}
if(!isset($_SESSION['oauth_uid'])){
   die(); 
}
$username = $_SESSION['username'];

// POST Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['user_change']))
    {
        $new_username = raino_trim($_POST['new_username']);
        if ($new_username == "" || $new_username == null)
        {
            $error =  $lang['254']; //"Username not vaild";
        }
        else
        {    
        if (isValidUsername($new_username))
        {
        $query =  "SELECT * FROM users WHERE username='$new_username'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0)  
        {
            $error = $lang['255']; //"Username already taken";
        }
        else
        {
        $client_id = Trim($_SESSION['oauth_uid']);
        $query = "UPDATE users SET username='$new_username' WHERE oauth_uid='$client_id'";
        mysqli_query($con,$query);
        if(mysqli_error($con))
        {
        $error = $lang['256']; //"Unable to post on database! Contact Support!";
        }
        else
        {
            $successMsg = $lang['257']; //"Username changed successfully";
            unset($_SESSION['username']);
            $_SESSION['username'] = $new_username;
        }
        }
        }
        else
        {
            $error = $lang['258']; //"Username not vaild";
            $username = Trim($_SESSION['username']);
        }
        }
    }
}

?>