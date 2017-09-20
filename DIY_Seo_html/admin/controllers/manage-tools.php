<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
$fullLayout = 1;
$p_title = "Manage SEO Tools";

if(isset($_GET['disable'])){
    $dID = raino_trim($_GET['id']);
    $query = "UPDATE seo_tools SET tool_show='no' WHERE id='$dID'";
    if (!mysqli_query($con,$query))
    {
    $msg = '<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Something Went Wrong!
    </div>';
    }else{
    $msg = '<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> SEO Tool disabled Successfully!
    </div>';
    }
}

if(isset($_GET['enable'])){
    $dID = raino_trim($_GET['id']);
    $query = "UPDATE seo_tools SET tool_show='yes' WHERE id='$dID'";
    if (!mysqli_query($con,$query))
    {
    $msg = '<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> Something Went Wrong!
    </div>';
    }else{
    $msg = '<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <b>Alert!</b> SEO Tool enabled Successfully!
    </div>';
    }

}

$query =  "SELECT * FROM seo_tools";
$result = mysqli_query($con,$query);

while($row = mysqli_fetch_array($result)){
  $toolList[]=$row;  
}
?>