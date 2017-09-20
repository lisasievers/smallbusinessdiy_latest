
<div class="navbar-custom-menu">
  <ul class="nav navbar-nav">
  
	<li>          
                 
          <p>
            <?php echo $this->session->userdata('username');?>           
          </p>
        </li>
  <li>
  <a href="<?php echo site_url('home/logout') ?>" class="btn btn-warning btn-flat"><?php echo $this->lang->line("logout"); ?></a>
  </li>
    <?php 
      $pro_pic=base_url().'assets/images/logo.png';
    ?>
    <!-- User Account: style can be found in dropdown.less -->
    
    <!-- Control Sidebar Toggle Button -->
    <!-- <li>
      <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
    </li> -->
  </ul>
</div>