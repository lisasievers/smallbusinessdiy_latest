<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Ban User IP";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $ban_ip = escapeTrim($con, $_POST['ban_ip']);
    if (!filter_var($ban_ip, FILTER_VALIDATE_IP) === false) {
    $query = "INSERT INTO ban_user (last_date,ip) VALUES ('$date','$ban_ip')";
    mysqli_query($con, $query);
        if (mysqli_errno($con)) {   
    $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> '.mysqli_error($con).'
                                    </div>';
    }
    else
    {
        $msg =  '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> IP added to database successfully.
                                    </div>';
    }
    } else {
    $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> IP is not valid!
                                    </div>';
    }
}

?>