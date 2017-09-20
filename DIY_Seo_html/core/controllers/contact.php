<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
 
$p_title = "Contact Us";
$des = "";
$keyword = "";

// Load Capthca
$query =  "SELECT * FROM capthca where id='1'";
$result = mysqli_query($con,$query);
       	 	 	 	   
while($row = mysqli_fetch_array($result)) {
$color =  Trim($row['color']);
$mode =   Trim($row['mode']);
$mul =  Trim($row['mul']);
$allowed =   Trim($row['allowed']);
$cap_e = Trim($row['cap_e']);
$cap_c = Trim($row['cap_c']);
}
if ($cap_c == "on")
{
$_SESSION['captcha'] = elite_captcha($color,$mode,$mul,$allowed);    
}


?>