<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */

require_once (LIB_DIR . 'geoip.inc');
$gi = geoip_open('../core/library/GeoIP.dat', GEOIP_MEMORY_CACHE);

$fullLayout = 1;
$p_title = "Admin Account";
$passPage = false;
$avatarPage = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['logoID']))
    {
    $avatarPage = true;
    $target_dir = "..$theme_path"."dist/img/";
    $target_filename = basename($_FILES["logoUpload"]["name"]);
    $target_file = $target_dir . $target_filename;
    $uploadSs = 1;
    $check = getimagesize($_FILES["logoUpload"]["tmp_name"]);
    // Check it is a image
    if ($check !== false)
    {
        // Check if file already exists
        if (file_exists($target_file))
        {
            $target_filename = rand(1, 99999) . "_" . $target_filename;
            $target_file = $target_dir . $target_filename;
        }
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check file size
        if ($_FILES["logoUpload"]["size"] > 500000)
        {
            $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> Sorry, your file is too large.
     </div>';
            $uploadSs = 0;
        } else
        {
            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType !=
                "jpeg" && $imageFileType != "gif")
            {
                $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> Sorry, only JPG, JPEG, PNG & GIF files are allowed.
     </div>';
                $uploadSs = 0;
            }
        }

        // Check if $uploadSs is set to 0 by an error
        if (!$uploadSs == 0)
        {
            if (move_uploaded_file($_FILES["logoUpload"]["tmp_name"], $target_file))
            {
                $msg = '<div class="alert alert-success alert-dismissable" >
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> Image was successfully uploaded
         </div>';
         $file_path = "dist/img/$target_filename";
         $query = "UPDATE admin SET admin_logo='$file_path' WHERE id='1'"; 
         mysqli_query($con,$query); 
         
            } else
            {
                $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> Sorry, there was an error uploading your file.
     </div>';
            }
        }

    } else
    {
        $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> File is not an image.
     </div>';
    }
    
    }

    if(isset($_POST['passChange'])){
    $passPage = true;
    $query = "SELECT * FROM admin";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result))
    {
    $admin_oldPass = Trim($row['pass']);
    }

    $admin_user = escapeTrim($con, $_POST['admin_user']);
    $admin_name = escapeTrim($con, $_POST['admin_name']);
    $new_pass = passwordHash(escapeTrim($con, $_POST['new_pass']));
    $retype_pass = passwordHash(escapeTrim($con, $_POST['retype_pass']));
    $old_pass = passwordHash(escapeTrim($con, $_POST['old_pass']));
    
    if($new_pass == $retype_pass){
        
    if($old_pass == $admin_oldPass){
        
    $query = "UPDATE admin SET user='$admin_user', pass='$new_pass', admin_name='$admin_name' WHERE id='1'";
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
                                        <b>Alert!</b> New Passwors saved successfully!
                                    </div>';
    }
    }else{
                        $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Old admin panel password is wrong!
                                    </div>';
    }
    }else{
                $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> New Password field / Retype password field can\'t matched!
                                    </div>';
    }
    

  }
}
$query = "SELECT * FROM admin";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $admin_user = Trim($row['user']);
    $admin_pass = Trim($row['pass']);
    $admin_name = Trim($row['admin_name']);
    $admin_logo = Trim($row['admin_logo']);
    $admin_reg_date = Trim($row['admin_reg_date']);
    $admin_reg_ip = Trim($row['admin_reg_ip']);
}

$query = "SELECT count(id) FROM admin_history";
$retval = mysqli_query($con,$query);
 
$row = mysqli_fetch_array($retval);
$rec_count = Trim($row[0]);

$lastID_Admin = getLastID($con,"admin_history");

$query = "SELECT * FROM admin_history WHERE id='$lastID_Admin'";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $admin_last_login_date = Trim($row['last_date']);
    $admin_last_login_ip = Trim($row['ip']);
}

if ($passPage)
{
    $page1 = "";
    $page2 = "active";
    $page3 = "";
}elseif($avatarPage){
    $page1 = "";
    $page2 = "";
    $page3 = "active";
}else
{
    $page1 = "active";
    $page2 = "";
    $page3 = "";
}


?>