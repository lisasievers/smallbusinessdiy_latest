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
    <title><?php echo $p_title; ?> | AtoZ SEO Tools</title>
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
    <link href="<?php echo $theme_path; ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo $theme_path; ?>dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
   
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo $theme_path; ?>plugins/select2/select2.min.css" />
    
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

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/admin/" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>S</b>EO</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>AtoZ SEO</b>Tools</span>
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
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/admin/?route=admin-accs" >
                  <!-- The user image in the navbar-->
                  <img src="<?php echo $admin_logo_path; ?>" class="user-image" alt="User Image"/>
                  <!-- hidden-xs hides the username on small devices so only the image appears. -->
                  <span class="hidden-xs"><?php echo $adminName; ?></span>
                </a>

              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="/" title="View Site" target="_blank"><i class="glyphicon glyphicon-globe"></i></a>
              </li>
              <li>
                <a href="/admin/?logout" title="Logout"><i class="glyphicon glyphicon-off"></i></a>
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
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo $admin_logo_path; ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>Welcome Back </p>
              <!-- Status -->
              <p style="font-size:15px;"><a href="#"><?php echo $adminName; ?>!</a> </p>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="<?php echo $dasSelected; ?>"><a href="/admin/"><i class='fa fa-dashboard'></i> <span> Dashboard</span></a></li>
            <li class="treeview <?php echo $manageSiteSelected; ?>">
              <a href="#"><i class='fa fa-bar-chart'></i> <span> Manage Site</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="/admin/?route=manage-site"><i class="fa fa-circle-o"></i>Site Details</a></li>
                <li><a href="/admin/?route=site-logo"><i class="fa fa-circle-o"></i>Site Logos</a></li>
                <li><a href="/admin/?route=ban-user-ip"><i class="fa fa-circle-o"></i>Ban User IP</a></li>
                <li><a href="/admin/?route=image-verification"><i class="fa fa-circle-o"></i>Captcha Protection</a></li>
                <li><a href="/admin/?route=mail-settings"><i class="fa fa-circle-o"></i>Mail Settings</a></li>
                <li><a href="/admin/?route=maintenance"><i class="fa fa-circle-o"></i>Maintenance</a></li>
              </ul>
            </li>
            <li class="<?php echo $reportSelected; ?>"><a href="/admin/?route=reports"><i class='fa fa-list-alt'></i> <span> Reports</span></a></li>
            <li class="treeview <?php echo $seotoolstSelected; ?>">
              <a href="#"><i class='fa fa-cogs'></i> <span> SEO Tools</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="/admin/?route=manage-tools"><i class="fa fa-circle-o"></i>Manage Tools</a></li>
                <li><a href="/admin/?route=shop-addons"><i class="fa fa-circle-o"></i>Shop Addons</a></li>
                <li><a href="/admin/?route=manage-addons"><i class="fa fa-circle-o"></i>Install Addons</a></li>
                <li><a href="/admin/?route=additional-option"><i class="fa fa-circle-o"></i>Additional Options</a></li>
              </ul>
            </li>
            <?php if(file_exists(ADMIN_CON_DIR."premium-dashboard.php")){ ?>
            <li class="treeview <?php echo $premiumSelected; ?>">
              <a href="#"><i class='fa fa-dollar'></i> <span> Premium Membership </span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="/admin/?route=premium-dashboard"><i class="fa fa-circle-o"></i>Dashboard</a></li>
                <li><a href="/admin/?route=premium-clients"><i class="fa fa-circle-o"></i>Customers</a></li>
                <li><a href="/admin/?route=premium-plans"><i class="fa fa-circle-o"></i>Plans</a></li>
                <li><a href="/admin/?route=orders"><i class="fa fa-circle-o"></i>Orders</a></li>
                <li><a href="/admin/?route=invoice"><i class="fa fa-circle-o"></i>Invoice</a></li>
                <li><a href="/admin/?route=tax"><i class="fa fa-circle-o"></i>Tax</a></li>
                <li><a href="/admin/?route=currency"><i class="fa fa-circle-o"></i>Currency Settings</a></li>
                <li><a href="/admin/?route=payment-gateway"><i class="fa fa-circle-o"></i>Payment Gateways</a></li>
                <li><a href="/admin/?route=website-reviewer"><i class="fa fa-circle-o"></i>Website Reviewer</a></li>
                <li><a href="/admin/?route=templates"><i class="fa fa-circle-o"></i>Mail Templates</a></li>
              </ul>
            </li>
            <?php } ?>
            <li class="<?php echo $adsSelected; ?>"><a href="/admin/?route=site-ads"><i class='fa fa-money'></i> <span> Advertisements</span></a></li>
            <li class="treeview <?php echo $userSelected; ?>">
              <a href="#"><i class='fa fa-group'></i> <span> Users</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="/admin/?route=manage-users"><i class="fa fa-circle-o"></i>Manage Users</a></li>
                <li><a href="/admin/?route=user-settings"><i class="fa fa-circle-o"></i>Settings</a></li>
              </ul>
            </li>
            <li class="<?php echo $adminSelected; ?>"><a href="/admin/?route=admin-accs"><i class='fa fa-server'></i> <span> Admin Account</span></a></li>
            <li class="<?php echo $interfaceSelected; ?>"><a href="/admin/?route=interface"><i class='fa fa-desktop'></i> <span> Interface</span></a></li>
            <li class="<?php echo $pagesSelected; ?>"><a href="/admin/?route=manage-pages"><i class='fa fa-book'></i> <span> Pages</span></a></li>
            <?php if(file_exists(CON_DIR."blog.php")){ ?>
            <li class="treeview <?php echo $blogSelected; ?>">
              <a href="#"><i class='fa fa-edit'></i> <span> Blog </span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="/admin/?route=new-post"><i class="fa fa-circle-o"></i>New Post</a></li>
                <li><a href="/admin/?route=manage-blog"><i class="fa fa-circle-o"></i>Manage Posts</a></li>
                <li><a href="/admin/?route=blog-settings"><i class="fa fa-circle-o"></i>Blog Settings</a></li>
              </ul>
            </li>
            <?php } ?>
            <li class="<?php echo $sitemapSelected; ?>"><a href="/admin/?route=sitemap"><i class='fa fa-sitemap'></i> <span> Sitemap</span></a></li>
            <li class="<?php echo $miscellaneousSelected; ?>"><a href="/admin/?route=miscellaneous"><i class='fa fa-bolt'></i> <span> Miscellaneous</span></a></li>
            
          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>