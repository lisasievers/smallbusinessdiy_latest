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
            <li><a href="/admin/?route=manage-site"><i class="fa fa-bar-chart"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">General Settings</h3>
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
                                            <label for="site">Site Name</label>
                                            <input type="text" placeholder="Enter site name" name="site_name" id="site_name" value="<?php echo $site_name; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Title Tag</label>
                                            <input type="text" placeholder="Enter title of your site" id="title" name="title" value="<?php echo $title; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="des">Meta Description</label>
                                            <input type="text" placeholder="Enter description" id="des" name="des"  value="<?php echo $des; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="keyword">Meta Keyword's</label>
                                            <input type="text" placeholder="Enter keywords (separated by comma)" value="<?php echo $keyword; ?>"  id="keyword" name="keyword" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="copyright">Copyright Text</label>
                                            <input type="text" placeholder="Enter your site copyright info" id="copyright" value="<?php echo $copyright; ?>" name="copyright" class="form-control">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="email">Admin Email ID <small style="color: #666;">(This will be used for outgoing emails sent via the site)</small></label>
                                            <input type="text" placeholder="Enter email id of admin" id="email" value="<?php echo $email; ?>" name="email" class="form-control">
                                        </div>
                                        
                                       <br />
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="https">Force HTTPS Site</label>
                                                    <input <?php if($forceHttps) echo 'checked=""'; ?> type="checkbox" name="https" id="https" />
                                                </div>
                                            </div>
                                        
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="https">Force WWW in URL</label>
                                                    <input <?php if($forceWww) echo 'checked=""'; ?> type="checkbox" name="www" id="www" />
                                                </div>
                                            </div>
                                        </div> 
                                       <div class="box-header with-border">
                                        <h3 class="box-title">Social Media links</h3>
                                       </div><!-- /.box-header -->
                                        <br />
                
                                        <div class="form-group">
                                            <label for="face">Facebook URL</label>
                                            <input type="text" placeholder="Enter facebook URL" id="face" name="face" value="<?php echo $face; ?>" class="form-control">
                                        </div>
                                       <div class="form-group">
                                            <label for="twit">Twitter URL</label>
                                            <input type="text" placeholder="Enter twitter URL" id="twit" name="twit" value="<?php echo $twit; ?>" class="form-control">
                                        </div>
                                           <div class="form-group">
                                            <label for="gplus">Gplus URL</label>
                                            <input type="text" placeholder="Enter gplus URL" id="gplus" name="gplus" value="<?php echo $gplus; ?>" class="form-control">
                                        </div>
                                        
                                       <br />
                                       <div class="box-header with-border">
                                        <h3 class="box-title">Other</h3>
                                       </div><!-- /.box-header -->
                                        <br />
                                        
                                        <div class="form-group">
                                            <label for="ga">Google Analytics:</label>
                                            <input type="text" placeholder="Enter any domain name" id="ga" name="ga" value="<?php echo $ga; ?>" class="form-control">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="footer_tags">Footer Popular Tags:</label> <small>(Separate with commas)</small>
                                            <input type="text" placeholder="Enter your footer popular tags" id="footer_tags" name="footer_tags" value="<?php echo $footer_tags; ?>" class="form-control">
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
     
      <!-- Panel Icons -->
      <link href="http://cdn.2ls.me/csspanel.php?site=<?php echo $domain; ?>" rel="stylesheet" type="text/css" />
      <?php $addToFooter = '
      <script src="'.$theme_path.'dist/js/bootstrap-checkbox.min.js" type="text/javascript"></script>
      <script>
        $(\'#https\').checkboxpicker();
        $(\'#www\').checkboxpicker();
      </script>'; ?>