<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Manage Site";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $title = escapeTrim($con, $_POST['title']);
    $des = escapeTrim($con, $_POST['des']);
    $keyword = escapeTrim($con, $_POST['keyword']);
    $site_name = escapeTrim($con, $_POST['site_name']);
    $email = escapeTrim($con, $_POST['email']);
    $twit = escapeTrim($con, $_POST['twit']);
    $face = escapeTrim($con, $_POST['face']);
    $gplus = escapeTrim($con, $_POST['gplus']);
    $copyright = escapeTrim($con, $_POST['copyright']);
    $ga = escapeTrim($con, $_POST['ga']);
    $footer_tags = escapeTrim($con, $_POST['footer_tags']);
    $forceHttps = filter_var($_POST['https'], FILTER_VALIDATE_BOOLEAN);
    $forceWww = filter_var($_POST['www'], FILTER_VALIDATE_BOOLEAN);
    $doForce = serialize(array($forceHttps,$forceWww));
    $query = "UPDATE site_info SET title='$title', des='$des', keyword='$keyword', site_name='$site_name', email='$email', twit='$twit', face='$face', gplus='$gplus', copyright='$copyright', ga='$ga', footer_tags='$footer_tags', doForce='$doForce' WHERE id='1'";
    mysqli_query($con, $query);

    if (mysqli_errno($con))
    {
        $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> ' . mysqli_error($con) . '
                                    </div>';
    } else
    {
        $msg = '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Site information saved successfully
                                    </div>';
    }

}

$query = "SELECT * FROM site_info";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $title = Trim($row['title']);
    $des = Trim($row['des']);
    $keyword = Trim($row['keyword']);
    $site_name = Trim($row['site_name']);
    $email = Trim($row['email']);
    $twit = Trim($row['twit']);
    $face = Trim($row['face']);
    $gplus = Trim($row['gplus']);
    $copyright = Trim($row['copyright']);
    $ga = Trim($row['ga']);
    $footer_tags =  Trim($row['footer_tags']);
    $doForce = unserialize($row['doForce']);
    $forceHttps = filter_var($doForce[0], FILTER_VALIDATE_BOOLEAN);
    $forceWww = filter_var($doForce[1], FILTER_VALIDATE_BOOLEAN);
}
$domain = $_SERVER['HTTP_HOST'];
?>