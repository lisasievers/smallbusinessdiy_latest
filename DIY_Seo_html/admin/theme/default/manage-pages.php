<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    
    <script type="text/javascript" language="javascript" src="<?php echo $theme_path; ?>dist/js/jquery.dataTables.js"></script>
    
    <script type="text/javascript" language="javascript" class="init">
    
    $(document).ready(function() {
    	$('#mySitesTable').dataTable( {
    		"processing": true,
    		"serverSide": true,
    		"ajax": "/admin/?route=ajax&managePages"
    	} );
    } );
    
    </script>
    
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo $p_title; ?>  
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/admin/?route=manage-pages"><i class="fa fa-book"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
        
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <li class="<?php echo $page1; ?>"><a href="#managePages" data-toggle="tab">Manage Pages</a></li>
                  <li class="<?php echo $page2; ?>"><a href="#add-new" data-toggle="tab">Add New Page</a></li>
                </ul>
                <div class="tab-content">
                
                <div class="tab-pane <?php echo $page1; ?>" id="managePages" >
                <br />
                <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mySitesTable">
                	<thead>
                		<tr>
                              <th>Added Date</th>
                              <th>Page Name</th>
                              <th>Page Title</th>
                              <th>View</th>
                              <th>Edit</th>
                              <th>Delete</th>
                		</tr>
                	</thead>         
                    <tbody>                        
                    </tbody>
                </table>
                
                </div>
                
                <div class="tab-pane <?php echo $page2; ?>" id="add-new" >
                
                <form action="#" method="POST" onsubmit="return finalFixedLink();">
                <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                      <label for="page_title">Page Title</label>
                      <input type="text" placeholder="Enter your page title" value="<?php echo $page_title; ?>" name="page_title" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label for="page_url">Page URL</label><small id="linkBox"> (<?php echo 'http://' . $_SERVER['HTTP_HOST'] ."/page/"; ?>) </small>
                      <input type="text" id="pageUrlBox" placeholder="Enter your page url" value="<?php echo $page_url; ?>" name="page_url" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                      <label for="meta_des">Meta Description</label>
                      <textarea placeholder="Description must be within 150 Characters" rows="3" name="meta_des" class="form-control"><?php echo $meta_des; ?></textarea>
                   </div>
                   
                        	<div class="form-group">
								<div class="checkbox">
									<label class="checkbox inline">
										<input <?php if ($header_show) echo 'checked="true"'; ?>
										type="checkbox" name="header_show" /> <strong>Display the page link on header menu bar.</strong>
									</label>
								</div>
							</div>
                                        
                    </div><!-- /.col-md-6 -->
                    
                    <div class="col-md-6">
                    
                    <div class="form-group">
                      <label for="page_name">Page Name</label>
                      <input type="text" placeholder="Enter the page name" value="<?php echo $page_name; ?>" name="page_name" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                      <label for="posted_date">Posted Date</label>
                      <?php if($posted_date == '') { ?>
                      <input type="text" placeholder="Enter your posted date" id="postedDate" name="posted_date" class="form-control" />
                      <?php } else { ?>
                      <input type="text" placeholder="Enter your posted date" value="<?php echo $posted_date; ?>" id="postedDate" name="posted_date" class="form-control" />
                      <?php } ?>
                    </div>
                    
                    <div class="form-group">
                      <label for="meta_tags">Meta Keywords (Separate with commas)</label>
                      <textarea placeholder="keywords1, keywords2, keywords3" rows="3" name="meta_tags" class="form-control"><?php echo $meta_tags; ?></textarea>
                   </div>
                   
                        	<div class="form-group">
								<div class="checkbox">
									<label class="checkbox inline">
										<input <?php if ($footer_show) echo 'checked="true"'; ?>
										type="checkbox" name="footer_show" /> <strong>Display the page link on footer menu bar.</strong>
									</label>
								</div>
							</div>

                    </div>
                </div><!-- /.row -->
                
                <div class="row">
                 
                 <div class="form-group" style="margin: 12px;">
                      <label for="page_content">Page Content</label>
                      <textarea id="editor1" name="page_content" class="form-control"><?php echo $page_content; ?></textarea>
                   </div>
                 
                 </div>
                 <?php if(isset($_GET['edit'])){ ?>
                 <input type="hidden" name="editPage" value="1" />
                 <input type="hidden" name="editID" value="<?php echo $editID; ?>" />
                 <?php } else { ?>
                 <input type="hidden" name="newPage" value="1" />
                 <?php } ?>
                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br />
                
                </div><!-- /.box-body -->
            </form>
                </div>
            
                </div>
            </div>

      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
