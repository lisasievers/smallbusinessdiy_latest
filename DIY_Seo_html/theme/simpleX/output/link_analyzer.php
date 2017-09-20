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
               <p><?php echo $lang['23']; ?>
               </p>
               <form method="POST" action="<?php echo $toolOutputURL;?>" onsubmit="return fixURL();"> 
               <input type="text" name="url" id="url" value="" class="form-control"/>
               <br />
               <?php
               if ($toolCap)
               {
               echo $captchaCode;   
               }
               ?>
               <input class="btn btn-info" type="submit" value="<?php echo $lang['8']; ?>" name="submit"/>
               </form>     
                          
               <?php 
               } else { 
               //Output Block
               if(isset($error)) {
                
                echo '<br/><br/><div class="alert alert-error">
                <strong>Alert!</strong> '.$error.'
                </div><br/><br/>
                <div class="text-center"><a class="btn btn-info" href="'.$toolURL.'">'.$lang['12'].'</a>
                </div><br/>';
                
               } else {
               ?>

                <br />
                    <table class="table table-bordered table-striped">
                                <thead>
                                        <tr>
                                        <td><?php echo $lang['46']; ?></td>
                                        <td><?php echo $lang['47']; ?></td>
                                        </tr>
                                     </thead>
                                        <tbody>
                                        <tr>
                                            <td><strong><?php echo $lang['48']; ?></strong></td>
                                             <td><span class="badge bg-blue"><?php   echo $total_links; ?></span></td>

                                        </tr>
                                        <tr>
                                            <td><strong><?php echo $lang['49']; ?></strong></td>
                                            <td><span class="badge bg-green"><?php   echo $internal_links_count; ?></span></td>

                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['50']; ?></strong></td>
                                            <td><span class="badge bg-purple"><?php   echo $external_links_count; ?></span></td>
                                        </tr>
                                        <tr>
                                           <td><strong><?php echo $lang['51']; ?></strong></td>
                                           <td><span class="badge bg-orange"><?php   echo $total_nofollow_links; ?></span></td>
                                        </tr>
                                    </tbody>
                                    
        </table>
        <br />
        <h3><?php echo $lang['49']; ?> <small><?php echo $lang['52']; ?></small></h3><br />
              <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <td>No.</td>
                                            <td><?php echo $lang['53']; ?></td>
                                            <td><?php echo $lang['54']; ?></td>
                                        </tr>
                                     </thead>
                                        <tbody>
                                        <?php foreach($internal_links as $count=>$links) { ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $links['href']; ?></td>
                                            <td><?php echo $links['follow_type']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody></table>
                                    
                                            <br />
        <h3><?php echo $lang['50']; ?> <small><?php echo $lang['55']; ?></small></h3><br />
              <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <td>No.</td>
                                            <td><?php echo $lang['53']; ?></td>
                                            <td><?php echo $lang['54']; ?></td>
                                        </tr>
                                     </thead>
                                        <tbody>
                                        <?php foreach($external_links as $count=>$links) { ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $links['href']; ?></td>
                                            <td><?php echo $links['follow_type']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody></table>
                                    
    <div class="text-center">
    <br /> &nbsp; <br />
    <a class="btn btn-info" href="<?php echo $toolURL; ?>"><?php echo $lang['27']; ?></a>
    <br />
    </div>

<?php } } ?>

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