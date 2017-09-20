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
            <li><a href="/admin/?route=miscellaneous"><i class="fa fa-bolt"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">General Settings</h3>
                </div><!-- /.box-header -->

                <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <br />
                <div class="alert alert-warning">
                <strong>Warning!</strong> All actions are irreversible! 
                </div>
                <div class="text-center">
                    <br/>
                    <hr/>
                    <label style="font-weight: bold;"> Clear all logged information </label> <small>(Site PageViews, Visitors etc..)</small> <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_1" onclick="return checkBTX();">Process</a>
                    <hr/>
                    <label style="font-weight: bold;"> Clear all recent history </label>  <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_2" onclick="return checkBTX();">Process</a>
                    <hr/>
                    <label style="font-weight: bold;"> Clear admin logged history </label>  <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_3" onclick="return checkBTX();">Process</a>
                    <hr/>
                    <label style="font-weight: bold;"> Clear all not verfied user accounts </label>  <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_4" onclick="return checkBTX();">Process</a>
                    <hr/>
                    <label style="font-weight: bold;"> Clear all site screenshot data </label>  <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_5" onclick="return checkBTX();">Process</a>
                    <hr/>
                    <label style="font-weight: bold;"> Clear all user acccounts </label>  <br/>
                    <a class="btn btn-danger" href="/admin/?route=miscellaneous&action_6" onclick="return checkBTX();">Process</a>
                    <hr/>
                    
                </div>
                
                </div><!-- /.box-body -->
      
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
