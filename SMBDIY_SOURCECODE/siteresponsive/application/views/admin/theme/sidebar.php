<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header"></li>    

       <li><a href="<?php echo site_url()."domain_details_visitor/domain_details"; ?>"><i class="fa fa-line-chart"></i> <span><?php echo $this->lang->line("visitor tracking"); ?></span></a></li>
       <li class="treeview">
        <a href="#">
          <i class="fa fa-cogs"></i> <span><?php echo $this->lang->line("Settings"); ?></span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
    
        <ul class="treeview-menu">
          <li><a href='<?php echo site_url()."admin_config/configuration";?>' > <i class="fa fa-cog"></i> <?php echo $this->lang->line("General Settings"); ?></a></li>  
          <li><a href='<?php echo site_url()."admin_config_connectivity/connectivity_config"; ?>'><i class="fa fa-plug"></i> <?php echo $this->lang->line("google API settings"); ?></a></li>    
          <li><a href='<?php echo site_url()."admin_config_email/email_smtp_settings"; ?>'><i class="fa fa-envelope"></i> <?php echo $this->lang->line("Email Settings"); ?></a></li>    
          <li><a href='<?php echo site_url()."admin_config_lead/lead_config"; ?>'><i class="fa fa-bullseye"></i> <?php echo $this->lang->line("lead settings"); ?></a></li>     
          <li><a href='<?php echo site_url()."admin_config_ad/ad_config"; ?>'><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line("advertisement settings"); ?></a></li>     
        </ul>
      </li> <!-- end settings -->
      
                
      <li> <a href="<?php echo site_url()."admin/delete_junk_file"; ?>"> <i class="fa fa-trash-o"></i> <span><?php echo $this->lang->line("delete junk files"); ?></span></a></li>
      <li> <a href="<?php echo site_url()."admin_leads/lead_list"; ?>"> <i class="fa fa-list"></i> <span><?php echo $this->lang->line("lead list"); ?></span></a></li>
      <li> <a href="<?php echo site_url()."admin/recent_check_report"; ?>"> <i class="fa fa-file-o"></i> <span><?php echo $this->lang->line("site health report"); ?></span></a></li>
      <li> <a href="<?php echo site_url()."admin/comparative_check_report"; ?>"> <i class="fa fa-files-o"></i> <span><?php echo $this->lang->line("comparitive health report"); ?></span></a></li>

       
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>