<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  

    <!-- sidebar menu: : style can be found in sidebar.less -->
		<?php $project_url=$this->config->item('project_url'); ?>
    <ul class="sidebar-menu">
       <li><a href="<?php echo $project_url."/sitebuilder/public/userdashboard"; ?>"><i class="fa fa-line-chart"></i> <span>Main Dashboard</span></a></li>
       
      <!--<li> <a href="<?php //echo site_url()."dashboard/admin_dashboard"; ?>"> <i class="fa fa-dashboard"></i> <span>Siteanalysis Home</span></a></li>-->
    
      <?php if ($this->session->userdata('user_type') == 'Member') : ?>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-cogs"></i> <span><?php echo $this->lang->line("Settings"); ?></span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>    
        <ul class="treeview-menu">        
          <li><a href="<?php echo site_url()."config/index"; ?>"><i class="fa fa-connectdevelop"></i> <?php echo $this->lang->line("Connectivity Settings"); ?> </a></li>    
          <li><a href="<?php echo site_url()."config/proxy"; ?>"><i class="fa fa-user-secret"></i> <?php echo $this->lang->line("Proxy Settings"); ?> </a></li>    
        </ul>
      </li>

      <li><a href="<?php echo site_url()."payment/member_payment_history"; ?>"><i class="fa fa-paypal"></i> <span><?php echo $this->lang->line("Payment"); ?></span> </a></li>
      <?php endif; ?>
     
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(1,$this->module_access)): ?>
        <li><a href="<?php echo site_url()."domain_details_visitor/domain_list_visitor"; ?>"><i class="fa fa-line-chart"></i> <span><?php echo $this->lang->line("Visitor Analysis"); ?></span></a></li>
      <?php endif; ?>
      
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(2,$this->module_access)): ?>
        <li><a href="<?php echo site_url()."domain/domain_list_for_domain_details"; ?>"><i class="fa fa-bar-chart"></i> <span><?php echo $this->lang->line("Website Analysis"); ?></span></a></li> 
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(3,$this->module_access)): ?>
        <li> <a href="<?php echo site_url()."social/social_list"; ?>"> <i class="fa fa-share-alt"></i> <span><?php echo $this->lang->line("Social Network Analysis"); ?></span></a></li>
      <?php endif; ?>
      

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(4,$this->module_access)): ?>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-trophy"></i> <span><?php echo $this->lang->line("Rank & Index Analysis"); ?></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>    
          <ul class="treeview-menu">
            <li> <a href="<?php echo site_url()."rank/alexa_rank"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Alexa Rank"); ?></a></li>     
            <li> <a href="<?php echo site_url()."rank/alexa_rank_full"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Alexa Data"); ?></a></li>     
            <li> <a href="<?php echo site_url()."rank/similar_web"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("SimilarWeb Data"); ?></a></li>     
            <li> <a href="<?php echo site_url()."rank/moz_rank"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("MOZ Check"); ?></a></li>     
            <!-- <li> <a href="<?php echo site_url()."rank/google_page_rank"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Google Page Rank"); ?></a></li>      -->
            <li> <a href="<?php echo site_url()."search_engine_index/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Search Engine Index"); ?></a></li> 
          </ul>
        </li> 
      <?php endif; ?>
      
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(5,$this->module_access)): ?>
        <li class="treeview">
            <a href="#"><i class="fa fa-server"></i> <span><?php echo $this->lang->line("Domain Analysis"); ?></span>
            <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu"> 
              <li> <a href="<?php echo site_url()."who_is/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Whois Search"); ?></a></li>    
              <li> <a href="<?php echo site_url()."rank/dmoz_rank"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("DMOZ Check"); ?></a></li>    
              <li> <a href="<?php echo site_url()."expired_domain/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Auction Domain List"); ?></a></li>     
              <li> <a href="<?php echo site_url()."dns_info/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("DNS Information"); ?></a></li>                         
              <li> <a href="<?php echo site_url()."server_info/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Server Information"); ?></a></li> 
                     
            </ul>
        </li>
      <?php endif; ?>

    
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(6,$this->module_access)): ?>
        <li class="treeview">
            <a href="#"><i class="fa fa-map-marker"></i> <span><?php echo $this->lang->line("IP Analysis"); ?></span>
            <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li> <a href="<?php echo site_url()."ip/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("My IP Information"); ?></a></li>     
              <li> <a href="<?php echo site_url()."ip/domain_info"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Domain IP Information"); ?></a></li>     
              <li> <a href="<?php echo site_url()."ip/site_this_ip"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Sites In Same IP"); ?></a></li>     
              <li> <a href="<?php echo site_url()."ip/ipv6_check"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("ipv6 compability check"); ?></a></li>     
              <li> <a href="<?php echo site_url()."ip/ip_canonical_check"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("IP Canonical Check"); ?></a></li>           
            </ul>
        </li>
      <?php endif; ?>


      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(7,$this->module_access)): ?>
       <li class="treeview">
            <a href="#"><i class="fa fa-anchor"></i> <span><?php echo $this->lang->line("Link Analysis"); ?></span>
            <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li> <a href="<?php echo site_url()."link_analysis/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Link Analyzer"); ?></a></li>        
              <li> <a href="<?php echo site_url()."page_status/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Page Status Check"); ?></a></li>        
            </ul>
        </li>
      <?php endif; ?>    

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(8,$this->module_access)): ?>
        <li class="treeview">
            <a href="#"><i class="fa fa-tags"></i> <span><?php echo $this->lang->line("Keyword Analysis"); ?></span>
            <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li> <a href="<?php echo site_url()."keyword/keyword_analyzer"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Keyword Analyzer"); ?></a></li> 
              <li> <a href="<?php echo site_url()."keyword/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Keyword Position Analysis"); ?></a></li>     
              <li> <a href="<?php echo site_url()."keyword/google_correlated_keyword"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Correlated Keywords"); ?></a></li>     
              <li> <a href="<?php echo site_url()."keyword/keyword_suggestion"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Keyword Auto Suggestion"); ?></a></li>     
            </ul>
        </li>
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(16,$this->module_access)): ?>
        <li class="treeview">
            <a href="#"><i class="fa fa-eye"></i> <span><?php echo $this->lang->line("Keyword Position Tracking"); ?></span>
            <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li> <a href="<?php echo site_url()."keyword_position_tracking/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Keyword Tracking Settings"); ?></a></li>
              <li> <a href="<?php echo site_url()."keyword_position_tracking/keyword_position_report"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Keyword Position Report"); ?></a></li>   
            </ul>
        </li>
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(9,$this->module_access)): ?>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-link"></i> <span><?php echo $this->lang->line("Backlink & Ping"); ?></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>    
          <ul class="treeview-menu">
            <li> <a href="<?php echo site_url()."backlink/backlink_search"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Google Backlink Search"); ?></a></li>     
            <li> <a href="<?php echo site_url()."backlink/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Backlink Generator"); ?></a></li>
            <li> <a href="<?php echo site_url()."ping/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Website Ping"); ?></a></li>      
          </ul>
        </li>
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(10,$this->module_access)): ?>
        <li> <a href="<?php echo site_url()."antivirus/scan"; ?>"> <i class="fa fa-shield"></i> <span><?php echo $this->lang->line("Malware Scan"); ?></span></a></li>       
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(11,$this->module_access)): ?>
        <li> <a href="<?php echo site_url()."google_adwords/index"; ?>"> <i class="fa fa-google"></i> <span><?php echo $this->lang->line("Google Adwords Scraper"); ?></span></a></li>     
      <?php endif; ?>


      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(18,$this->module_access)): ?>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-cut"></i> <span><?php echo $this->lang->line("google url shortener"); ?></span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>    
        <ul class="treeview-menu">
          <li> <a href="<?php echo site_url()."url_shortener/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("url shortener"); ?></a></li>   
          <li><a href="<?php echo site_url()."url_shortener/url_analytics_page_loader"; ?>"><i class="fa fa-circle-o"></i> <?php echo $this->lang->line("analytics"); ?></a></li>   
        </ul>
      </li>
      <?php endif; ?>

              
      <?php if( $this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access) || in_array(12,$this->module_access)): ?>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-asterisk"></i> <span><?php echo $this->lang->line("Utilities"); ?></span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/email_encoder_decoder"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Email Encoder/Decoder"); ?></a></li> 
          <?php endif; ?>
          
          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/meta_tag_list"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Metatag Generator"); ?></a></li> 
          <?php endif; ?>

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(12,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/plagarism_check_list"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Plagiarism Check"); ?></a></li>
          <?php endif; ?>
          
          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/index"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Valid Email Check"); ?></a></li>     
          <?php endif; ?>

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/duplicate_email_filter_list"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Duplicate Email Filter"); ?></a></li>   
          <?php endif; ?>

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/url_encode_list"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("URL Encoder/Decoder"); ?></a></li>
          <?php endif; ?>

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/url_canonical_check"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("URL Canonical Check"); ?></a></li>
          <?php endif; ?> 

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/gzip_check"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Gzip Check"); ?></a></li>
          <?php endif; ?>           

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/base64_encode_list"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Base64 Encoder/Decoder"); ?></a></li>   
          <?php endif; ?>                  

          <?php if($this->session->userdata('user_type') == 'Admin' || in_array(13,$this->module_access)): ?>
            <li> <a href="<?php echo site_url()."tools/robot_code_generator"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("Robot Code Generator"); ?></a></li>   
          <?php endif; ?>
              
        </ul>
      </li> 
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(17,$this->module_access)): ?>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-object-group"></i> <span><?php echo $this->lang->line("code minifier"); ?></span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>    
        <ul class="treeview-menu">
          <li> <a href="<?php echo site_url()."code_minifier/html_minifier"; ?>"> <i class="fa fa-circle-o"></i> <?php echo $this->lang->line("html minifier"); ?></a></li>   
          <li><a href="<?php echo site_url()."code_minifier/css_minifier"; ?>"><i class="fa fa-circle-o"></i> <?php echo $this->lang->line("css minifier"); ?></a></li>    
          <li><a href="<?php echo site_url()."code_minifier/js_minifier"; ?>"><i class="fa fa-circle-o"></i> <?php echo $this->lang->line("js minifier"); ?></a></li>    
        </ul>
      </li>
      <?php endif; ?>
  
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>