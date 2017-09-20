<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */

//Page Title
$p_title = $lang['297'];

//Mail
$admin_mail = $email;
$admin_name = $site_name;

// SMTP information 
$query =  "SELECT * FROM mail WHERE id='1'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
        $smtp_host =   Trim($row['smtp_host']);
        $smtp_user =  Trim($row['smtp_username']);
        $smtp_pass =  Trim($row['smtp_password']);
        $smtp_port =  Trim($row['smtp_port']);
        $protocol =  Trim($row['protocol']);
        $smtp_auth =  Trim($row['auth']);
        $smtp_sec =  Trim($row['socket']);
}

$mail_type = $protocol;

//Check already login
if(isset($_SESSION['userToken']))
{
    header("Location: ../");
    echo '<meta http-equiv="refresh" content="1;url=../">';
    $success = "You are already logged in";
}


if (isset($_GET['resend']))
{          
    if (isset($_POST['email']))
    {
        $email = escapeTrim($con,$_POST['email']);
        $query =  "SELECT * FROM users WHERE email_id='$email'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0) 
        {
        // Username found
        while($row = mysqli_fetch_array($result)) {
        $username = $row['username'];
        $db_email_id = $row['email_id'];
        $db_full_name  = $row['full_name'];
        $db_platform  = $row['platform'];
        $db_password  = Trim($row['password']);
        $db_verified  = $row['verified'];
        $db_picture = $row['picture'];
        $db_date = $row['date'];
        $db_ip = $row['ip'];
        $db_id = $row['id'];
        }
        if ($db_verified == '0')
        {
          $verify_url = 'http://' . $_SERVER['HTTP_HOST'] . "/?route=verify&username=$username&code=".Md5(HASH_CODE . $db_email_id . HASH_CODE);
          $sent_mail =  $email;
          $subject = $site_name . ' ' . $lang['278']; //"$site_name Account Confirmation";
          $body = "<br />
          Welcome and thank you for registering at $site_name <br /><br />
          
          If you are the creator of the $site_name account, please click your activation url: <br />
          
          <a href='$verify_url' target='_self'>$verify_url</a>  <br /> <br />
          
          After account confirmation, You can log in by using your username and password by visiting our website. <br /> <br />
          
          Thank you,<br />
            - The $site_name Team
          ";
      
          if ($mail_type == '1')
        {
          default_mail ($admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
        else
        {
          smtp_mail($smtp_host,$smtp_port,$smtp_auth,$smtp_user,$smtp_pass,$smtp_sec,$admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
          $success = $lang['279']; //"Activation code successfully sent to your mail id";    
          
        }
        else
        {
                $error = $lang['280']; //"Email ID already verified!";    
        }

        }
        else
        {
        $error =  $lang['281']; // "Email ID not found!";
        }
    
}
}

if (isset($_GET['forget']))
{
    if (isset($_POST['email']))
    {
        $email = escapeTrim($con,$_POST['email']);
        $query =  "SELECT * FROM users WHERE email_id='$email'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0) 
        {
        // Username found
        while($row = mysqli_fetch_array($result)) {
        $username = $row['username'];
        $db_email_id = $row['email_id'];
        $db_full_name  = $row['full_name'];
        $db_platform  = $row['platform'];
        $db_password  = Trim($row['password']);
        $db_verified  = $row['verified'];
        $db_picture = $row['picture'];
        $db_date = $row['date'];
        $db_ip = $row['ip'];
        $db_id = $row['id'];
        }
        $new_pass = md5(uniqid(rand(), true));  
        $new_pass_md5 = passwordHash($new_pass);
        
        $query = "UPDATE users SET password='$new_pass_md5' WHERE username='$username'";
        mysqli_query($con,$query);
        if(mysqli_error($con))
        {
        $error = $lang['282']; //"Unable to post on database! Contact Support!";
        }
        else
        {
        $success = $lang['283']; //"Password changed successfully and Sent to your mail";
          $sent_mail =  $email;
          $subject = $site_name. ' ' . $lang['284']; //"$site_name Password Reset";
          $body = "<br />
          Hello, <br /><br />
          
          Recently your account password has been reset by your request. Please take new password to login. <br /><br />
          
          Your New Password:  $new_pass  <br /> <br />
          
          You can log in by using your username and new password by visiting our website. <br /> <br />
          
          Thank you,<br />
            - The $site_name Team
          ";
          if ($mail_type == '1')
        {
          default_mail ($admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
        else
        {
          smtp_mail($smtp_host,$smtp_port,$smtp_auth,$smtp_user,$smtp_pass,$smtp_sec,$admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
        
        }
        
        }
        else
        {
        $error = $lang['281']; // "Email ID not found!";  
        }
    
    }
    
}
if ($_SERVER['REQUEST_METHOD'] == POST)
{
    //Already login
    if(isset($_SESSION['userToken']))
    {
    header("Location: ../");
    echo '<meta http-equiv="refresh" content="1;url=../">';
    $success =$lang['285']; // "You are already logged in";
    }else{
    // Login process
    if (isset($_POST['signin']))
    {
        $username = escapeTrim($con,$_POST['username']);
        $password = md5(raino_trim($_POST['password']));
//      echo 'rr'; exit;
        if ($username != null && $password!= null)
        {
        
        $query =  "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0) 
        {
        // Username found
        while($row = mysqli_fetch_array($result)) {
        $db_oauth_uid = $row['oauth_uid'];
        $db_email_id = $row['email_id'];
        $db_full_name  = $row['full_name'];
        $db_platform  = $row['platform'];
        $db_password  = Trim($row['password']);
        $db_verified  = $row['verified'];
        $db_picture = $row['picture'];
        $db_date = $row['date'];
        $db_ip = $row['ip'];
        $db_id = $row['id'];
        }

        if ($password == "$db_password")
        {
        if ($db_verified == "1")
        { 
            // Login Success
            $_SESSION['userToken'] = passwordHash($db_id . $username);
            $_SESSION['token'] = Md5($db_id.$username);
            $_SESSION['oauth_uid'] = $db_oauth_uid;
            $_SESSION['username'] = $username;
            
            $success = $lang['286']; // "Login Successful..";
        }
        elseif ($db_verified == "2")
        {
        // Account not verified
         $error = $lang['287']; //"Oh, no your account was banned! Contact Support..";
        }
        else
        {
        // Account not verified
         $error = $lang['288']; //"Oh, no account not verified";
        }              
        }
        else
        {
                // Password wrong
            $error =  $lang['289']; //"Oh, no password is wrong";
            
        }
        }
        else
        {
        // Username not found
            $error =  $lang['290']; //"Username not found";
        }
        }
        else
        {
        $error =$lang['291']; //"All fields must be filled out!";
        }
    }
    
    // Register process
        if (isset($_POST['signup']))
        {
        $username = escapeTrim($con,$_POST['username']);
        $password = passwordHash(raino_trim($_POST['password']));
        $email = escapeTrim($con,$_POST['email']);
        $full_name = escapeTrim($con,$_POST['full']);

        if ($username != null && $password!= null && $full_name!=null && $email!= null)
        {
        if(isValidEmail($email)){  
        if (isValidUsername($username))
        {
        $query =  "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0)  
        {
            $error = $lang['292']; // "Username already taken";
        }
        else
        {
                   
        $query =  "SELECT * FROM users WHERE email_id='$email'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0) 
        {
            $error =  $lang['293']; //"Email ID already registered";
        }
        else
        { 
        $query =  "SELECT * FROM users WHERE date='$date' AND ip='$ip'";
        $result = mysqli_query($con,$query);
        if(mysqli_num_rows($result) > 0) 
        {
            $error =  $lang['336']; //"It looks like your IP has already been used to register an account today!";
        }
        else{
        $query = "INSERT INTO users (oauth_uid,username,email_id,full_name,platform,password,verified,picture,date,ip) VALUES ('0','$username','$email','$full_name','Direct','$password','0','NONE','$date','$ip')"; 
        mysqli_query($con,$query); 
        if (mysqli_error($con))
        $error = $lang['296'];
        else
        {
        $success =  $lang['294']; //"Your account was successfully registered";
        $verify_url = 'http://' . $_SERVER['HTTP_HOST'] . "/?route=verify&username=$username&code=".Md5(HASH_CODE . $email . HASH_CODE);

          $sent_mail =  $email;
          $subject = $site_name . ' ' . $lang['278']; //"$site_name Account Confirmation";
          $body = "<br />
          Welcome and thank you for registering at $site_name <br /><br />
          
          If you are the creator of the $site_name account, please click your activation url: <br />
          
          <a href='$verify_url' target='_self'>$verify_url</a>  <br /> <br />
          
          After account confirmation, You can log in by using your username and password by visiting our website. <br /> <br />
          
          Thank you,<br />
            - The $site_name Team
          ";
        if ($mail_type == '1')
        {
          default_mail ($admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
        else
        {
          smtp_mail($smtp_host,$smtp_port,$smtp_auth,$smtp_user,$smtp_pass,$smtp_sec,$admin_mail,$admin_name,$sent_mail,$subject,$body);
        }
        }
        }
        }
        }
    }else{
    $error =  $lang['295']; //"Username not valid! Username can't contain special characters..";
    }
    }
    else
    {
    $error =  $lang['227']; //"Email ID not valid!";
    }
    }
    else
    {
        $error = $lang['291']; //"All fields must be filled out!";
    }
    }
    }
}

?>