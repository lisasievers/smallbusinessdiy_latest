<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>

<script>
var msg1 = "<?php echo $lang['228']; ?>";
var msg2 = "<?php echo $lang['229']; ?>";
var msg3 = "<?php echo $lang['230']; ?>";
</script>

<script src="<?php echo $theme_path; ?>js/contact.js" type="text/javascript"></script>

<style>
#c_alert1 { 
    display:none; 
} 
#c_alert2{ 
    display:none; 
}
</style>

  <div class="container main-container">
	<div class="row">
      	
          	<div class="col-md-8 main-index">

              	<h2 id="title" class="text-center"><?php echo $lang['232']; ?></h2>
                <hr />
                <br />
                        <div id="c_alert1">

                    <div class="alertNew alertNew-block alertNew-success">
	
                    <button data-dismiss="alert" class="close" type="button">		
                    X
                    </button>
	
                    <i class="fa fa-check green"></i>							
                    <b>Alert!!</b> <?php echo $lang['229']; ?>.
                    </div>

                        </div>
                        
                        <div id="c_alert2">
                    <div class="alertNew alertNew-block alertNew-danger">
	
                    <button data-dismiss="alert" class="close" type="button">		
                    X
                    </button>
	
                    <i class="fa fa-ban red"></i>							
                    <b>Alert!</b> <?php echo $lang['231']; ?>
                    </div>
                        </div>
                        
        <form method="post" action="#">
                        <div class="modal-body">
        <h4> <?php echo $lang['233']; ?></h4>
        <?php echo $lang['234']; ?>
<br /><br />

                            <div class="form-group">
                                    <label><?php echo $lang['235']; ?>:</label>
                                    <input type="text" placeholder="<?php echo $lang['240']; ?>" class="form-control" id="c_name" name="c_name">
                            </div>
                            <div class="form-group">
                                    <label><?php echo $lang['236']; ?>:</label>
                                    <input type="email" placeholder="<?php echo $lang['241']; ?>" class="form-control" id="c_email" name="c_email">
                            </div>
                            <div class="form-group">
                                    <label><?php echo $lang['237']; ?>:</label>
                                    <input type="email" placeholder="<?php echo $lang['242']; ?>" class="form-control" id="c_subject" name="c_subject">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang['238']; ?>:</label>
                                <textarea style="height: 180px;" placeholder="<?php echo $lang['243']; ?>" class="form-control" id="email_message" name="email_message"></textarea>
                            </div>
                        </div>
        <?php
        if ($cap_c == "on") {
            echo '<center>
            <h4>'.$lang['7'].'</h4>
            <img id="capImg" src="' . $_SESSION['captcha']['image_src'] . '" alt="Captcha" class="imagever" style="border: 2px solid #ffffff !important;">
            
            <div class="input-group" style="width: 20% !important;">
              <input type="text" class="form-control" id="scode" name="scode" style="box-shadow: none !important;">
              <span onclick="reloadCap()" class="input-group-addon" style="cursor: pointer;"><i class="fa fa-refresh"></i></span>
            </div>
            <style>.fa.fa-refresh:hover {  transform: rotate(90deg); }.fa.fa-refresh { transition: transform 0.5s ease 0s; }</style>
            </center>
            <br>';  
        }
        ?>
                        <div class="modal-footer clearfix">
                        <br />
                        <?php                if ($cap_c == "on")
        { ?> 
                                    <button class="btn btn-primary" onclick="contactDocX()" type="button"><i class="fa fa-envelope"></i> <?php echo $lang['239']; ?></button>
        <?php  } else { ?>
                            <button class="btn btn-primary" onclick="contactDoc()" type="button"><i class="fa fa-envelope"></i> <?php echo $lang['239']; ?></button>
                       <?php } ?>
                        </div>
                    </form>  
  

<br />

<div class="xd_top_box">
<?php echo $ads_720x90; ?>
</div>

</div>              
            
<?php
// Sidebar
require_once(THEME_DIR."sidebar.php");
?>     		
        </div>
    </div> <br />