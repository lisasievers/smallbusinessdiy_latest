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

// Oauth Google 
define('G_Client_ID', $g_client_id);  // Enter your google api application id
define('G_Client_Secret', $g_client_secret); // Enter your google api application secret code
define('G_Redirect_Uri', $g_redirect_uri);
define('G_Application_Name', 'AtoZ_SEO_Tools');

//Google Oauth Library
require_once (LIB_DIR . 'Google/Client.php');

$client = new Google_Client();
$client->setScopes(array(
    "https://www.googleapis.com/auth/userinfo.profile",
    "https://www.googleapis.com/auth/userinfo.email"
));

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $access_token = json_decode($_SESSION['access_token'], 1);
  $access_token = $access_token['access_token'];
  $resp = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$access_token);
  $user = json_decode($resp, 1);  
  $client_email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
  $client_name = filter_var($user['name'], FILTER_SANITIZE_STRING);
  $client_id = filter_var($user['id']);
  $client_plat = "Google";
  $client_pic = $user['picture'];
  $content = $user;
  $token = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
}
if ($client->getAccessToken() && isset($_GET['url'])) {

  $_SESSION['access_token'] = $client->getAccessToken();
}


if (isset($client_email))
{
$query = mysqli_query($con,"SELECT * FROM users WHERE oauth_uid='$client_id'");
if(mysqli_num_rows($query) > 0){
$query =  "SELECT * FROM users WHERE oauth_uid='$client_id'";
  $result = mysqli_query($con,$query);
  while($row = mysqli_fetch_array($result)) {
  $user_username  = $row['username'];
  $db_verified  = $row['verified'];
  }
    if ($db_verified == "2")
  {
    die($lang['260']);
  }
  else
  {    
  $_SESSION['username'] = $user_username;
  $_SESSION['token'] = $token;
  $_SESSION['oauth_uid'] = $client_id;
  $_SESSION['pic'] = $client_pic;
  $_SESSION['userToken'] = passwordHash($db_id . $username);
  
  $old_user =1;
  header("Location: ../");
  exit();
  }

} else {
   $new_user= 1;
  #user not present.
  $query =  "SELECT @last_id := MAX(id) FROM users";
  $result = mysqli_query($con,$query);
  while($row = mysqli_fetch_array($result)) {
  $last_id =  $row['@last_id := MAX(id)'];
  }
  if ($last_id== "" || $last_id==null)
  {
      $username = "User1";
  }
  else
  {
      $last_id = $last_id+1;  
      $username = "User$last_id";
  }
  $_SESSION['username'] = $username;
  $_SESSION['token'] = $token;
  $_SESSION['oauth_uid'] = $client_id;
  $_SESSION['pic'] = $client_pic;
  $_SESSION['userToken'] = passwordHash($db_id . $username);
  
  $query = "INSERT INTO users (oauth_uid,username,email_id,full_name,platform,password,verified,picture,date,ip) VALUES ('$client_id','$username','$client_email','$client_name','$client_plat','$password','1','$client_pic','$date','$ip')"; 
  mysqli_query($con,$query); 
  header("Location: ../?route=oauth&new_user&successInt");
  exit();
}

}
else
{
if(isset($_GET['login']))
{
header("Location: $authUrl");
exit();
}
}
die();
?>