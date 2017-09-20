<?php
session_start();

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.1.2
 * @copyright © 2016 ProThemes.Biz
 *
 */
 
//Admin Section
define('APP_NAME','A to Z SEO Tools');
define('VER_NO','1.6');
define('ADMIN_DIR', realpath(dirname(__FILE__)) .DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(dirname(dirname(__FILE__))) .DIRECTORY_SEPARATOR);
define('APP_DIR', ROOT_DIR .'core'.DIRECTORY_SEPARATOR);
define('ADMIN_CON_DIR', ADMIN_DIR.'controllers'.DIRECTORY_SEPARATOR);

$admin_theme = "default";
define('ADMIN_THEME_DIR', ADMIN_DIR.'theme' . DIRECTORY_SEPARATOR . $admin_theme . DIRECTORY_SEPARATOR);

if(isset($_GET['logout'])) {
if(isset($_SESSION['adminToken'])){
unset($_SESSION['adminToken']);
unset($_SESSION['adminID']);
}
session_destroy();
header("Location: /admin/");
echo '<meta http-equiv="refresh" content="1;url=/admin/">';
exit();
}

// Required functions
require(ROOT_DIR.'config.php');
require(APP_DIR.'functions.php');

//Database Connection
$con = dbConncet($dbHost,$dbUser,$dbPass,$dbName);

//Start the Application
require(ADMIN_DIR.'app.php');

if($fullLayout){
//Theme & Output
require_once(ADMIN_THEME_DIR.'header.php');
require_once(ADMIN_THEME_DIR.VIEW.'.php');
require_once(ADMIN_THEME_DIR.'footer.php');
}else{
require_once(ADMIN_THEME_DIR.VIEW.'.php');
}

//Close the database conncetion
mysqli_close($con);
?>