<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>

<style>
.percentbox {
    text-align: center;
    font-size: 18px;
}
.percentimg {
    text-align: center;
    display: none;
}
#resultBox{
    display:none;
}
</style>
<script>
var sucMsg = "<?php echo $lang['143']; ?>";
var errMsg = "<?php echo $lang['144']; ?>";
var smErr = "<?php echo $lang['145']; ?>";
var msgDomain = "<?php echo $lang['28']; ?>";
var msgTab1 = "<?php echo $lang['147']; ?>";
var msgTab2 = "<?php echo $lang['148']; ?>";
var msgTab3 = "<?php echo $lang['149']; ?>";
</script>
<script src='../core/library/backlink.js'></script>

  <div class="container main-container">
	<div class="row">
      	
          	<div class="col-md-8 main-index">
            
            <div class="xd_top_box">
             <?php echo $ads_720x90; ?>
            </div>
            
                <h2 id="title"><?php echo $data['tool_name']; ?></h2>
                <div id="mainbox">
               <?php if ($pointOut != 'output') { ?>
               <br />
               <p><?php echo $lang['139']; ?>
               </p>
               <input type="text" id="myurl" value="" class="form-control"/>
               <br />
               <?php
               if ($toolCap)
               {
               echo $captchaCode;  
               }
               ?>
               <div class="text-center">
               <a class="btn btn-info" style="cursor:pointer;" id="checkButton"><?php echo $lang['8']; ?></a>
               </div>     
               </div>           
               <?php 
               } 
               ?>
            <div id="resultBox">
            <div class="percentimg">
            <img src="<?php echo $theme_path; ?>img/load.gif" />
            <br />
            <?php echo $lang['146']; ?>...
            <br />
            </div>

            <div id="results"></div>

            <div class="text-center">
            <br /> &nbsp; <br />
            <a class="btn btn-info" href="<?php echo $toolURL; ?>"><?php echo $lang['27']; ?></a>
            <br />
            </div>
            </div>
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