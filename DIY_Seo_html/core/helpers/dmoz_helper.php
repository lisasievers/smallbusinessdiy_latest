<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */
 

function dmozCheck($site,$str1="Listed",$str2="Not Listed")
{
    $mydata = getMyData("http://www.dmoz.org/search?q=$site");
    return strpos($mydata, "Categories") ? $str1 : $str2;
}

?>