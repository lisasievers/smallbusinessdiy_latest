<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>

  <div class="container main-container">
	<div class="row">
      	
          	<div class="col-md-8 main-index">
            
            <div class="xd_top_box">
             <?php echo $ads_720x90; ?>
            </div>
            
              	<h2 id="title"><?php echo $data['tool_name']; ?></h2>

                <?php if ($pointOut != 'output') { ?>
                        <br />
                        <table class="table table-bordered table-striped">
                            <tbody>
                                        <tr>
                                            <td><strong><?php echo $lang['15']; ?></strong></td>
                                            <td><span class="badge bg-green"><?php echo $ip; ?></span></td>

                                        </tr>
                                        <tr>
                                            <td><strong><?php echo $lang['16']; ?></strong></td>
                                            <td><span class="badge bg-aqua"><?php echo $city; ?></span></td>

                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['17']; ?></strong></td>
                                            <td><span class="badge bg-purple"><?php echo $region; ?></span></td>
                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['18']; ?></strong></td>
                                           <td><span class="badge bg-orange"><?php echo $country; ?></span></td>
                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['22']; ?></strong></td>
                                           <td><span class="badge bg-olive"><?php echo $country_code; ?></span></td>
                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['19']; ?></strong></td>
                                           <td><span class="badge bg-blue"><?php echo $isp; ?></span></td>
                                        </tr>
                                       <tr>
                                           <td><strong><?php echo $lang['20']; ?></strong></td>
                                           <td><span class="badge bg-red"><?php echo $latitude; ?></span></td>
                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['21']; ?></strong></td>
                                           <td><span class="badge bg-maroon"><?php echo $longitude; ?></span></td>
                                        </tr>
                                    </tbody>
                                    
                                </table>   

<?php  } ?>

<br />

<div class="xd_top_box">
<?php echo $ads_720x90; ?>
</div>

<h2 id="sec1" class="about_tool"><?php echo $lang['11'].' '.$data['tool_name']; ?></h2>
<p>
<?php echo $data['about_tool']; ?>
</p> <br />
</div>              
            
<?php
// Sidebar
require_once(THEME_DIR."sidebar.php");
?>     		
        </div>
    </div> <br />