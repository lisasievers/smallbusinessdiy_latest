<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Unifield
 */
?>
<div id="footer-wrapper">
    	<div class="container">
        <?php if ( ! dynamic_sidebar( 'footer-head' ) ) : ?>                                   
               <?php endif; // end footer widget area ?>   
            <?php if ( ! dynamic_sidebar( 'footer-headmenu' ) ) : ?>                                   
               <?php endif; // end footer widget area ?>   
            <div class="clear gap"><!--clear both --></div>
            <?php if ( ! dynamic_sidebar( 'footer-1' ) ) : ?>             
               <?php endif; // end footer widget area ?>    
                        
               <?php if ( ! dynamic_sidebar( 'footer-2' ) ) : ?>                                  	
               <?php endif; // end footer widget area ?>   
            
               <?php if ( ! dynamic_sidebar( 'footer-3' ) ) : ?>                
               <?php endif; // end footer widget area ?>  
              
               <?php if ( ! dynamic_sidebar( 'footer-4' ) ) : ?>                 
               <?php endif; // end footer widget area ?>    
                
                
            <div class="clear"></div>
        </div><!--end .container-->
        
        <div class="copyright-wrapper">
        	<div class="container">
            	<div class="copyright-txt">
					<?php bloginfo('name'); ?>. <?php _e('All Rights Reserved', 'unifield');?>           
                </div>
                <div class="design-by">
				  <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'unifield' ) ); ?>"><?php //printf( __( 'Proudly powered by %s', 'unifield' ), 'WordPress' ); ?></a>
                </div>
                 <div class="clear"></div>
                 
                 <?php $hidesocial = get_theme_mod('disabled_social', '1'); ?>
	 				 <?php if($hidesocial == ''){ ?> 
                  <div class="social-icons">                   
                   <?php $fb_link = get_theme_mod('fb_link');
		 				if( !empty($fb_link) ){ ?>
            			<a title="facebook" class="fa fa-facebook" target="_blank" href="<?php echo esc_url($fb_link); ?>"></a>
           		   <?php } ?>
                
                   <?php $twitt_link = get_theme_mod('twitt_link');
					if( !empty($twitt_link) ){ ?>
					<a title="twitter" class="fa fa-twitter" target="_blank" href="<?php echo esc_url($twitt_link); ?>"></a>
          		  <?php } ?>
            
    			  <?php $gplus_link = get_theme_mod('gplus_link');
					if( !empty($gplus_link) ){ ?>
					<a title="google-plus" class="fa fa-google-plus" target="_blank" href="<?php echo esc_url($gplus_link); ?>"></a>
           		  <?php }?>
            
           		  <?php $linked_link = get_theme_mod('linked_link');
					if( !empty($linked_link) ){ ?>
					<a title="linkedin" class="fa fa-linkedin" target="_blank" href="<?php echo esc_url($linked_link); ?>"></a>
          		  <?php } ?>                  
                </div><!--end .social-icons-->
                <?php } ?>
                
            </div>           
        </div>        
    </div>
</div><!--#end pageholder-->
<?php wp_footer(); ?>
</body>
</html>