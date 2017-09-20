<?php 
/**
 *Unifield functions and definitions
 *
 * @package Unifield
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'unifield_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.  
 */
function unifield_setup() {		
	global $content_width;   
    if ( ! isset( $content_width ) ) {
        $content_width = 640; /* pixels */
    }	

	load_theme_textdomain( 'unifield', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support('woocommerce');
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-header', array( 
		'default-text-color' => false,
		'header-text' => false,
	) );
	add_theme_support( 'title-tag' );	
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
	) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'unifield' ),
		'footer' => __( 'Footer Menu', 'unifield' ),
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );
	add_editor_style( 'editor-style.css' );
} 
endif; // unifield_setup
add_action( 'after_setup_theme', 'unifield_setup' );
function unifield_widgets_init() { 	
	
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'unifield' ),
		'description'   => __( 'Appears on blog page sidebar', 'unifield' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer Widget 1', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="cols-4 widget-column-1 %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer Widget 2', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="cols-4 widget-column-2 %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer Widget 3', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="cols-4 widget-column-3 %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer Widget 4', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="cols-4 widget-column-4 %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );	
	register_sidebar( array(
		'name'          => __( 'Footer Head', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-head',
		'before_widget' => '<div id="foottitle" class="one_half">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Headmenu', 'unifield' ),
		'description'   => __( 'Appears on footer', 'unifield' ),
		'id'            => 'footer-headmenu',
		'before_widget' => '<div id="footmenu" class="one_half">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
	) );	
	
}
add_action( 'widgets_init', 'unifield_widgets_init' );


function unifield_font_url(){
		$font_url = '';		
		
		/* Translators: If there are any character that are not
		* supported by Montserrat, trsnalate this to off, do not
		* translate into your own language.
		*/
		$montserrat = _x('on','montserrat:on or off','unifield');			
		
		if('off' !== $montserrat ){
			$font_family = array();
			
			if('off' !== $montserrat){
				$font_family[] = 'Montserrat:300,400,600,700,800,900';
			}
					
						
			$query_args = array(
				'family'	=> urlencode(implode('|',$font_family)),
			);
			
			$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
		}
		
	return $font_url;
	}


function unifield_scripts() {
	wp_enqueue_style('unifield-font', unifield_font_url(), array());
	wp_enqueue_style( 'unifield-basic-style', get_stylesheet_uri() );	
	wp_enqueue_style( 'nivo-slider', get_template_directory_uri()."/css/nivo-slider.css" );
	wp_enqueue_style( 'unifield-responsive', get_template_directory_uri()."/css/responsive.css" );		
	wp_enqueue_style( 'unifield-default', get_template_directory_uri()."/css/default.css" );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri()."/css/font-awesome.css" );
	wp_enqueue_script( 'jquery-nivo-slider', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array('jquery') );
	wp_enqueue_script( 'unifield-custom', get_template_directory_uri() . '/js/custom.js' );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'unifield_scripts' );

function unifield_ie_stylesheet(){
	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style('unifield-ie', get_template_directory_uri().'/css/ie.css', array( 'unifield-style' ), '20160928' );
	wp_style_add_data('unifield-ie','conditional','lt IE 10');
	
	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'unifield-ie8', get_template_directory_uri() . '/css/ie8.css', array( 'unifield-style' ), '20160928' );
	wp_style_add_data( 'unifield-ie8', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'unifield-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'unifield-style' ), '20160928' );
	wp_style_add_data( 'unifield-ie7', 'conditional', 'lt IE 8' );	
	}
add_action('wp_enqueue_scripts','unifield_ie_stylesheet');

define('UNIFIELD_THEME_DOC','https://gracethemes.com/documentation/unifield-doc/','unifield');
define('UNIFIELD_PROTHEME_URL','https://gracethemes.com/themes/best-corporate-wordpress-theme/','unifield');
define('UNIFIELD_LIVE_DEMO','https://gracethemes.com/demo/unifield/','unifield');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom template for about theme.
 */
if ( is_admin() ) { 
require get_template_directory() . '/inc/about-themes.php';
}

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

