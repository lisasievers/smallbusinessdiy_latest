<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */

if(isset($_SESSION['adminToken'])){
header("Location: /admin/");
echo '<meta http-equiv="refresh" content="1;url=/admin/">';
exit();
} 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    $emailBox = escapeTrim($con,$_POST['email']);
    $passwordBox = passwordHash(escapeTrim($con,$_POST['password']));
        
    $query = mysqli_query($con, "SELECT * FROM admin WHERE user='$emailBox'");
    if (mysqli_num_rows($query) > 0)
    {
    $row = mysqli_fetch_array($query);
    $adminPssword =   Trim($row['pass']);
    $adminID =   Trim($row['id']);
    if ($adminPssword == $passwordBox) {
        
      $msg = '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                                        <b>Alert!</b> Login Successful. Redirect to dashboard page wait...
                                    </div>';
    $_SESSION['adminToken'] = true;
    $_SESSION['adminID'] = $adminID;
    echo '<meta http-equiv="refresh" content="1;url=/admin/">';
    }
    else
    {
    $msg = '  <div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                                        <b>Alert!</b> Password is Wrong. Try Again! 
                                    </div> ';
    }
  }
  else
  {
    $msg = ' <div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Login Failed. Try Again! 
                                    </div> ';
  }
}


?>