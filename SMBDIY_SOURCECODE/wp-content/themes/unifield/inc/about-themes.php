<?php
/**
 *unifield About Theme
 *
 * @package Unifield
 */

//about theme info
add_action( 'admin_menu', 'unifield_abouttheme' );
function unifield_abouttheme() {    	
	add_theme_page( __('About Theme Info', 'unifield'), __('About Theme Info', 'unifield'), 'edit_theme_options', 'unifield_guide', 'unifield_mostrar_guide');   
} 

//guidline for about theme
function unifield_mostrar_guide() { 	
?>
<div class="wrap-GT">
	<div class="gt-left">
   		   <div class="heading-gt">
			  <h3><?php _e('About Theme Info', 'unifield'); ?></h3>
		   </div>
          <p><?php _e('Unifield is a Free Corporate WordPress theme. It is Perfect for all Corporate, Professional, personal, medical and any type of business. It is user friendly customizer options and Compatible in WordPress Latest Version. also Compatible with WooCommerce, Nextgen gallery ,Contact Form 7 and many WordPress popular plugins.','unifield'); ?></p>
<div class="heading-gt"> <?php _e('Theme Features', 'unifield'); ?></div>
 

<div class="col-2">
  <h4><?php _e('Theme Customizer', 'unifield'); ?></h4>
  <div class="description"><?php _e('The built-in customizer panel quickly change aspects of the design and display changes live before saving them.', 'unifield'); ?></div>
</div>

<div class="col-2">
  <h4><?php _e('Responsive Ready', 'unifield'); ?></h4>
  <div class="description"><?php _e('The themes layout will automatically adjust and fit on any screen resolution and looks great on any device. Fully optimized for iPhone and iPad.', 'unifield'); ?></div>
</div>

<div class="col-2">
<h4><?php _e('Cross Browser Compatible', 'unifield'); ?></h4>
<div class="description"><?php _e('Our themes are tested in all mordern web browsers and compatible with the latest version including Chrome,Firefox, Safari, Opera, IE11 and above.', 'unifield'); ?></div>
</div>

<div class="col-2">
<h4><?php _e('E-commerce', 'unifield'); ?></h4>
<div class="description"><?php _e('Fully compatible with WooCommerce plugin. Just install the plugin and turn your site into a full featured online shop and start selling products.', 'unifield'); ?></div>
</div>
<hr />  
</div><!-- .gt-left -->
	
	<div class="gt-right">			
			<div>				
				<a href="<?php echo esc_url( UNIFIELD_LIVE_DEMO ); ?>" target="_blank"><?php _e('Live Demo', 'unifield'); ?></a> | 
				<a href="<?php echo esc_url( UNIFIELD_PROTHEME_URL ); ?>"><?php _e('Purchase Pro', 'unifield'); ?></a> | 
				<a href="<?php echo esc_url( UNIFIELD_THEME_DOC ); ?>" target="_blank"><?php _e('Documentation', 'unifield'); ?></a>
              
				<hr />  
                <ul>
                 <li><?php _e('Theme Customizer', 'unifield'); ?></li>
                 <li><?php _e('Responsive Ready', 'unifield'); ?></li>
                 <li><?php _e('Cross Browser Compatible', 'unifield'); ?></li>
                 <li><?php _e('E-commerce', 'unifield'); ?></li>
                 <li><?php _e('Contact Form 7 Plugin Compatible', 'unifield'); ?></li>  
                 <li><?php _e('User Friendly', 'unifield'); ?></li> 
                 <li><?php _e('Translation Ready', 'unifield'); ?></li>
                 <li><?php _e('Many Other Plugins  Compatible', 'unifield'); ?></li>   
                </ul>              
               
			</div>		
	</div><!-- .gt-right-->
    <div class="clear"></div>
</div><!-- .wrap-GT -->
<?php } ?>