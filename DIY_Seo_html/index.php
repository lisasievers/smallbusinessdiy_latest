<?php

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.1.2
 * @copyright © 2016 ProThemes.Biz
 *
 */

//Define about application!
define('APP_NAME','A to Z SEO Tools');
define('VER_NO','1.6');
define('ROOT_DIR', realpath(dirname(__FILE__)) .DIRECTORY_SEPARATOR);
define('APP_DIR', ROOT_DIR .'core'.DIRECTORY_SEPARATOR);

//Required functions
require(ROOT_DIR.'config.php');
require(APP_DIR.'functions.php');

//Check installation
detectInstaller();

//Check Htaccess File
if(!file_exists('.htaccess')) copy(APP_DIR.'data'.D_S.'htaccess.tdata', '.htaccess');

//Database Connection
$con = dbConncet($dbHost,$dbUser,$dbPass,$dbName);

//Start the Application
require(APP_DIR.'app.php');

//Theme & Output
require_once(THEME_DIR.'header.php');
require_once(THEME_DIR.VIEW.'.php');
require_once(THEME_DIR.'footer.php');

//Close the database conncetion
mysqli_close($con);

?>