<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));
/*
 * @author Balaji
 * @name: A to Z SEO Tools - PHP Script
 * @theme: SimpleX Style
 * @copyright © 2017 ProThemes.Biz
 *
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="Content-Language" content="en" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="icon" type="image/png" href="<?php echo $fav_path; ?>" />

        <!-- Meta Data-->
        <title><?php if(isset($p_title)) { echo $p_title.' | '. $site_name;}else { echo $title; } ?></title>
                
        <meta property="site_name" content="<?php echo $site_name; ?>"/>
        <meta name="description" content="<?php echo $des; ?>" />
        <meta name="keywords" content="<?php echo $keyword; ?>" />
        <meta name="author" content="Balaji" />
        
        <!-- Open Graph -->
        <meta property="og:title" content="<?php if(isset($p_title)) { echo $p_title.' | '. $site_name;}else { echo $title; } ?>" />
        <meta property="og:site_name" content="<?php echo $site_name; ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:description" content="<?php echo $des; ?>" />
        <meta property="og:image" content="<?php echo (substr($server_path,-1) == '/' ? substr($server_path,0,-1) : $server_path).$logo_path?>"/>
        <meta property="og:url" content="<?php echo (substr($server_path,-1) == '/' ? substr($server_path,0,-1) : $server_path).$_SERVER[REQUEST_URI]; ?>"/>

        <!-- Main style -->
        <link href="<?php echo $theme_path; ?>css/theme.css" rel="stylesheet" />
        
        <!-- Font-Awesome -->
        <link href="<?php echo $theme_path; ?>css/font-awesome.min.css" rel="stylesheet" />
        
        <!-- Animation -->
        <link href="<?php echo $theme_path; ?>css/animate.css" rel="stylesheet" type="text/css" />
        
        <!-- Custom Theme style -->
        <link href="<?php echo $theme_path; ?>css/custom.css" rel="stylesheet" type="text/css" />
        
        <link href="<?php echo $theme_path; ?>css/reset.css" rel="stylesheet" type="text/css" />
    
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <!-- jQuery 1.10.2 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

</head>

<body>   
   <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse-menu">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://localhost/sitehome/" style="height: 112px;">
                  <img alt="<?php echo $site_name; ?>" src="<?php echo $logo_path; ?>" class="atoz_seo_tools_logo" /></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="collapse-menu">
                    <ul class="nav navbar-nav navbar-right">
                       
                        <li <?php if($controller == "main") echo 'class="active"'; ?>> 
                            <a href="/"><?php echo $lang['1']; ?> </a>
                        </li>
                        <?php if(file_exists(CON_DIR."blog.php")){ ?>
                        <li>
                            <a href="/blog"><?php echo $lang['2']; ?> </a>
                        </li> 
                        <?php } ?>
                        <li <?php if($controller == "contact") echo 'class="active"'; ?>>
                            <a href="/contact"> Contact Us </a>
                        </li> 
                        
                        <?php 
                        
                        foreach($headerLinks as $headerLink)
                        echo $headerLink;
                        
                        if($enable_reg){
                        if(!isset($_SESSION['userToken'])) {    
                        ?>
                        <li>
                            <a href="#" data-target="#signin" data-toggle="modal"><?php echo $lang['263']; ?> </a>
                        </li>
                        <li>
                            <a href="#" data-target="#signup" data-toggle="modal"><?php echo $lang['264']; ?> </a>
                        </li>  
                        <?php } else { ?>
                        <li>
                            <a href="/?logout" title="<?php echo $lang['266']; ?>"><?php echo $lang['266']; ?> </a>
                        </li>  
                        <?php } } ?>
                        </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container -->
    </nav><!--/.navbar-->  
    <?php if($maintenanceRes[0]){ ?>
    <div class="alert alert-error text-center" style="margin: 35px 140px -10px 140px;">
    <strong>Alert!</strong> &nbsp; Your website is currently set to be closed.
    </div>
   <?php } ?>
