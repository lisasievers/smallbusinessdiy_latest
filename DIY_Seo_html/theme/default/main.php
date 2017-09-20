<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));
/*
 * @author Balaji
 * @name: A to Z SEO Tools - PHP Script
 * @Theme: Default Style
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>
 <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo $p_title; ?> 
            <small>Control panel</small>
          </h1>
          
        </section>

        <!-- Main content -->
        <section class="content">

	<div class="row">
    
      		<div class="col-md-12" id="seoTools">
          
            <?php
            $count = 1;
            $smCount = 0;
            $oneTime  = 0;
            $tools_count =count($tools);
            $loop = 0; 
            foreach ($tools as $tool)
            {  
                $loop++;
                if ($count==1)
                {
                $smCount++;
                if($smCount == 4){
                    if($oneTime == 0){
                        $oneTime =1;
                        echo '<div class="text-center moreToolsBut"><button class="btn btn-info" id="browseTools">Browse More Tools</button></div>';
                    }
                    echo '<div class="row hideAll">';
                    $smCount--;
                }else{
                    echo '<div class="row">';
                }    

                } 
                if(!file_exists(THEME_DIR.$tool[2]))
                $tool[2] = "icons/no_image.png";   
                echo '   <div class="col-md-3">
                            <div class="thumbnail">
                                <a class="seotoollink" data-placement="top" data-toggle="tooltip" data-original-title="'.$tool[0].'" title="'.$tool[0].'" href="'.$baseURL.$tool[1].'"><img alt="'.$tool[0].'" src="'.$theme_path.$tool[2].'" class="seotoolimg" />
                                <div class="caption">
                                        '.$tool[0].'
                                </div></a>
                            </div>
                        </div>';
                        if ($loop == 20)
                        { ?>
                            <div class="xd_top_box">
                                <?php //echo $ads_468x70; ?>
                            </div>
                       <?php }
                if ($tools_count==$loop)
                { 
               // if ($count==4)
                echo '</div><!-- /.row -->';
                $count = 0;
                }
                    if ($count==4)
                    {
                    $count = 0;
                    echo '</div><!-- /.row -->';
                    } 
                    $count++;   
                   
            } 
            ?>
            
          

      		</div>
              
            <?php
            // Sidebar
            require_once("sidebar.php");
            ?>
        </div><!--row -->
      </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
