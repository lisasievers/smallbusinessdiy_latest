<!DOCTYPE html>
<html lang="<?php echo Yii::app() -> language ?>">
<head>
<meta charset="utf-8">
<title>Review Site</title>
<link rel="icon" href="https://storage.googleapis.com/assets-sitebuilder/images/favicon.ico" type="image/x-icon" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="php5developer.com">
<meta name="dc.language" content="<?php echo Yii::app() -> language ?>">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/app.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/AdminLTE.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/skins/_all-skins.css" rel="stylesheet">
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/custom.css" rel="stylesheet">

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if lte IE 8]>
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->getBaseUrl(true) ?>/js/excanvas.min.js"></script>
<![endif]-->
<?php Yii::app()->clientScript->registerCoreScript('jquery') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/bootstrap.min.js') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/base.js') ?>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/app.min.js') ?>
<?php $project_url=Yii::app() -> params['project_url']; ?>
</head>
  <body class="skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">
  <!-- Logo -->
  <a href="<?php echo $project_url; ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><i class="fa fa-line-chart fa-2x"></i></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg logo-inside"> <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY">
  </span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only"></span>
    </a>
<!-- notification -->

<div class="navbar-custom-menu">
  
   <div class="navbar-header-name">
                
                <div class="col-md-3">
                    <div class="proicon">
                    <img src="<?php echo Yii::app()->request->getBaseUrl(true); ?>/img/dude.png" alt="icon"/>
                    </div>
                  </div><div class="col-md-9">
                    <div class="proname">
                    <a><?php  if(isset(Yii::app()->request->cookies['name']->value)){ echo Yii::app()->request->cookies['name']->value;}else{echo "Guest";} ?></a></br>
                   <?php  if(isset(Yii::app()->request->cookies['name']->value)){ ?><a href="<?php echo $project_url; ?>/sitebuilder/public/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a><?php } ?>
                    </div></div>
                </div>
            </div>
  </nav>
</header>

      <!-- Left side column. contains the logo and sidebar -->
     <aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  
	
    <!-- sidebar menu: : style can be found in sidebar.less -->
	

    <ul class="sidebar-menu">
         

       <li><a href="<?php echo $project_url."/sitebuilder/public/userdashboard"; ?>"><i class="fa fa-line-chart"></i> <span>Main Dashboard</span></a></li>
       <li> <a href="<?php echo $project_url."/review"; ?>"> <i class="fa fa-list"></i> <span>Review Home</span></a></li>          
      
      
    
       
    </ul>


  </section>
  <!-- /.sidebar -->
</aside>

      <!-- Content Wrapper. Contains page content --> 
      <div class="content-wrapper">   
      <div class="container-fluid">     
			<?php echo $content ?> 
		</div>	
      </div><!-- /.content-wrapper -->

      <!-- footer was here -->

      <!-- Control Sidebar -->
      <?php //$this->load->view('theme/control_sidebar');?>
      <!-- /.control-sidebar -->

      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <!-- Footer -->

<footer class="main-footer clearfix">
	<div class="pull-left hidden-xs">
	<b>DIY Review</b>
	<a target="_BLANK" href="#"><b>Small Business DIY</b></a>
	</div>
</footer>
    <!-- Footer -->
  </body>
</html>
