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
            <li><a href="/admin/?route=site-ads"><i class="fa fa-money"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Site Ads Settings</h3>
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
                        <label for="site">Ad Spot - 1 (Size: 720x90)</label>
                        <textarea placeholder="Enter your Javascript / HTML Code" name="ad720x90" class="form-control"><?php echo $ad720x90; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site">Ad Spot - 2 (Size: 250x300)</label>
                        <textarea placeholder="Enter your Javascript / HTML Code" name="ad250x300" class="form-control"><?php echo $ad250x300; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site">Ad Spot - 3 (Size: 250x125)</label>
                        <textarea placeholder="Enter your Javascript / HTML Code" name="ad250x125" class="form-control"><?php echo $ad250x125; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site">Ad Spot - 4 (Size: 468x60)</label>
                        <textarea placeholder="Enter your Javascript / HTML Code" name="ad480x60" class="form-control"><?php echo $ad480x60; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site">Text Ad Spot - 5</label>
                        <textarea placeholder="Enter your Javascript / HTML Code" name="text_ads" class="form-control"><?php echo $text_ads; ?></textarea>
                    </div>
                    
                        </div>
                </div>

                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br />
                
                </div><!-- /.box-body -->
                </form>
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->