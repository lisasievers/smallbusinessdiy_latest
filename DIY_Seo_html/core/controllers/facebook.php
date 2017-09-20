<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

if(!$enable_reg){
    header("Location: ../");
    exit();  
}

if(!$enable_oauth){
    header("Location: ../");
    exit();  
}

// Oauth Facebook
define('FB_APP_ID', $fb_app_id);   // Enter your facebook application id
define('FB_APP_SECRET', $fb_app_secret);    // Enter your facebook application secret code

//Facebook Oauth Library
require_once (LIB_DIR . 'facebook/facebook.php');

$facebook = new Facebook(array(
    'appId' => FB_APP_ID,
    'secret' => FB_APP_SECRET,
    ));

$user = $facebook->getUser();

if ($user)
{
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me?fields=id,name,email,first_name,last_name');
    }
    catch (FacebookApiException $e)
    {

        $user = null;
    }

    if (!empty($user_profile))
    {
        # User info ok? Let's print it (Here we will be adding the login and registering routines)

        $client_name = $user_profile['name'];
        $client_id = $user_profile['id'];
        $client_email = $user_profile['email'];
        $client_pic = $user_profile['picture'];
        $client_plat = 'Facebook';


        if (!empty($user_profile))
        {
            $query = mysqli_query($con, "SELECT * FROM users WHERE oauth_uid='$client_id'");
            if (mysqli_num_rows($query) > 0)
            {
                $query = "SELECT * FROM users WHERE oauth_uid='$client_id'";
                $result = mysqli_query($con, $query);
                while ($row = mysqli_fetch_array($result))
                {
                    $user_username = $row['username'];
                    $db_verified = $row['verified'];
                }
                if ($db_verified == "2")
                {
                    die($lang['260']);
                } else
                {

                    $_SESSION['username'] = $user_username;
                    $_SESSION['token'] = Md5($db_id . $username);
                    $_SESSION['oauth_uid'] = $client_id;
                    $_SESSION['pic'] = $client_pic;
                    $_SESSION['userToken'] = passwordHash($db_id . $username);
                    
                    $old_user = 1;
                    header("Location: ../");
                    exit();
                }
            } else
            {
                $new_user = 1;
                #user not present.
                $query = "SELECT @last_id := MAX(id) FROM users";
                $result = mysqli_query($con, $query);
                while ($row = mysqli_fetch_array($result))
                {
                    $last_id = $row['@last_id := MAX(id)'];
                }
                if ($last_id == "" || $last_id == null)
                {
                    $username = "User1";
                } else
                {
                    $last_id = $last_id + 1;
                    $username = "User$last_id";
                }
                $_SESSION['username'] = $username;
                $_SESSION['oauth_uid'] = $client_id;
                $_SESSION['token'] = Md5($db_id . $username);
                $_SESSION['userToken'] = passwordHash($db_id . $username);
                $query = "INSERT INTO users (oauth_uid,username,email_id,full_name,platform,password,verified,picture,date,ip) VALUES ('$client_id','$username','$client_email','$client_name','$client_plat','$password','1','$client_pic','$date','$ip')";
                mysqli_query($con, $query);
                header("Location: ../?route=oauth&new_user&successInt");
                exit();
            }

        }
    } else
    {
        # For testing purposes, if there was an error, let's kill the script
        die($lang['261']);
    }
} else
{
    if (isset($_GET['login']))
    {
        # There's no active session, let's generate one
        $login_url = $facebook->getLoginUrl(array('scope' => 'email'));
        header("Location: " . $login_url);
        exit();
    }
}
die();
?>