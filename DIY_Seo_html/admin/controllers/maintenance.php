<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Maintenance Settings";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $maintenance_mode = escapeTrim($con, $_POST['maintenance_mode']);
    $maintenance_mes = escapeTrim($con, $_POST['maintenance_mes']);

    $query = "UPDATE maintenance SET maintenance_mode='$maintenance_mode', maintenance_mes='$maintenance_mes' WHERE id='1'";
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
                                        <b>Alert!</b> Maintenance settings saved successfully
                                    </div>';
    }

}

//Load Maintenance Settings
$query =  "SELECT * FROM maintenance WHERE id='1'";
$result = mysqli_query($con,$query);
        
while($row = mysqli_fetch_array($result)) {
    $maintenance_mode =  filter_var(Trim($row['maintenance_mode']), FILTER_VALIDATE_BOOLEAN);
    $maintenance_mes =   Trim($row['maintenance_mes']);
}
?>