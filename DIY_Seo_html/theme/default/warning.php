<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
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
                
                <br />
                
                <div class="alert alert-error">
                <br />
                <strong>Alert!</strong> <?php echo $warningMsg; ?>
                <br /><br />
                </div>
                
                


                <br />
            </div>
            <?php 
            // Sidebar 
            require_once(THEME_DIR. "sidebar.php"); 
            ?>
        </div>
    </div>
    <br />