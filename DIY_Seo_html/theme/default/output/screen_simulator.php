<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>

  <script src='<?php echo $baseURL; ?>core/library/screen_simulator.js'></script>
    
 <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo $p_title; ?> 
            <small>Control panel</small>
          </h1>
          
        </section>

        <!-- Main content -->
        <section class="content">
	<div class="row">
      	
          	<div class="col-md-8 main-index">
            
            <div class="xd_top_box">
             <?php echo $ads_720x90; ?>
            </div>
            
              	<h2 id="title"><?php echo $data['tool_name']; ?></h2>

                <?php if ($pointOut != 'output') { ?>
               <br />
               <p><?php echo $lang['23']; ?>
               </p>
                    <form  onsubmit="doscreen_simulator(); return false">	
							<p>
								<input class="form-control" type="text" name="url" id="url" value=""/>
								
                          <br />  <br />  <?php echo $lang['33']; ?>:
								<div class="radio-box">
                                            <div class="form-group">
											<input type="radio" id="160x160"  name="resolution" value="160x160" />
											<label for="160x160">&nbsp;160x160 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div></div>
											
											<input type="radio" id="320x320"  name="resolution" value="320x320"/>
											<label for="320x320">&nbsp;320x320 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div>
											
                                            <input type="radio" id="640x480"  name="resolution" value="640x480"/>
											<label for="640x480">&nbsp;640x480 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div>
											
                                            <input type="radio" id="800x600"  name="resolution" value="800x600"/>
											<label for="800x600">&nbsp;800x600 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div>
											
                                            <input type="radio" id="1024x768"  name="resolution" value="1024x768" checked="checked" />
											<label for="1024x768">&nbsp;1024x768 <?php echo $lang['34']; ?></label>
                                            <div class="clear">&nbsp;</div>
                                            
                                            <input type="radio" id="1366x768"  name="resolution" value="1366x768" />
											<label for="1366x768">&nbsp;1366x768 <?php echo $lang['34']; ?></label>
                                            <div class="clear">&nbsp;</div>
											
                                            <input type="radio" id="1152x864"  name="resolution" value="1152x864"/>
											<label for="1152x864">&nbsp;1152x864 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div>
											
                                            <input type="radio" id="1600x1200"  name="resolution" value="1600x1200"/>
											<label for="1600x1200">&nbsp;1600x1200 <?php echo $lang['34']; ?></label>
											<div class="clear">&nbsp;</div>
											
								
								</div>
								
							</p>
								<div>
                                    <button class="btn btn-sm btn-info"><?php echo $lang['35']; ?></button>
								</div>
					</form>    
                          
               <?php 
               } ?> 

<br />

<div class="xd_top_box">
<?php echo $ads_720x90; ?>
</div>

<h2 id="sec1" class="about_tool"><?php echo $lang['11'].' '.$data['tool_name']; ?></h2>
<p>
<?php echo $data['about_tool']; ?>
</p> <br />
</div>              
            
<?php
// Sidebar
require_once(THEME_DIR."sidebar.php");
?>     		
                        </div><!--row -->
      </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

   <br />