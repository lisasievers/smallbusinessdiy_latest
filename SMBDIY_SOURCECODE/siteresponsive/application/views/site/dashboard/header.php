<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo base_url(); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><img style="margin:8px 0 0 8px"  src="<?php echo base_url();?>assets/images/favicon.png" alt="<?php echo $this->config->item('product_name');?>" class="img-responsive"></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg logo-inside"> <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY">
  </span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <?php $this->load->view("site/dashboard/notification"); ?>
  </nav>
</header>