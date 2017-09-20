<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */

function alexaRank($site)
{

    $xml = simplexml_load_file('http://data.alexa.com/data?cli=10&dat=snbamz&url=' .
        $site);
    $a = $xml->SD[1]->POPULARITY;
    if ($a != null)
    {
        $alexa_rank = $xml->SD[1]->POPULARITY->attributes()->TEXT;
        $alexa_rank = ($alexa_rank==null ? 'No Global Rank' : $alexa_rank);
    } else
    {
        $alexa_rank = 'No Global Rank';
    }
    
    $a1 = $xml->SD[1]->COUNTRY;
    if ($a1 != null)
    {
        $alexa_pop = $xml->SD[1]->COUNTRY->attributes()->NAME;
        $regional_rank = $xml->SD[1]->COUNTRY->attributes()->RANK;
        $alexa_pop = ($alexa_pop==null ? 'None' : $alexa_pop);
        $regional_rank = ($regional_rank==null ? 'None' : $regional_rank);

    } else
    {
        $alexa_pop = 'None';
        $regional_rank = 'None';
    }
    
    $a2 = $xml->SD[0]->LINKSIN;
    if ($a2 != null)
    {
        $alexa_back = $xml->SD[0]->LINKSIN->attributes()->NUM;

        if ($alexa_back==null)
        {
            $outData = getMyData("http://www.alexa.com/siteinfo/$site");
            $back = explode('<span class="font-4 box1-r">',$outData);
            $back = explode('</span>',$back[1]);
            $alexa_back = $back[0];
        }
    } else
    {
        $alexa_back = '0';
    }
    $alexa_back = ($alexa_back==null ? '0' : $alexa_back);
    return array($alexa_rank,$alexa_pop,$regional_rank,$alexa_back);
}

?>