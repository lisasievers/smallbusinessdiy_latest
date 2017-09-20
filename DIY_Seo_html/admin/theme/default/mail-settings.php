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
            <li><a href="/admin/?route=mail-settings"><i class="fa fa-bar-chart"></i> Admin</a></li>
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
                    <div class="form-group">
                    <label>Select your Mail Protocol: </label>
                    <select name="protocol" class="form-control"> 
                                           <?php if ($protocol == '1')
                                           {
                                            echo '<option selected value="1">PHP Mail</option>';
                                            echo '<option value="2">SMTP</option>';
                                           }
                                           else
                                           {
                                           echo '<option value="1">PHP Mail</option>';
                                            echo '<option selected value="2">SMTP</option>';
                                           } ?>

                                           </select>
                                           
                      </div>   
                                                              
                                       <br />
                                       <div class="box-header with-border">
                                        <h3 class="box-title">SMTP Information </h3>
                                       </div><!-- /.box-header -->
                                        <br />
                                           
                                        <div class="form-group">
                                            <label for="smtp_host">SMTP Host</label>
                                            <input type="text" placeholder="Enter smtp host" name="smtp_host" value="<?php echo $smtp_host; ?>" class="form-control">
                                        </div>
				                   <div class="form-group">											
											<label for="smtp_auth">SMTP Auth</label>
								    <select name="auth" class="form-control">  
                                    
                                            <?php if ($auth == 'true')
                                           {
                                            echo '<option selected value="true">True</option>
                                           <option value="false">False</option>';
                                           }
                                           else
                                           {
                                            echo '<option value="true">True</option>
                                           <option selected value="false">False</option>';
                                           } ?>
                                           
                                           </select>				
										</div> <!-- /form-group -->
                                        
                                       <div class="form-group">
                                            <label for="smtp_port">SMTP Port</label>
                                            <input type="text" placeholder="Enter smtp port" name="smtp_port" value="<?php echo $smtp_port; ?>" class="form-control">
                                        </div>
                                           <div class="form-group">
                                            <label for="smtp_user">SMTP Username</label>
                                            <input type="text" placeholder="Enter smtp username" name="smtp_user" value="<?php echo $smtp_username; ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="smtp_pass">SMTP Password</label>
                                            <input type="password" placeholder="Enter smtp password" name="smtp_pass" value="<?php echo $smtp_password; ?>" class="form-control">
                                        </div>       
                                        
                                        <div class="form-group">											
											<label for="smtp_socket">SMTP Secure Socket</label>
								    <select name="socket" class="form-control"> 
                                         <?php if ($socket == 'tls')
                                           {
                                          echo '   
                                           <option selected value="tls">TLS</option>
                                           <option value="ssl">SSL</option>';
                                           }
                                           else
                                           {
                                            echo '   
                                           <option value="tls">TLS</option>
                                           <option selected value="ssl">SSL</option>';
                                           }
                                           ?>
                                           </select>				
										</div> <!-- /form-group -->      
                                        </div> </div>                                     
                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br />
                
                </div><!-- /.box-body -->
                </form>
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