if ( ! function_exists( 'unifield_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 */
function unifield_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

//Custom lines
add_action( 'wp_enqueue_scripts', 'custom_google_fonts' );
function custom_google_fonts() {
wp_enqueue_style( 'custom-font', "//fonts.googleapis.com/css?family=Open+Sans");
}

function reg_scripts() {
wp_enqueue_style( 'bootstrapstyle', get_template_directory_uri() . '/css/bootstrap.min.css' );
wp_enqueue_style( 'page-pricing', get_template_directory_uri() . '/css/page_pricing.css' );
wp_enqueue_script( 'bootstrap-script', get_template_directory_uri() . '/js/bootstrap.min.js', array(), true );
}
add_action('wp_enqueue_scripts', 'reg_scripts');

add_action( 'init', 'custom_bootstrap_slider' );
/**
 * Register a Custom post type for.
 */
function custom_bootstrap_slider() {
	$labels = array(
		'name'               => _x( 'Slider', 'post type general name'),
		'singular_name'      => _x( 'Slide', 'post type singular name'),
		'menu_name'          => _x( 'Bootstrap Slider', 'admin menu'),
		'name_admin_bar'     => _x( 'Slide', 'add new on admin bar'),
		'add_new'            => _x( 'Add New', 'Slide'),
		'add_new_item'       => __( 'Name'),
		'new_item'           => __( 'New Slide'),
		'edit_item'          => __( 'Edit Slide'),
		'view_item'          => __( 'View Slide'),
		'all_items'          => __( 'All Slide'),
		'featured_image'     => __( 'Featured Image', 'text_domain' ),
		'search_items'       => __( 'Search Slide'),
		'parent_item_colon'  => __( 'Parent Slide:'),
		'not_found'          => __( 'No Slide found.'),
		'not_found_in_trash' => __( 'No Slide found in Trash.'),
	);

	$args = array(
		'labels'             => $labels,
		'menu_icon'	     => 'dashicons-star-half',
    	        'description'        => __( 'Description.'),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
		'supports'           => array('title','editor','thumbnail')
	);

	register_post_type( 'slider', $args );
}

// Resource shortcode
add_shortcode( 'resource-basic', 'rmcc_post_listing_shortcode1' );
function rmcc_post_listing_shortcode1( $atts ) {
    ob_start();
    $query = new WP_Query( array(
        'post_type' => 'resource',
        'posts_per_page' => 4,
        'order' => 'ASC',
        'orderby' => 'title',
        'post_status' => 'publish',
        'limit'     => 4,

    ) );
    if ( $query->have_posts() ) { ?>
      
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            

            <div class="fourbox" id="post-<?php the_ID(); ?>">
	            <div class="thumbbx">
	            	<?php the_post_thumbnail(); ?>
	            </div>
             <div class="pagecontent">
             <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>            
             <p><?php the_excerpt(); ?></p><a class="pagemore" href="<?php the_permalink(); ?>">READ MORE</a>
			 </div>
        	</div>
            <?php endwhile;
            wp_reset_postdata(); ?>
        
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
}

// Products shortcode
add_shortcode( 'product-basic', 'product_listing_shortcode1' );
function product_listing_shortcode1( $atts ) {
    ob_start();
    $query = new WP_Query( array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'order' => 'ASC',
        'orderby' => 'title',
        'post_status' => 'publish',
        'limit'     => 10,

    ) );
    if ( $query->have_posts() ) { ?>
      
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <div class="threebox ">
			<div class="thumbbx"><?php the_post_thumbnail(); ?></div>
			<div class="pagecontent">

			<?php if ( get_post_meta( get_the_ID(), 'external_url', true ) ) : ?>
    		<a class="pagemore" target="_blank" href="<?php echo esc_url( get_post_meta( get_the_ID(), 'external_url', true ) ); ?>"><?php the_title(); ?></a>
			<?php endif; ?>
			</div>
			</div>
            <?php endwhile;
            wp_reset_postdata(); ?>
        
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
}
