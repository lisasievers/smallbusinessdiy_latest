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
            <li><a href="/admin/?route=interface"><i class="fa fa-desktop"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Interface Settings</h3>
                </div><!-- /.box-header -->
                <form action="#" method="POST">                      
                <div class="box-body">
                <div class="row" style="padding-left: 5px;">
                <div class="col-md-8">
                <br />
                <table class="table table-hover table-bordered">
                <tbody>
                <tr>
                                                    <td style="width: 200px;">Active Template</td>
                                                    <td><span class="badge bg-green"><?php echo ucwords($activeTheme); ?> Theme</span></td>
                                                </tr> 
                                                    <tr>
                                                    <td style="width: 200px;">Active Language</td>
                                                    <td><span class="badge bg-blue"><?php echo str_replace(".PHP","",strtoupper($activeLang)); ?></span></td>         
                                                </tr> 
                </tbody></table>
                    <br />                            
                <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <div class="form-group">
                <label>Select your template: </label>
                <select name="theme" class="form-control">
                <?php
                foreach($themeList as $cTheme){
                echo '<option value="'.$cTheme.'">'.ucfirst($cTheme).' Theme</option>'; 
                }
                ?>
                </select>
                </div>
                                                     
                <div class="form-group">
                <label>Select your language: </label>
                <select name="lang" class="form-control">
                <?php
                foreach($langList as $cLang){
                echo '<option value="'.$cLang.'">'.str_replace(".PHP","",strtoupper($cLang)).'</option>'; 
                }
                ?>
                </select>
                </div> 
                <br />
                </div></div>
                <input type="submit" name="save" value="Save" class="btn btn-primary"/>
                <br />
                
                </div><!-- /.box-body -->
                </form>
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
