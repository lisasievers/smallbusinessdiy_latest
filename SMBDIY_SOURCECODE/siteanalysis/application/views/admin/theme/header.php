<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo $this->config->item('project_url'); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><i class="fa fa-line-chart fa-2x"></i></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg logo-inside"> <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" alt="SMBDIY">
  </b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <?php $this->load->view("admin/theme/notification"); ?>
  </nav>
</header>