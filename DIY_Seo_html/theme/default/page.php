<?php

defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */
?>
  <style>
.thumbnail > img, .thumbnail a > img {
    display: block;
    height: auto;
    margin-left: auto;
    margin-right: auto;
    max-width: 100%;
    width: 100%;
}
.blog-img {
    margin: 0;
    width: 198px;
}
.col-md-4,.col-md-8 {
    padding-left: 15px;
}
.color-grey {
    color: #bebebe;
    margin-right: 5px;
}
.color_grap {
    color: #141517;
    font-weight: 700;
}
.color_text_in {
    color: #545e6a;
    margin-right: 30px;
}
.text_word {
    color: #fff;
    font-size: 36px;
    text-align: center;
}
.date_down, .date_down2 {
    background: none repeat scroll 0 0 #2BABCF;
    height: 46px;
    width: 60px;
}
.date_up, .date_up2 {
    background: none repeat scroll 0 0 #2390af;
    height: 22px;
    width: 60px;
}
.date, .date_1 {
    height: 68px;
    width: 60px;
}
.center2 {
    text-align: center;
}
.feb2 {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding-top: 2px;
}
.version {
    color: #252c34;
    margin-top: 11px;
}
.version a {
    color: #1e85a2;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
} 
h4, .h4 {
    font-size: 22px;
}
.divider_h, .hr_horiznt_dotted {
    border-bottom: 1px dotted #aaacaf;
}
.mr_top30 {
    margin-top: 25px;
    margin-bottom: 30px;
}
.comment-here {
    color: #252c34;
    margin-bottom: 4px;
}
.font_14 {
    font-size: 14px;
}
.posts_1 {
    margin-top: 12px;
}
.posts_1 h5 {
    color: #545e6a;
    font-family: Dosis,sans-serif;
    font-size: 22px;
    font-weight: 600;
    margin: 0 0 6px;
    text-transform: uppercase;
}
.posts_1 h5 span {
    font-size: 22px;
    font-weight: 300;
}
</style> 
    <div class="container main-container">
        <div class="row">
            <div class="col-md-8 main-index">
            <div class="row">
              <div class="col-md-1">
                <div class="date_1">
                  <div class="date_up2">
                    <div class="center2 feb2"><?php echo $post_month; ?> </div>
                  </div>
                  <div class="date_down2">
                    <div class="text_word"><?php echo $post_day; ?> </div>
                  </div>
                </div>
              </div>
              <div class="col-md-11 pad_left26">
              <div class="romantic_free">
                <h4 style="margin-left: 2px; font-size: 24px;"><?php echo ucfirst($page_title); ?></h4>
                  </div>
                <div class="text_size12 mr_top8 clock"> <span class="color_text_in"> 
                <i style="font-size:14px;" class="fa fa-clock-o color-grey"></i><b class="color_grap"> <?php echo $posted_date; ?>    
                </b> </span>  </div>
              </div>
            </div>
            
                <hr /><br />
                
                <?php echo $page_content; ?>
                
                
                <div class="xd_top_box">
                    <?php echo $ads_720x90; ?>
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