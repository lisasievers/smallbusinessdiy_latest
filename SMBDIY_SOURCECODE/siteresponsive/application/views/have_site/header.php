<!DOCTYPE html>
<html>
<head>
	<title>Small Business DIY</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.ico">
<!--
	<link href="{{ URL::to('src/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/flat-ui-pro.css') }}" rel="stylesheet">
	
	<link href="{{ URL::to('src/css/login.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/font-awesome.css') }}" rel="stylesheet"> -->
<!--
	<link href="{{ URL::to('src/css/builder.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/spectrum.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/chosen.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/summernote.css') }}" rel="stylesheet">
    <link href="{{ URL::to('src/css/page_pricing.css') }}" rel="stylesheet"> -->
	<?php echo $this->load->view("site/css_include_site.php"); ?>
	<?php echo $this->load->view("site/js_include_site.php"); ?>
	<!--<link href="<?php //echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php //echo base_url();?>assets/css/flat-ui-pro.css" rel="stylesheet"> 
	<link href="<?php //echo base_url();?>assets/css/font-awesome.css" rel="stylesheet">
	-->
	<link href="<?php echo base_url();?>assets/css/login.css" rel="stylesheet">
	
	<!--<link rel="stylesheet" href="{{ URL::to('src/css/build-main.min.css') }}">-->
    <!--<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" type="text/css">-->
    <style>


    </style>
</head>
<body class="login">
<div id="app" class="havesite">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="<?php echo $this->config->item('project_url'); ?>">
                        
                        <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY"></a>
                        
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    
<?php //echo Session::get('name');?>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                      <?php foreach($topmenu as $key=>$menu) { ?>
                      		<li><a href="<?php echo $menu ?>"><?php echo $key ?></a></li>
                      		<?php } ?>
                            
                           <!-- <li class="dropdown">
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
                            </li> -->
                      
                    </ul>
                </div>
            </div>
        </nav>