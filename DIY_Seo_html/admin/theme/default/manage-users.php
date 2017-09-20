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
    		"ajax": "/admin/?route=ajax&manageUsers"
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
            <li><a href="/admin/?route=manage-users"><i class="fa fa-group"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">User List</h3>
                    <div style="position:absolute; top:4px; right:15px;">
                      <a href="/admin/?route=manage-users&export" class="btn btn-primary"><i class="fa fa-fw fa-share-square"></i> Export</a>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body">
                <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <br />
                
                <?php if(isset($_GET['details'])){ ?>
                <div class="widget widget-table action-table">
                    <div class="widget-header"> <i class="icon-th-list"></i>
                      <h3><?php echo $user_username.' Details'; ?></h3>
                    </div>
                    <!-- /widget-header -->
                    <div class="widget-content">
                      <table class="table table-striped table-bordered">
                        <tbody>
                          <tr>
                            <td>  Username </td>
                                        <td> <?php echo $user_username; ?> </td>   
                          </tr>
                                        <tr>
                            <td> Email ID </td>
                            <td> <?php echo $user_email_id; ?> </td>
                          </tr>
                          <tr>
                            <td> Platform </td>
                             <td> <?php echo $user_platform; ?> </td>
                          </tr>
                          
                          <tr>
                            <td> Oauth ID </td>
                            <td> <?php echo $user_oauth_uid; ?> </td>
                          </tr>
                                    
                                        <tr>
                            <td> Current Status </td>
                             <td> <?php echo $user_verified; ?> </td>
                          </tr>
                                        <tr>
                            <td> User IP </td>
                             <td> <?php echo $user_ip; ?> </td>
                          </tr>
                          <tr>
                            <td> Joined Date </td>
                             <td> <?php echo $user_date; ?> </td>
                          </tr>
                          <tr>
                            <td> Full Name </td>
                             <td> <?php echo $user_full_name; ?> </td>
                          </tr>
        
                        
                        </tbody>
                      </table>
                      <div>
                        <a href="../admin/?route=manage-users" class="btn btn-primary">Go Back</a>
                      </div>
                    </div>
                    <!-- /widget-content --> 
                  </div>    
 

                <?php } else { ?>
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mySitesTable">
                	<thead>
                		<tr>
                              <th>Username</th>
                              <th>Email ID</th>
                              <th>Joined Date</th>
                              <th>Platform</th>
                              <th>Oauth ID</th>
                              <th>Ban User</th>
                              <th>Complete Profile</th>
                              <th>Delete</th>
                		</tr>
                	</thead>         
                    <tbody>                        
                    </tbody>
                </table>
                <?php } ?>
                <br />
                
                </div><!-- /.box-body -->

              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
