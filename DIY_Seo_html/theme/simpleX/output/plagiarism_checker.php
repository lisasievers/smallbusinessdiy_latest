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
var wordsLimit = '<?php echo $wordLimit; ?>';
var minLimit = '<?php echo $minChar; ?>';
var apiType = '<?php echo $api_type; ?>';
var placeHolderText = '<?php echo $lang['61']; ?>';
</script>

<script src='../core/library/plagiarism.js'></script>

  <div class="container main-container">
	<div class="row">
      	
          	<div class="col-md-8 main-index">
            
            <div class="xd_top_box">
             <?php echo $ads_720x90; ?>
            </div>
            
              	<h2 id="title"><?php echo $data['tool_name']; ?></h2>

                <?php if ($pointOut != 'output') { ?>
      
               <p><?php echo $lang['56']; ?>
               </p>
               
            <div id="mainbox">
                <textarea id="mycontent" rows="3" style="height: 270px;" class="form-control"></textarea> <br />
                <?php
                if ($toolCap)
                {
                echo $captchaCode;   
                }
                ?>
                <div class="text-center"> 
                <a class="btn btn-info" style="cursor:pointer;" id="checkButton"><?php echo $lang['57']; ?></a>
                </div>
                <br />  
                
                <div class="tbox">
                <div class="max-text"><?php echo $lang['59']; ?> <span id="max_words_limit"><?php echo $wordLimit; ?></span> <?php echo $lang['60']; ?>.</div>
                <div class="total-word"><?php echo $lang['58']; ?>: <span id="words-count">0</span></div>
                </div>
            </div>

            <div class="percentimg">
            <img src="<?php echo $theme_path; ?>img/load.gif" />
            <br />
            Checking...
            <br />
            </div>
            
            <div class="percentbox" id="percent">

            </div>
            
            <div>
                <table class="table table-bordered" id="resultList">
                    
                </table>
            </div>

  
                          
               <?php } ?>

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