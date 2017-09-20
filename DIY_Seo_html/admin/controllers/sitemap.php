<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
* @author Balaji
* @name: A to Z SEO Tools
* @copyright © 2015 ProThemes.Biz
*
*/

$fullLayout = 1;
$p_title = "Sitemap";

if ($_SERVER['REQUEST_METHOD'] == POST)
{
    $priority =  raino_trim($_POST['priority']);
    $changefreq =   raino_trim($_POST['changefreq']);

    $query = "UPDATE sitemap_options SET priority='$priority', changefreq='$changefreq' WHERE id='1'"; 
    mysqli_query($con,$query); 
      
    if (mysqli_errno($con)) {   
    $msg = '<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> '.mysqli_error($con).'
                                    </div>';
    }
    else
    {
        $msg =  '
        <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <b>Alert!</b> Sitemap info saved successfully
                                    </div>';
    }
}

$query = "Select * From sitemap_options WHERE id='1'"; 
$result = mysqli_query($con,$query); 
    
while($row = mysqli_fetch_array($result)) {
    $priority =  $row['priority'];
    $changefreq =  $row['changefreq'];
}
    
if (isset($_GET['re']))
{
    if(file_exists(ROOT_DIR.'sitemap.xml')){
    delFile(ROOT_DIR.'sitemap.xml');
    }

    $c_date = date('Y-m-d');
    
    $data = 
'<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        
        <url>
            <loc>http://' . $_SERVER['SERVER_NAME'] . '/</loc>
            <priority>1.0</priority>
            <changefreq>daily</changefreq>
            <lastmod>' . $c_date . '</lastmod>
        </url>
        
        <url>
            <loc>http://' . $_SERVER['SERVER_NAME'] . '/contact</loc>
            <priority>' . $priority . '</priority>
            <changefreq>' . $changefreq . '</changefreq>
            <lastmod>' . $c_date . '</lastmod>
        </url>';
    
    
        $query = "SELECT * FROM pages";
        $result = mysqli_query($con, $query);
    
        while ($row = mysqli_fetch_array($result))
        {
            $page_url = Trim($row['page_url']);
            $server_name = "http://" . $_SERVER['SERVER_NAME'] . "/page/" . $page_url;
    
        $data = $data. '
    
        <url>
            <loc>' . $server_name . '</loc>
            <priority>' . $priority . '</priority>
            <changefreq>' . $changefreq . '</changefreq>
            <lastmod>' . $c_date . '</lastmod>
        </url>';
        }
        
        //Blog Posts
        $resDa = mysqli_query($con,"SHOW TABLES LIKE 'blog_content'");
        if(mysqli_num_rows($resDa) > 0) {

        $query = "SELECT * FROM blog_content";
        $result = mysqli_query($con, $query);
    
        while ($row = mysqli_fetch_array($result))
        {
            $post_url = Trim($row['post_url']);
            $server_name = "http://" . $_SERVER['SERVER_NAME'] . "/blog/" . $post_url;
    
        $data = $data. '
    
        <url>
            <loc>' . $server_name . '</loc>
            <priority>' . $priority . '</priority>
            <changefreq>' . $changefreq . '</changefreq>
            <lastmod>' . $c_date . '</lastmod>
        </url>';
        }
        }
        
        $query = "SELECT * FROM seo_tools";
        $result = mysqli_query($con, $query);
    
        while ($row = mysqli_fetch_array($result))
        {
            
            $tool_url = Trim($row['tool_url']);
            $tool_show = filter_var($row['tool_show'], FILTER_VALIDATE_BOOLEAN);
            
            if($tool_show) {
            $server_name = "http://" . $_SERVER['SERVER_NAME'] . "/" . $tool_url;
    
        $data = $data. '
    
        <url>
            <loc>' . $server_name . '</loc>
            <priority>' . $priority . '</priority>
            <changefreq>' . $changefreq . '</changefreq>
            <lastmod>' . $c_date . '</lastmod>
        </url>';
            }
        }

        $data = $data.'
    </urlset>';
        putMyData(ROOT_DIR."sitemap.xml", $data);  
        
        $msg = '<div class="alert alert-success alert-dismissable">
                    <i class="fa fa-check"></i>
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <b>Alert!</b> Sitemap build successfully
                </div>';
}
$sitemapData = false;
if(file_exists(ROOT_DIR.'sitemap.xml')){
    $siteMapRes = "File Found";
    $sitemapData = true;
}else{
    $siteMapRes = "File Not Found";
}
?>