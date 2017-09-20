<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Manage Users";

if (isset($_GET['delete']))
{
    $user_id = raino_trim($_GET['delete']);
    $query = "DELETE FROM users WHERE id=$user_id";
    $result = mysqli_query($con, $query);
    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
     <strong>Alert!</strong> ' . mysqli_error($con) . '
     </div>';
    } else
    {
        $msg = '<div class="alert alert-success alert-dismissable">
         <strong>Alert!</strong> User deleted from database successfully
         </div>';
    }

}

if (isset($_GET['ban']))
{
    $ban_id = raino_trim($_GET['ban']);
    $query = "UPDATE users SET verified='2' WHERE id='$ban_id'";
    $result = mysqli_query($con, $query);
    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
     <strong>Alert!</strong> ' . mysqli_error($con) . '
     </div>';
    } else
    {
        $msg = '<div class="alert alert-success alert-dismissable">
         <strong>Alert!</strong> User banned successfully
         </div>';
    }

}

if (isset($_GET['unban']))
{
    $ban_id = raino_trim($_GET['unban']);
    $query = "UPDATE users SET verified='1' WHERE id='$ban_id'";
    $result = mysqli_query($con, $query);
    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
     <strong>Alert!</strong> ' . mysqli_error($con) . '
     </div>';
    } else
    {
        $msg = '<div class="alert alert-success alert-dismissable">
         <strong>Alert!</strong> User un-banned successfully
         </div>';
    }

}

if (isset($_GET['export'])) {
    
    function sendHeaders($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    
    sendHeaders("data_export_" . date("Y-m-d") . ".csv");
    
    $idsList = array();
    $out = fopen('php://output', 'w');
    $query = "SELECT * FROM users";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        $idsList = array($row['email_id']);
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($out, $idsList);
    }
    fclose($out);
    die();
}

if (isset($_GET['details']))
{
    $detail_id = raino_trim($_GET['details']);
    $query = "SELECT * FROM users WHERE id='$detail_id'";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result))
    {
        $user_oauth_uid = $row['oauth_uid'];
        $user_username = $row['username'];
        $user_email_id = $row['email_id'];
        $user_full_name = $row['full_name'];
        $user_platform = Trim($row['platform']);
        $user_verified = $row['verified'];
        $user_date = $row['date'];
        $user_ip = $row['ip'];
    }
    if ($user_oauth_uid == '0')
    {
        $user_oauth_uid = "None";
    }
    if ($user_verified == '0')
    {
        $user_verified = "Not verfied user";
    } elseif ($user_verified == '1')
    {
        $user_verified = "Verfied User / Active";
    } elseif ($user_verified == '2')
    {
        $user_verified = "Banned User";
    }
}
?>