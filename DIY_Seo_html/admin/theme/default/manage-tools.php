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
            <li><a href="#"><i class="fa fa-cogs"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Complete SEO Tools List</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                  <table id="seoToolTable" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Tool UID</th>
                        <th>Tool Name</th>
                        <th>Status</th>
                        <th>Position</th>
                        <th>Edit</th>
                        <th>Disable</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($toolList as $seoTool){
                        $toolActive = filter_var($seoTool["tool_show"], FILTER_VALIDATE_BOOLEAN);
                        
                        if($toolActive){
                         $toolActive = "<span style='color: #27ae60;'>Active</span>";
                         $toolActiveBut = '<a class="btn btn-block btn-danger btn-sm" href="/admin/?route=manage-tools&id='.$seoTool["id"].'&disable">Disable</a>';
                        }else{
                           $toolActive = "<span style='color: #c0392b;'>Not Active</span>"; 
                           $toolActiveBut = '<a class="btn btn-block btn-success btn-sm" href="/admin/?route=manage-tools&id='.$seoTool["id"].'&enable">Enable</a>';
                        }
                
                        echo '<tr>
                        <td>'.$seoTool["uid"].'</td>
                        <td>'.$seoTool["tool_name"].'</td>
                        <td>'.$toolActive.'</td>
                        <td>'.$seoTool["tool_no"].'</td>
                        <td><a class="btn btn-block btn-primary btn-sm" href="/admin/?route=edit-tools&id='.$seoTool["id"].'&edit">Edit</a></td>
                        <td>'.$toolActiveBut.'</td>
                      </tr>';
                    }
                    ?>

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
