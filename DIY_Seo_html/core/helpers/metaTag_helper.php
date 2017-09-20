<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */

function getMyMeta($url)
{
    //Get Data of the URL
    $html = getMydata($url);
    
    //Fix Meta Uppercase Problem
    $html = str_ireplace(array("Title","TITLE"),"title",$html);
    $html = str_ireplace(array("Description","DESCRIPTION"),"description",$html);
    $html = str_ireplace(array("Keywords","KEYWORDS"),"keywords",$html);
    $html = str_ireplace(array("Content","CONTENT"),"content",$html);  
    $html = str_ireplace(array("Meta","META"),"meta",$html);  
    $html = str_ireplace(array("Name","NAME"),"name",$html);      
    
    //Load the document and parse the meta     
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $nodes = $doc->getElementsByTagName('title');
    $title = $nodes->item(0)->nodeValue;
    $metas = $doc->getElementsByTagName('meta');

    for ($i = 0; $i < $metas->length; $i++)
    {
    $meta = $metas->item($i);
    if($meta->getAttribute('name') == 'description')
       $description = $meta->getAttribute('content');
    if($meta->getAttribute('name') == 'keywords')
        $keywords = $meta->getAttribute('content');
    }
    
    //Check Empty Data
    $site_title = ($title == '' ? "No Title" : $title);
    $site_description = ($description == '' ? "No Description" : $description);
    $site_keywords = ($keywords == '' ? "No Keywords" : $keywords);
    $arr_meta = array($site_title,$site_description,$site_keywords);
    
    //Return as Array
    return $arr_meta;
}

?>