<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  
	<?php $project_url=$this->config->item('project_url'); ?>
    <!-- sidebar menu: : style can be found in sidebar.less -->

    <ul class="sidebar-menu">

       <li><a href="<?php echo $project_url."/sitebuilder/public/userdashboard"; ?>"><i class="fa fa-line-chart"></i> <span> Main Dashboard</span></a></li>
       <li> <a href="<?php echo $project_url."/siteresponsive"; ?>"> <i class="fa fa-list"></i> <span>This Home</span></a></li>          
      <li> <a href="<?php echo site_url()."/admin/recent_check_report"; ?>"> <i class="fa fa-archive" aria-hidden="true"></i> <span><?php echo $this->lang->line("site health report"); ?></span></a></li>
      <li> <a href="<?php echo site_url()."/admin/comparative_check_report"; ?>"> <i class="fa fa-archive" aria-hidden="true"></i> <span><?php echo $this->lang->line("comparitive health report"); ?></span></a></li>
   
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>