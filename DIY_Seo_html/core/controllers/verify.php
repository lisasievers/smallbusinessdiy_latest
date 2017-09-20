<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: Rainbow PHP Framework v1.0
* @copyright © 2015 ProThemes.Biz
*
*/

$username = raino_trim($_GET['username']);
$code = raino_trim($_GET['code']);

$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($con, $query);
if (mysqli_num_rows($result) > 0)
{
    // Username found
    while ($row = mysqli_fetch_array($result))
    {
        $db_oauth_uid = $row['oauth_uid'];
        $db_email_id = Trim($row['email_id']);
        $db_full_name = $row['full_name'];
        $db_platform = $row['platform'];
        $db_password = Trim($row['password']);
        $db_verified = $row['verified'];
        $db_picture = $row['picture'];
        $db_date = $row['date'];
        $db_ip = $row['ip'];
        $db_id = $row['id'];
    }
    $ver_code = Md5(HASH_CODE . $db_email_id . HASH_CODE);

    if ($db_verified == '1')
    {
        die($lang['303']);
    }
    if ($ver_code == $code)
    {
        $query = "UPDATE users SET verified='1' WHERE username='$username'";
        mysqli_query($con, $query);
        if (mysqli_error($con))
        {
            $error = $lang['304'];
        } else
        {
            echo $lang['305'];
            header("Location: ../");
            echo '<meta http-equiv="refresh" content="1;url=../">';
            exit();
        }
    } else
    {
        die($lang['306']);
    }
} else
{
    die($lang['307']);
}
die();
?>