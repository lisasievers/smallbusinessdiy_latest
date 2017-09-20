<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php echo $this->config->item('product_name'); if(isset($page_title) && $page_title!="") echo " | ".$page_title;?></title>

	<!-- Mobile Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php $share_cover_image=base_url("assets/images/share_cover.png"); ?>
	<!--For Google -->
    <meta name="description" content="<?php echo $seo_meta_description; ?>" />
	<meta name="keywords" content="<?php echo $seo_meta_keyword; ?>" />
	<meta name="author" content="<?php echo $this->config->item('institute_address1');?>" />
	<meta name="copyright" content="<?php echo $this->config->item('product_short_name');?>" />
	<meta name="application-name" content="<?php echo $this->config->item('product_short_name');?>" />
	
	<!-- for Facebook -->          
	<meta property="og:title" content="<?php echo $this->config->item('product_short_name')." | ".$page_title;?>" />
	<meta property="og:type" content="article" />
	<meta property="og:image" content="<?php echo $share_cover_image; ?>" />
	<meta property="og:url" content="<?php echo current_url(); ?>" />
	<meta property="og:description" content="<?php echo $seo_meta_description; ?>" />
	<meta property="fb:app_id" content="" />
	
	<!-- for Twitter -->          
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?php echo $this->config->item('product_short_name')." | ".$page_title;?>" />
	<meta name="twitter:description" content="<?php echo $seo_meta_description; ?>" />
	<meta name="twitter:image" content="<?php echo $share_cover_image; ?>" />
	

	<?php echo $this->load->view("site/css_include_site.php"); ?>
	<?php echo $this->load->view("site/js_include_site.php"); ?>

</head>

<body class="login">
<div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <!--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>-->

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="<?php echo $this->config->item('project_url'); ?>">
                        
                        <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY"></a>
                        
                    </a>
                </div>

               <!-- <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                   <!-- <ul class="nav navbar-nav">
                      
                    <!-- Right Side Of Navbar -->
                    <!--<ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                      
                            
                        <!--    <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                   <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                       
                                    </li>
                                </ul>
                            </li>
                      
                    </ul>
                </div>-->
            </div>
        </nav>