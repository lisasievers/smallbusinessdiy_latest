<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$query = mysqli_query($con, "SELECT * FROM pages WHERE page_url='$pointOut'");
if (mysqli_num_rows($query) > 0)
{
    $data = mysqli_fetch_array($query);
    
    $editID = $data['id'];
    $page_title = $data['page_title'];
    $page_url = $data['page_url'];
    $meta_des = $data['meta_des'];
    $page_name = $data['page_name'];
    $posted_date = $data['posted_date'];
    $meta_tags = $data['meta_tags'];
    $header_show = filter_var($data['header_show'], FILTER_VALIDATE_BOOLEAN);
    $footer_show = filter_var($data['footer_show'], FILTER_VALIDATE_BOOLEAN);
    $page_content = htmlspecialchars_decode($data['page_content']);
} else
{
    require_once (CON_DIR . "error.php");
}

$posted_date_raw=date_create($posted_date);
$post_month = date_format($posted_date_raw,"M");
$post_day = date_format($posted_date_raw,"j");

$p_title = $page_title;
$des = $meta_des;
$keyword = $meta_tags;


?>