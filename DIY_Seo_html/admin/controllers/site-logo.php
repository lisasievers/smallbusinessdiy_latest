<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Site Logo";

if (isset($_POST['logoID']))
{
    $target_dir = "../uploads/";
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
         $file_path = "uploads/$target_filename";
         $query = "UPDATE image_path SET logo_path='$file_path' WHERE id='1'"; 
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

if (isset($_POST['favID']))
{
    $target_dir = "../uploads/";
    $target_filename = basename($_FILES["favUpload"]["name"]);
    $target_file = $target_dir . $target_filename;
    $uploadSs = 1;
    $check = getimagesize($_FILES["favUpload"]["tmp_name"]);
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
        if ($_FILES["favUpload"]["size"] > 500000)
        {
            $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> Sorry, your file is too large.
     </div>';
            $uploadSs = 0;
        } else
        {
            // Allow certain file formats
            if ($imageFileType != "icon" && $imageFileType != "png" && $imageFileType !=
                "ico" && $imageFileType != "gif")
            {
                $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> Sorry, only ICO, PNG & GIF files are allowed.
     </div>';
                $uploadSs = 0;
            }
        }

        // Check if $uploadSs is set to 0 by an error
        if (!$uploadSs == 0)
        {
            if (move_uploaded_file($_FILES["favUpload"]["tmp_name"], $target_file))
            {
                $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> Icon was successfully uploaded
         </div>';
         $file_path = "uploads/$target_filename";
         $query = "UPDATE image_path SET fav_path='$file_path' WHERE id='1'"; 
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

$query = "SELECT * FROM image_path where id='1'";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_array($result))
{
    $logo_path = Trim($row['logo_path']);
    $fav_path = Trim($row['fav_path']);
}
if ($logo_path == '')
    $logo_path = 'https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png';
if ($fav_path == '')
    $fav_path = 'https://storage.googleapis.com/assets-sitebuilder/images/facicon.ico';

?>