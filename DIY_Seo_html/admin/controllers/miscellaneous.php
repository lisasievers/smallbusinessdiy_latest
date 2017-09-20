<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
$fullLayout = 1;
$p_title = "Miscellaneous";

if(isset($_GET['action_1'])){
    $query = "DELETE FROM page_view"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
    $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> '.mysqli_error($con).'
     </div>';
     
    }
    else
    {
         $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> All site information has been successfully deleted.
         </div>';
    }
}

if(isset($_GET['action_2'])){
    
    $query = "DELETE FROM user_input_history"; 
    mysqli_query($con,$query); 
    
    $query = "DELETE FROM recent_history"; 
    mysqli_query($con,$query); 
    
    if (mysqli_errno($con)) {   
    $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> '.mysqli_error($con).'
     </div>';
     
    }
    else
    {
         $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> Users History has been deleted successfully.
         </div>';
    }
    
}

if(isset($_GET['action_3'])){
    
    $query = "DELETE FROM admin_history"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
    $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> '.mysqli_error($con).'
     </div>';
     
    }
    else
    {
         $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> Admin logged in history has been deleted successfully
         </div>';
    }
}

if(isset($_GET['action_4'])){
    
    $query = "DELETE FROM users where verified='0'"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
    $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> '.mysqli_error($con).'
     </div>';
     
    }
    else
    {
         $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> All not verified user accounts has been deleted successfully
         </div>';
    }
    
}

if(isset($_GET['action_5'])){
    $delSSDir = HEL_DIR."site_snapshot";
    
    $files = array_diff(scandir($delSSDir), array('.', '..','no-preview.png','index.php'));

    foreach ($files as $file)
    {
        (is_dir("$delSSDir/$file")) ? delDir("$delSSDir/$file") : unlink("$delSSDir/$file");
    }

    $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> All screenshot data has been deleted successfully
         </div>';
}

if(isset($_GET['action_6'])){

    $query = "DELETE FROM users"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
    $msg =  '<div class="alert alert-danger alert-dismissable">
     <button data-dismiss="alert" class="close" type="button">&times;</button>
     <strong>Alert!</strong> '.mysqli_error($con).'
     </div>';
     
    }
    else
    {
         $msg = '<div class="alert alert-success alert-dismissable">
         <button data-dismiss="alert" class="close" type="button">&times;</button>
         <strong>Alert!</strong> All users accounts has been deleted successfully
         </div>';
    }
}


?>