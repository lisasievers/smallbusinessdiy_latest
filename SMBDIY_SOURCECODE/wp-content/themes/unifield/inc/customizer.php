<?php
/**
 *unifield Theme Customizer
 *
 * @package Unifield
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function unifield_customize_register( $wp_customize ) {	
	
	function unifield_sanitize_checkbox( $checked ) {
		// Boolean check.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
		
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	
	//Layout Options
	$wp_customize->add_section('layout_option',array(
			'title'	=> __('Layout Option','unifield'),			
			'priority'	=> 1,
	));		
	
	$wp_customize->add_setting('box_layout',array(
			'sanitize_callback' => 'unifield_sanitize_checkbox',
	));	 

	$wp_customize->add_control( 'box_layout', array(
    	   'section'   => 'layout_option',    	 
		   'label'	=> __('Check to Box Layout','unifield'),
    	   'type'      => 'checkbox'
     )); //Layout Section 
	
	$wp_customize->add_setting('color_scheme',array(
			'default'	=> '#02adf4',
			'sanitize_callback'	=> 'sanitize_hex_color'
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','unifield'),			
			 'description'	=> __('More color options in PRO Version','unifield'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	// Slider Section		
	$wp_customize->add_section( 'slider_section', array(
            'title' => __('Slider Settings', 'unifield'),
            'priority' => null,
			'description'	=> __('Featured Image Size Should be same ( 1400x600 ) More slider settings available in PRO Version.','unifield'),            			
    ));
	
	
	$wp_customize->add_setting('page-setting7',array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','unifield'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','unifield'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','unifield'),
			'section'	=> 'slider_section'
	));	// Slider Section
	
	 $wp_customize->add_setting('slider_readmore',array(
	 		'default'	=> null,
			'sanitize_callback'	=> 'sanitize_text_field'
	 ));
	 
	 $wp_customize->add_control('slider_readmore',array(
	 		'settings'	=> 'slider_readmore',
			'section'	=> 'slider_section',
			'label'		=> __('Add text for slide read more button','unifield'),
			'type'		=> 'text'
	 ));// Slider Read more	
	
	$wp_customize->add_setting('disabled_slides',array(
				'default' => true,
				'sanitize_callback' => 'unifield_sanitize_checkbox',
				'capability' => 'edit_theme_options',
	));	 
	
	$wp_customize->add_control( 'disabled_slides', array(
			   'settings' => 'disabled_slides',
			   'section'   => 'slider_section',
			   'label'     => __('Uncheck To Enable This Section','unifield'),
			   'type'      => 'checkbox'
	 ));//Disable Slider Section	
	
	
	// Homepage Why Choose Us Section 	
	$wp_customize->add_section('section_first',array(
		'title'	=> __('Why Choose Us Section','unifield'),
		'description'	=> __('Select Page from the dropdown for why choose us section','unifield'),
		'priority'	=> null
	));
	
	$wp_customize->add_setting('page-setting1',	array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
		));
 
	$wp_customize->add_control(	'page-setting1',array('type' => 'dropdown-pages',			
			'section' => 'section_first',
	));
	
	$wp_customize->add_setting('disabled_welcomepg',array(
			'default' => true,
			'sanitize_callback' => 'unifield_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 
	
	$wp_customize->add_control( 'disabled_welcomepg', array(
			   'settings' => 'disabled_welcomepg',
			   'section'   => 'section_first',
			   'label'     => __('Uncheck To Enable This Section','unifield'),
			   'type'      => 'checkbox'
	 ));//why choose us Section 	
	
	// four services Boxes Section 	
	$wp_customize->add_section('section_second', array(
		'title'	=> __('Four Services Boxes Section','unifield'),
		'description'	=> __('Select Pages from the dropdown for four services boxes section','unifield'),
		'priority'	=> null
	));		
	
	$wp_customize->add_setting('page-column1',	array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
		));
 
	$wp_customize->add_control(	'page-column1',array('type' => 'dropdown-pages',			
			'section' => 'section_second',
	));		
	
	$wp_customize->add_setting('page-column2',	array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
		));
 
	$wp_customize->add_control(	'page-column2',array('type' => 'dropdown-pages',			
			'section' => 'section_second',
	));
	
	$wp_customize->add_setting('page-column3',	array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
		));
 
	$wp_customize->add_control(	'page-column3',array('type' => 'dropdown-pages',			
			'section' => 'section_second',
	));
	
	$wp_customize->add_setting('page-column4',	array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
		));
 
	$wp_customize->add_control(	'page-column4',array('type' => 'dropdown-pages',			
			'section' => 'section_second',
	));//end four column page boxes	
	
	$wp_customize->add_setting('disabled_pgboxes',array(
			'default' => true,
			'sanitize_callback' => 'unifield_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 
	
	$wp_customize->add_control( 'disabled_pgboxes', array(
			   'settings' => 'disabled_pgboxes',
			   'section'   => 'section_second',
			   'label'     => __('Uncheck To Enable This Section','unifield'),
			   'type'      => 'checkbox'
	 ));//Disable Homepage boxes Section	
	 
	 // Get an Appointment Section 	
	$wp_customize->add_section('section_features',array(
		'title'	=> __('Theme Features Section','unifield'),
		'description'	=> __('Select Page from the dropdown for theme features section','unifield'),
		'priority' => null,
	));
	
	$wp_customize->add_setting('features_page',array(
			'default'	=> '0',			
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('features_page',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for features page:','unifield'),
			'section'	=> 'section_features'
	));	// Get an features Section 
	
	$wp_customize->add_setting('disabled_ftrpage',array(
				'default' => true,
				'sanitize_callback' => 'unifield_sanitize_checkbox',
				'capability' => 'edit_theme_options',
	));	 
	
	$wp_customize->add_control( 'disabled_ftrpage', array(
			   'settings' => 'disabled_ftrpage',
			   'section'   => 'section_features',
			   'label'     => __('Uncheck To Enable This Section','unifield'),
			   'type'      => 'checkbox'
	 ));//Disable Slider Section 
	 
	 $wp_customize->add_section('social_sec',array(
			'title'	=> __('Social Settings','unifield'),
			'description' => __( 'Add social icons link here. more social icons available in PRO Version', 'unifield' ),			
			'priority'		=> null
	));
	$wp_customize->add_setting('fb_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'	
	));
	
	$wp_customize->add_control('fb_link',array(
			'label'	=> __('Add facebook link here','unifield'),
			'section'	=> 'social_sec',
			'setting'	=> 'fb_link'
	));	
	$wp_customize->add_setting('twitt_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('twitt_link',array(
			'label'	=> __('Add twitter link here','unifield'),
			'section'	=> 'social_sec',
			'setting'	=> 'twitt_link'
	));
	$wp_customize->add_setting('gplus_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('gplus_link',array(
			'label'	=> __('Add google plus link here','unifield'),
			'section'	=> 'social_sec',
			'setting'	=> 'gplus_link'
	));
	$wp_customize->add_setting('linked_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('linked_link',array(
			'label'	=> __('Add linkedin link here','unifield'),
			'section'	=> 'social_sec',
			'setting'	=> 'linked_link'
	));
	
	$wp_customize->add_setting('disabled_social',array(
				'default' => true,
				'sanitize_callback' => 'unifield_sanitize_checkbox',
				'capability' => 'edit_theme_options',
	));	 
	
	$wp_customize->add_control( 'disabled_social', array(
			   'settings' => 'disabled_social',
			   'section'   => 'social_sec',
			   'label'     => __('Uncheck To Enable This Section','unifield'),
			   'type'      => 'checkbox'
	 ));//Disable Slider Section 
		
}
add_action( 'customize_register', 'unifield_customize_register' );

function unifield_custom_css(){
		?>
        	<style type="text/css"> 					
					a, .blog_lists h2 a:hover,
					#sidebar ul li a:hover,									
					.blog_lists h3 a:hover,
					.cols-4 ul li a:hover, .cols-4 ul li.current_page_item a,
					.recent-post h6:hover,					
					.fourbox:hover h3,
					.footer-icons a:hover,
					.sitenav ul li a:hover, .sitenav ul li.current_page_item a, 
					.postmeta a:hover,
					.pagebutton:hover
					{ color:<?php echo esc_html( get_theme_mod('color_scheme','#02adf4')); ?>;}
					 
					
					.pagination ul li .current, .pagination ul li a:hover, 
					#commentform input#submit:hover,					
					.nivo-controlNav a.active,
					.ReadMore,
					.slide_info .slide_more,
					.appbutton:hover,					
					h3.widget-title,						
					#sidebar .search-form input.search-submit,				
					.wpcf7 input[type='submit'],
					#featureswrap			
					{ background-color:<?php echo esc_html( get_theme_mod('color_scheme','#02adf4')); ?>;}					
					
					
			</style> 
<?php 
}
         
add_action('wp_head','unifield_custom_css');	 

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function unifield_customize_preview_js() {
	wp_enqueue_script( 'unifield_customizer', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20161014', true );
}
add_action( 'customize_preview_init', 'unifield_customize_preview_js' );