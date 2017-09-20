<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2016 ProThemes.Biz
 *
 */

//Load Capthca Settings
$result = mysqli_query($con,"SELECT * FROM capthca where id='1'");
$data = mysqli_fetch_array($result);
extract($data);   

//Generate Image Verification
$_SESSION['captcha'] = elite_captcha($color,$mode,$mul,$allowed);  

die($_SESSION['captcha']['image_src']);
?>