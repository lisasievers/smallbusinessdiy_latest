<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
    <?php $this->load->view('include/css_include_back');?>
	  <?php $this->load->view('include/js_include_back');?>
   <link rel="shortcut icon" href="https://storage.googleapis.com/assets-sitebuilder/images/favicon.ico" type="image/icon" >
    <link rel="icon" href="https://storage.googleapis.com/assets-sitebuilder/images/favicon.ico" type="image/ico" >
  
  </head>
  <body class="skin-blue sidebar-mini">
    <div class="wrapper">

      <?php $this->load->view('admin/theme/header');?>

      <!-- for RTL support -->
      <?php 
      //if($this->config->item('language')=="arabic")  
      if($this->is_rtl) 
      { ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.2.0-rc2/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>css/rtl.css" rel="stylesheet" type="text/css" />       
      <?php
      }
      ?>

      <!-- Left side column. contains the logo and sidebar -->
      <?php $this->load->view('admin/theme/sidebar'); ?>

      <!-- Content Wrapper. Contains page content --> 
      <div class="content-wrapper">
      
      <?php
      if($this->uri->segment(2)=="login_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
      <div class="clearfix" style="padding:15px">
        <a class="btn btn-primary pull-right" href="https://www.youtube.com/watch?v=5cLYkoL1X5M&feature=youtu.be" target="_BLANK"><?php echo $this->lang->line("how to create facebook app?"); ?></a>
        <a class="btn btn-primary pull-left" href="https://youtu.be/1hj0WWEnftU" target="_BLANK"><?php echo $this->lang->line("how to create google auth?"); ?></a> <br/><br/>
        <div class="alert alert-info text-center">
          <b> <?php echo $this->lang->line("google auth redirect url")." : <i>". site_url("home/google_login_back"); ?> </i></b>
        </div>
      </div>
      <?php } ?>

  		<?php 
        if($crud==1) 
			$this->load->view('admin/theme/theme_crud',$output); 
        else 
			$this->load->view($body);
      ?>  
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
      <?php $this->load->view('admin/theme/footer');?>
    <!-- Footer -->
  </body>
</html>
