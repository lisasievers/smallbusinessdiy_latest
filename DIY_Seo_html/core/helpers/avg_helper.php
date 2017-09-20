<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2016 ProThemes.Biz
 *
 */
 
function avgCheck($host) {
    
    $avgUrl = 'http://www.avgthreatlabs.com/website-safety-reports/domain/'.$host;
    
    $avgData = curlGET($avgUrl,'http://www.avgthreatlabs.com');
    $avgData = explode('<div class="rating',$avgData);
    $avgData = explode('">',$avgData[1]);
    $resStats = Trim(strtolower($avgData[0]));

    if($resStats == 'green')
        return '1';
    elseif($resStats == 'yellow')
        return '2';
    elseif($resStats == 'orange')
        return '2';
    elseif($resStats == 'red')
        return '3';
    else
        return '0';
}

?>