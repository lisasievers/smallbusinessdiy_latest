<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
$fullLayout = 1;
$p_title = "Website Advertisement";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $ad720x90 = escapeTrim($con, $_POST['ad720x90']);
    $ad250x300 = escapeTrim($con, $_POST['ad250x300']);
    $ad250x125 = escapeTrim($con, $_POST['ad250x125']);
    $ad480x60 = escapeTrim($con, $_POST['ad480x60']);
    $text_ads = escapeTrim($con, $_POST['text_ads']);

    $query = "UPDATE ads SET ad720x90='$ad720x90', ad250x300='$ad250x300', ad250x125='$ad250x125', ad480x60='$ad480x60', text_ads='$text_ads' WHERE id='1'";
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
                                        <b>Alert!</b> Ads Settings saved successfully
                                    </div>';
    }

}

$query = "SELECT * FROM ads";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $ad720x90 = Trim($row['ad720x90']);
    $ad250x300 = Trim($row['ad250x300']);
    $ad250x125 = Trim($row['ad250x125']);
    $ad480x60 = Trim($row['ad480x60']);
    $text_ads = Trim($row['text_ads']);
}
?>