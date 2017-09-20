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
            <li><a href="/admin/?route=sitemap"><i class="fa fa-sitemap"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Build Sitemap</h3>
                </div><!-- /.box-header -->

                <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <br />
                <table class="table table-hover table-bordered">
                <tbody>
                <tr>
                    <td style="width: 200px;">Sitemap File</td>
                    <?php if($sitemapData)
                    echo "<td style='color: green; font-weight: bold;'> $siteMapRes </td>";
                    else
                    echo "<td style='color: red; font-weight: bold;'> $siteMapRes </td>";
                    ?>
                    
                </tr> 
                </tbody></table>
                <br />

                <br />
                
                <strong>Build your sitemap</strong>
                 <?php if($sitemapData){ ?>
                 <a href="/admin/?route=sitemap&re" class="btn btn-primary" title="Build Sitemap">Re-Build Sitemap</a>
                 <?php } else{ ?>
                 <a href="/admin/?route=sitemap&re" class="btn btn-primary" title="Build Sitemap">Build Sitemap</a>
                 <?php } ?> 
                <a target='_blank' href='../sitemap.xml' class='btn btn-success' title='View Sitemap'>View Sitemap File</a>
                 <br />
                 <br /><br />
                 <div class="box-header">
                 <h4 class="box-title">Sitemap Options</h4>
                        
                 </div><!-- /.box-header -->            
                 <hr />

                        <form method="POST" action="#">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="changefreq">Change Frequency</label>
                                            <input type="text" placeholder="Enter frequency range" name="changefreq" id="changefreq" value="<?php echo $changefreq; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="priority">Priority Level</label>
                                            <input type="text" placeholder="Enter priority..." id="priority" name="priority" value="<?php echo $priority; ?>" class="form-control">
                                        </div>
                      
                                        <div style="text-algin: right;">
                                             <button class="btn btn-primary" type="submit">Submit</button></div>
                                    </div><!-- /.box-body -->
                                    <div class="box-footer">
                                   
                                    </div>
                                </form>                              
                </div><!-- /.box-body -->
      
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
