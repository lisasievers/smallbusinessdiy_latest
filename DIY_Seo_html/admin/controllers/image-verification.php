<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Captcha Protection";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $cap_e = escapeTrim($con,$_POST['cap_e']);
    $cap_c = escapeTrim($con,$_POST['cap_c']);
    $mode = escapeTrim($con,$_POST['mode']);
    $mul = escapeTrim($con,$_POST['mul']);
    $allowed = escapeTrim($con,$_POST['allowed']);
    $color =  escapeTrim($con,$_POST['color']);
    
    $query = "UPDATE capthca SET cap_e='$cap_e', cap_c='$cap_c', mode='$mode', mul='$mul', allowed='$allowed', color='$color' WHERE id='1'"; 
    mysqli_query($con,$query); 

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
                                        <b>Alert!</b> Captcha information saved successfully
                                    </div>';
    }

}

$query = "SELECT * FROM capthca WHERE id='1'";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result))
{
    $cap_e = Trim($row['cap_e']);
    $cap_c = Trim($row['cap_c']);
    $mode = Trim($row['mode']);
    $mul = Trim($row['mul']);
    $allowed = Trim($row['allowed']);
    $color = Trim($row['color']);
}

?>