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
    	$('#userQueryTable').dataTable( {
    		"processing": true,
            "aaSorting": [[0, 'desc']],
    		"serverSide": true,
    		"ajax": "/admin/?route=ajax&userQuery"
    	} );
    	$('#userHisTable').dataTable( {
    		"processing": true,
            "aaSorting": [[0, 'desc']],
    		"serverSide": true,
    		"ajax": "/admin/?route=ajax&userHis"
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
            <li><a href="/admin/?route=reports"><i class="fa fa-list-alt"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
        
        
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
                <li><a href="#user-query" data-toggle="tab">User Query</a></li>
                <li><a href="#recent-access" data-toggle="tab">Recent Access History</a></li>
                </ul>
                <div class="tab-content">
                
                    <div class="tab-pane active" id="overview" >
                <div class="box-header with-border">
                  <h3 class="box-title">Overall status</h3>
                </div><!-- /.box-header -->

                <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                

                                    <table class="table table-bordered">
                                        <tbody>
                                        
                                        <tr>
                                            <th>#</th>
                                            <th>Task</th>
                                            <th>Stats</th>
                                        </tr>

                                        <tr>
                                            <td>1</td>
                                            <td>Total Users</td>
                                            <td><span class="label label-primary"><?php echo $total_users; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Total Banned Users</td>
                                            <td><span class="label label-success"><?php echo $total_ban; ?></span></td>
                                        </tr>  
                                        <tr>
                                            <td>3</td>
                                            <td>Total Banned IPs</td>
                                            <td><span class="label label-warning"><?php echo $total_ban_ip; ?></span></td>
                                        </tr>  
                                        <tr>
                                            <td>4</td>
                                            <td>Not verified users</td>
                                            <td><span class="label label-danger"><?php echo $not_ver; ?></span></td>
                                        </tr>  
                                        <tr>
                                            <td>5</td>
                                            <td>Total Page Views</td>
                                            <td><span class="label label-success"><?php echo $total_page; ?></span></td>
                                        </tr>  
                                            <tr>
                                            <td>6</td>
                                            <td>Total Unique Visitors</td>
                                            <td><span class="label label-info"><?php echo $total_un; ?></span></td>
                                        </tr>  
                                          
                                    </tbody></table>
                                    
                                    <br />

                <div class="box-header with-border">
                  <h3 class="box-title">Past 10 days Pageviews status</h3>
                </div><!-- /.box-header -->
                <br />        
            <table class="table table-bordered">
                                        <tbody><tr>
                                            <th style="width: 10px">#</th>
                                            <th>Date</th>
                                            <th>Unique Visitors</th>
                                            <th style="width: 130px">PageViews</th>
                                        </tr>
                                <?php
                                
                                foreach($pageArr as $pageArrData){
                                    echo '<tr>
                                    <td>'.$pageArrData[0].'</td>
                                    <td>'.$pageArrData[1].'</td>
                                    <td>'.$pageArrData[2].'</td>
                                    <td>'.$pageArrData[3].'</td>
                                    </tr>
                                    ';
                                }
                                ?>
                                    </tbody></table>


                

                
                </div><!-- /.box-body -->
                    
                    </div>
                    
                    <div class="tab-pane" id="user-query" >
                                  <br />        
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="userQueryTable">
                	<thead>
                		<tr>
                              <th style="width: 150px;">Tool Name</th>
                              <th style="width: 450px;">User Query</th>
                              <th>Username</th>
                              <th>User IP</th>
                              <th>Date</th>
                		</tr>
                	</thead>         
                    <tbody>                        
                    </tbody>
                </table>
                    </div>
                    
                    <div class="tab-pane" id="recent-access" >
                                   <br />                  
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="userHisTable">
                	<thead>
                		<tr>
                              <th>Tool Name</th>
                              <th>Username</th>
                              <th>User IP</th>
                              <th>User Country</th>
                              <th>Date</th>
                		</tr>
                	</thead>         
                    <tbody>                        
                    </tbody>
                </table>
                    
                    </div>
            
                </div>
            </div>    

      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
