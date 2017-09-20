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
            <li><a href="/admin/?route=edit-tools&id=<?php echo $dID; ?>&edit"><i class="fa fa-bar-chart"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                              <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
              <div class="row">
              <div class="col-md-6">
              <div class="box-header with-border">
                  <h3 class="box-title">Site Logo: <small> - Best Image Size: 150x60</small></h3>
                </div><!-- /.box-header -->
                                <form id="theme_id" method="POST" action="/admin/?route=site-logo" enctype="multipart/form-data">
                                       <br />
                                        <div class="form-group">											
											<label for="logoID">Select image to upload:</label>
											<div class="controls">			   
                                            <img src="../<?php echo $logo_path; ?>" style="text-align:center;"/> <br />
                                         <input type="file" name="logoUpload" id="logoUpload" class="btn btn-default" />
                                         <input type="hidden" name="logoID" id="logoID" value="1" /> <br />
                                         <input type="submit" value="Upload Image" name="submit" class="btn btn-primary" />

		                                  </div> <!-- /controls -->	
					
                    					</div> <!-- /control-group -->
          </form>  
                
              </div>
              
              <div class="col-md-6">
              <div class="box-header with-border">
                  <h3 class="box-title">Favicon: </h3>
                </div><!-- /.box-header -->
                
                                                
          <form id="theme_id" method="POST" action="/admin/?route=site-logo" enctype="multipart/form-data">
					               <br />
                                        <div class="form-group">											
											<label class="control-label" for="favID">Select icon to upload:</label>
											<div class="controls">			   
                                            <img src="../<?php echo $fav_path; ?>" style="text-align:center;"/> <br />
                                         <input type="file" name="favUpload" id="favUpload" class="btn btn-default" />
                                         <input type="hidden" name="favID" id="favID" value="1" /> <br />
                                         <input type="submit" value="Upload" name="submit" class="btn btn-primary" />

		                                  </div> <!-- /controls -->	
										</div> <!-- /control-group -->
          </form>  
              </div>
              
              </div>

                </div><!-- /.box-body -->
      <br /><br />
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
