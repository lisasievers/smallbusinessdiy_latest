<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2016 ProThemes.Biz
 *
 */
 
function googleIndex($site) {

    $apiKey = 'AIzaSyCVAXiUzRYsML1Pv6RwSG1gunmMikTzQqY';
    $lang = 'en';
    $cx = '017909049407101904515:uifmlkwue1w';
    $searchQuery = urlencode("site:$site");
    $googleDomain = 'www.google.com';
    
    $url = 'https://www.googleapis.com/customsearch/v1element?key='.$apiKey.'&rsz=filtered_cse&num=10&hl='.$lang.
    '&prettyPrint=false&source=gcsc&gss=.com&sig=3aa157001604e3bc243e85b7344d5d15&cx='.$cx.'&q='.$searchQuery.'&googlehost='.$googleDomain.'&oq='.$searchQuery.'&gs_l=partner.12...0.0.3.18708.0.0.0.0.0.0.0.0..0.0.gsnos%2Cn%3D13...0.0jj1..1ac..25.partner..0.0.0.&callback=google.search.Search.apiary9718';
    $data = curlGET($url);
    $data = explode('google.search.Search.apiary9718(',$data);
    $data = explode(');',$data[1]);
    $data = $data[0];
    $data = json_decode($data, true);
    $dataRes = $data['cursor']['estimatedResultCount'];
    if ($dataRes == '')
        $dataRes = 0;
    return number_format($dataRes);
}

?>