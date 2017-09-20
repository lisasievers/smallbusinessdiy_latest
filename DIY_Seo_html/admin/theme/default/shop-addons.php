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
    		"serverSide": true,
            "ajax": {
            "url": "http://api.prothemes.biz/tools/shop_addon.php",
            "dataType": "jsonp"
        }
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
            <li><a href="/admin/?route=edit-tools&id=<?php echo $dID; ?>&edit"><i class="fa fa-cogs"></i> Admin</a></li>
            <li class="active"><?php echo $p_title; ?> </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Addons </h3>
                </div><!-- /.box-header -->

                <div class="box-body">
                 <?php
                if(isset($msg)){
                    echo $msg;
                }?>
                <br />
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mySitesTable">
                	<thead>
                		<tr>
                              <th>ID</th>
                              <th>Tool Name</th>
                              <th>Price</th>
                              <th>Tool Link</th>
                              <th>Buy Now</th>
                		</tr>
                	</thead>         
                    <tbody>                        
                    </tbody>
                </table>
                
                </div><!-- /.box-body -->
      
              </div><!-- /.box -->
      
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
