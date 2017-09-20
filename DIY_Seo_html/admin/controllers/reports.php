<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Reports";
$total_page = 0;
$total_un = 0;
$total_users = 0;
$total_ban = 0;
$not_ver = 0;
$total_ban_ip = 0;

$query = "SELECT * FROM page_view";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $total_page = $total_page + Trim($row['tpage']);
    $total_un = $total_un + Trim($row['tvisit']);
}


$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $total_users = $total_users + 1;
    $p_v = Trim($row['verified']);
    if ($p_v == '2')
    {
        $total_ban = $total_ban + 1;
    }
    if ($p_v == '0')
    {
        $not_ver = $not_ver + 1;
    }
}

$query = "SELECT * FROM ban_user";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $total_ban_ip = $total_ban_ip + 1;
}

$loopXCount = 0;

$query = "SELECT * FROM page_view ORDER BY id DESC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    if($loopXCount == 10)
    break;
    $pageArr[] = array($loopXCount+1,$row['date'],$row['tvisit'],$row['tpage']);
    $loopXCount = $loopXCount+1;
}

?>