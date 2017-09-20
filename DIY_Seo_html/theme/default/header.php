<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2016 ProThemes.Biz
 *
 */
 
?>
<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $p_title; ?> | SEO Tools</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="<?php echo $theme_path; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Morris chart -->
    <link href="<?php echo $theme_path; ?>plugins/morris/morris.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="<?php echo $theme_path; ?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- daterange picker -->
    <link href="<?php echo $theme_path; ?>plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo $theme_path; ?>dist/css/AdminLTE.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo $theme_path; ?>dist/css/skins/_all-skins.css" rel="stylesheet" type="text/css" />
   
    
    <!-- jQuery 2.1.4 -->
    <script src="<?php echo $theme_path; ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
	
  <body class="skin-blue sidebar-mini">
    <div class="wrapper">
	<?php $project_url ="https://smallbusinessdiy.com"; ?>
      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/index.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b><i class="fa fa-line-chart fa-2x"></i></b></span>
    <!-- logo for regular state and mobile devices -->
        <span class="logo-lg logo-inside"> <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY">
 
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              <!-- User Account Menu -->
              
              <li>
                <a href="<?php echo $project_url."/sitebuilder/public/userdashboard"; ?>" title="Logout"><i class="glyphicon glyphicon-off"></i></a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
            <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar user panel (optional) -->
          <!--<div class="user-panel">
            <div class="pull-left image">
              <img src="<?php //echo $admin_logo_path; ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>Welcome Back </p>
             
              <p style="font-size:15px;"><a href="#"><?php //echo $adminName; ?>!</a> </p>
            </div>
          </div>-->
          
          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <li><a href="<?php echo $project_url."/sitebuilder/public/userdashboard"; ?>"><i class="fa fa-line-chart"></i> <span> Main Dashboard</span></a></li>
            <li> <a href="/index.php"> <i class="fa fa-list"></i> <span>This Home</span></a></li>          
      
                
          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>