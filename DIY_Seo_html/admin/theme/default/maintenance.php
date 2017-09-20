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
                  <h3 class="box-title">Site Online / Offline</h3>
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
										          <input <?php if ($maintenance_mode) echo 'checked="true"'; ?>
										          type="checkbox" name="maintenance_mode"  /> Enable maintenance mode (Users can't able to access the site!).
					                           </label>
								            </div>
						  	           </div>
                                       
                                        <div class="form-group">
                                            <label for="maintenance_mes">Maintenance Reason</label>
                                            <textarea class="form-control" id="maintenance_mes" name="maintenance_mes" placeholder="Enter your reason"><?php echo $maintenance_mes; ?></textarea>
                                        </div>
                                       
                                       <div class="callout callout-info">
						                  <p>Note: Administrators still have access the full site!</p>
                                       </div>
                                        
                                       <br />

           </div>
                    </div>
                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br /> <br />
                
                </div><!-- /.box-body -->
                </form>
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
