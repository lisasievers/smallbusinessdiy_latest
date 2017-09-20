<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo $p_title; ?>  
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/admin/?route=user-settings"><i class="fa fa-group"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Users Settings</h3>
                </div><!-- /.box-header -->
                <form action="#" method="POST">
                <div class="box-body">
                <?php
                if(isset($msg)){
                    echo $msg;
                }?>

                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-8">
                                        <br />
             							<div class="form-group">
								            <div class="checkbox">
									           <label class="checkbox inline">
										          <input <?php if ($enable_reg) echo 'checked="true"'; ?>
										          type="checkbox" name="enable_reg"  /> Enable user registration and login system.
					                           </label>
								            </div>
						  	           </div>
                                       
             							<div class="form-group">
								            <div class="checkbox">
									           <label class="checkbox inline">
										          <input <?php if ($enable_oauth) echo 'checked="true"'; ?>
										          type="checkbox" name="enable_oauth"  /> Enable facebook and google oauth authentication system.
					                           </label>
								            </div>
						  	           </div>
                                       
                                        <div class="form-group">
                                            <label for="visitors_limit">Visitors Limit:</label>
                                            <input type="text" placeholder="Enter your visitors limit" name="visitors_limit" id="visitors_limit" value="<?php echo $visitors_limit; ?>" class="form-control">
                                        </div>
                                       
                                       <div class="callout callout-warning">
						                  <p>Note: "0" refers no limit! This feature, allow the guest users to access tools only for x 
                                          number of times. After that they forced to login. </p>
                                       </div>
                                        
                                       <br />
                                       <div class="box-header with-border">
                                        <h3 class="box-title">Oauth Settings - Google</h3>
                                       </div><!-- /.box-header -->
                                        <br />
                
                                        <div class="form-group">
                                            <label for="g_client_id">Client ID</label>
                                            <input type="text" placeholder="Enter your google api application id" id="g_client_id" name="g_client_id" value="<?php echo $g_client_id; ?>" class="form-control">
                                        </div>
                                       <div class="form-group">
                                            <label for="g_client_secret">Client Secret Code</label>
                                            <input type="text" placeholder="Enter your google api application secret code" id="g_client_secret" name="g_client_secret" value="<?php echo $g_client_secret; ?>" class="form-control">
                                        </div>
                                           <div class="form-group">
                                            <label for="g_redirect_uri">Redirect Uri</label>
                                            <input type="text" placeholder="Enter Redirect Uri" id="g_redirect_uri" name="g_redirect_uri" value="<?php echo $g_redirect_uri; ?>" class="form-control">
                                        </div>
                                        
                                       <br />
                                       <div class="box-header with-border">
                                        <h3 class="box-title">Oauth Settings - Facebook</h3>
                                       </div><!-- /.box-header -->
                                        <br />
                                        
                                        <div class="form-group">
                                            <label for="fb_app_id">Application ID:</label>
                                            <input type="text" placeholder="Enter your facebook application id" id="fb_app_id" name="fb_app_id" value="<?php echo $fb_app_id; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="fb_app_secret">Application Secret Code:</label>
                                            <input type="text" placeholder="Enter your facebook application secret code" id="fb_app_secret" name="fb_app_secret" value="<?php echo $fb_app_secret; ?>" class="form-control">
                                        </div>
           </div>
                    </div>
                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br /> <br />
                
                </div><!-- /.box-body -->
                </form>
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
