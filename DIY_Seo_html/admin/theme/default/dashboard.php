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
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3><?php echo $seoCount; ?></h3>
                  <p>Active SEO Tools</p>
                </div>
                <div class="icon">
                  <i class="fa fa-cogs"></i>
                </div>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3><?php echo $today_page; ?><sup style="font-size: 20px"></sup></h3>
                  <p>Today PageViews</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3><?php echo $today_users_count; ?></h3>
                  <p>Today New Users</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                  <h3><?php echo $today_visit; ?></h3>
                  <p>Today Unique Visitors</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
              </div>
            </div><!-- ./col -->
          </div><!-- /.row -->
          
          <!-- Main row -->
          <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
          
          
            <!-- Custom tabs (Charts with tabs)-->
              <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-signal"></i>  PageViews History</li>
                </ul>
                <div class="tab-content no-padding">
                 <?php
                 if($ldate[0] == null || $ldate[0] == ""){
                    echo '<div class="text-center"><br><br><br><br><br><br>Not enough Data<br><br><br><br><br><br><br><br></div>';
                    }else{
                 ?>
                  <!-- Morris chart - Sales -->
                  <div class="chart tab-pane active" id="pageviews-chart" style="position: relative; height: 300px;"></div>
                <?php } ?>
                </div>
              </div><!-- /.nav-tabs-custom -->


                     <div class="box box-primary">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-primary btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button title="" data-toggle="tooltip" data-widget="remove" class="btn btn-primary btn-sm" data-original-title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                   <i class="fa fa-line-chart"></i>

                                    <h3 class="box-title">Recent Access History</h3>
                                </div><!-- /.box-header -->

                               
                                <div class="box-body">
                                    <table class="table table-hover table-bordered">
                                        <tbody><tr>
                                            <th>Tool Name</th>
                                            <th>Username</th>
                                            <th>User Country</th>
                                            <th>Time</th>
                                        </tr>
                                        <?php 
                                        foreach($userInputHistory as $userInputHis){
                                                echo "
                                                    <tr>
                                                    <td style='color: ". rndFlatColor() .";'>$userInputHis[0]</td>
                                                    <td>$userInputHis[1]</td>
                                                    <td>$userInputHis[2]</td>
                                                    <td>$userInputHis[3]</td>
                                                </tr> ";
                                        }
                                    ?>
                                    </tbody></table>
                                </div><!-- /.box-body -->
                       
                 
                                <div class="box-footer">
                         
                                </div><!-- /.box-footer -->
                            </div>
            <div class="box box-success">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-success btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button title="" data-toggle="tooltip" data-widget="remove" class="btn btn-success btn-sm" data-original-title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                   <i class="fa fa-th-list"></i>

                                    <h3 class="box-title">Admin History</h3>
                                </div><!-- /.box-header -->

                               
                                <div class="box-body">
                                    <table class="table table-hover table-bordered">
                                        <tbody><tr>
                                            <th>Last Login Date</th>
                                            <th>IP</th>
                                            <th>Country</th>
                                            <th>Browser</th>
                                        </tr>
                                        <?php 
                                        foreach($adminHistory as $adminHis){
                                                echo "
                                                    <tr>
                                                    <td>$adminHis[0]</td>
                                                    <td><span class='badge bg-".rndColor()."'>$adminHis[1]</span></td>
                                                    <td>$adminHis[2]</td>
                                                <td>$adminHis[3]</td>
                                                </tr> ";
                                        }
                                    ?>
                                    </tbody></table>
                                </div><!-- /.box-body -->
                       
                 
                                <div class="box-footer">
                         
                                </div><!-- /.box-footer -->
                            </div>
                            
          
          </section><!-- /.Left col -->
          
          <section class="col-lg-5 connectedSortable">

                                 <div id="server-box" class="box box-info">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-info btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button title="" data-toggle="tooltip" data-widget="remove" class="btn btn-info btn-sm" data-original-title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                   <i class="fa fa-server"></i>

                                    <h3 class="box-title">Server Information</h3>
                                </div><!-- /.box-header -->

                               
                                <div class="box-body">
                     <table class="table table-striped table-bordered">
  
                <tbody> 
                
                  <tr>
                  <td>Server IP</td>
                  <td><strong><?php echo $_SERVER['SERVER_ADDR']; ?></strong></td>
                  </tr>
                  
                  <tr>
                  <td>Server Disk Space</td>
                  <td><strong><?php echo roundsize($ds); ?></strong></td>
                  </tr> 
                  
                  <tr>
                  <td>Free Disk Space</td>
                  <td><strong><?php echo roundsize($df); ?></strong></td>
                  </tr>               
                  
                  <tr>
                  <td>Disk Space used by Script</td>
                  <td><strong><?php echo roundsize(GetDirectorySize(ROOT_DIR)); ?></strong></td>
                  </tr>
                  
                  <tr>
                  <td>Memory Used</td>
                  <td><strong><?php echo round(getServerMemoryUsage()); ?></strong></td>
                  </tr>               
                  
                  <tr>
                  <td>Current CPU Load</td>
                  <td><strong><?php echo getServerCpuUsage(); ?></strong></td>
                  </tr>               
                  
                  <tr>
                  <td>PHP Version</td>
                  <td><strong><?php echo phpversion(); ?></strong></td>
                  </tr>
                  
                  <tr>
                  <td>MySql Version</td>
                  <td><strong><?php echo mysqli_get_server_info($con); ?></strong></td>
                  </tr>
                  
                  <tr>
                  <td>Database Size</td>
                  <td><strong><?php echo $database_size; ?> MB</strong></td>
                  </tr>
                  
                </tbody>
              </table>
                                </div><!-- /.box-body -->
                       
                 
                                <div class="box-footer">
                         
                                </div><!-- /.box-footer -->
                            </div>
                            
                            <div class="box box-danger">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-danger btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button title="" data-toggle="tooltip" data-widget="remove" class="btn btn-danger btn-sm" data-original-title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                   <i class="fa fa-user"></i>

                                    <h3 class="box-title">Latest New Users</h3>
                                </div><!-- /.box-header -->

                               
                                <div class="box-body">
                                    <table class="table table-hover table-bordered">
                                        <tbody><tr>
                                            <th>Username</th>
                                            <th>Date</th>
                                            <th>Country</th>
                                        </tr>
                                        <?php 
                                        foreach($userList as $userdata){
                                                echo "
                                                    <tr>
                                                    <td>$userdata[0]</td>
                                                    <td>$userdata[1]</td>
                                                    <td>$userdata[2]</td>
                                                </tr> ";
                                        }
                                    ?>
                                    </tbody></table>
                                </div><!-- /.box-body -->
                       
                 
                                <div class="box-footer">
                         
                                </div><!-- /.box-footer -->
                            </div>
                            
                            
                            <div class="box box-warning">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button title="" data-toggle="tooltip" data-widget="collapse" class="btn btn-warning btn-sm" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button title="" data-toggle="tooltip" data-widget="remove" class="btn btn-warning btn-sm" data-original-title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                   <i class="fa fa-paper-plane"></i>

                                    <h3 class="box-title">Script Update</h3>
                                </div><!-- /.box-header -->

                               
                                <div class="box-body">
                                    <br />
                                    <table class="table table-hover table-bordered">
                                        <tbody>
                                        <tr>
                                            <td>Your Version</td>
                                            <td>v<?php echo VER_NO; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Latest Version</td>
                                            <td>v<?php echo $latestVersion; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Update</td>
                                            <?php if($updater){
                                            echo '<td><a href="http://codecanyon.net/downloads" target="_blank" class="btn btn-success">Update</a></td>'; 
                                            }else{
                                                echo '<td style="color: #c0392b;">Currently no update available!</td>';
                                            } ?>                                           
                                        </tr>
                                        
                                    </tbody></table>
                                    <br />
                                    
                                    <table class="table table-hover table-bordered">
                                        <tbody>
                                        <tr><th class="text-center">Latest News</th></tr>
                                        
                                        <tr>
                                            <td>- <?php echo $latestNews1; ?></td>
                                        </tr>
                                        <tr>
                                            <td>- <?php echo $latestNews2; ?></td>
                                        </tr>
                                        
                                    </tbody></table>
                                </div><!-- /.box-body -->
                       
                 
                                <div class="box-footer">
                         
                                </div><!-- /.box-footer -->
                            </div>

                            
          </section>
          
          </div><!-- /.Main row -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->