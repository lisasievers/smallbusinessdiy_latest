<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  
	<?php $project_url=$this->config->item('project_url'); ?>
    <!-- sidebar menu: : style can be found in sidebar.less -->
	

    <ul class="sidebar-menu">
         

       <li><a href="<?php echo $project_url."sitebuilder/public/userdashboard"; ?>"><i class="fa fa-line-chart"></i> <span>Dashboard</span></a></li>
       <li> <a href="<?php echo $project_url."sitebuilder/public/userdashboard"; ?>"> <i class="fa fa-files-o"></i> <span>Website</span></a></li>          
      <li> <a href="<?php echo $project_url."sitebuilder/public/user/reports"; ?>"> <i class="fa fa-trash-o"></i> <span>Reports Dashboard</span></a></li>
      <li> <a href="<?php echo $project_url."sitebuilder/public/user/addwebsite"; ?>"> <i class="fa fa-list"></i> <span>Add Website for Reports</span></a></li>
     
	   <li class="treeview">
        <a href="#">
          <i class="fa fa-cogs"></i> <span>Report Tools</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
    
        <ul class="treeview-menu">
          <li><a href='<?php echo $project_url."sitebuilder/public/freereports"; ?>' > <i class="fa fa-cog"></i> Free</a></li>  
          <li><a href='<?php echo $project_url."sitebuilder/public/paidreports"; ?>'><i class="fa fa-plug"></i> Paid</a></li>    
          </ul>
      </li> <!-- end settings -->
      
       <li> <a href="<?php echo $project_url."sitebuilder/public/user/settings"; ?>"> <i class="fa fa-file-o"></i> <span>Settings</span></a></li>
      <li> <a href="<?php echo site_url()."admin/recent_check_report"; ?>"> <i class="fa fa-file-o"></i> <span><?php echo $this->lang->line("site health report"); ?></span></a></li>
      <li> <a href="<?php echo site_url()."admin/comparative_check_report"; ?>"> <i class="fa fa-files-o"></i> <span><?php echo $this->lang->line("comparitive health report"); ?></span></a></li>


       
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>