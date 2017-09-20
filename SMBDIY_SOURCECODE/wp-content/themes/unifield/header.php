<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="container">
 *
 * @package Unifield
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="shortcut icon" type="image/png" href="https://storage.googleapis.com/assets-sitebuilder/images/facicon.ico"/>
<link rel="shortcut icon" type="image/png" href="http://eg.com/favicon.png"/>
	
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="pageholder" <?php if( get_theme_mod( 'box_layout' ) ) { echo 'class="boxlayout"'; } ?>>
<div class="header <?php if( !is_front_page() && !is_home() ){ ?>headerinner<?php } ?>">
        <div class="container">
            <div class="logo">
            			<?php //unifield_the_custom_logo(); ?>
                 <a href="<?php echo esc_url(home_url('/')); ?>"> <img src="https://storage.googleapis.com/assets-sitebuilder/images/SMBDIY_Logo2.png" class="custom-logo" alt="" itemprop="logo"></a>
                        <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                        <span><?php bloginfo('description'); ?></span>
            </div><!-- logo -->
             <div class="hdrright">
             <div class="toggle">
                <a class="toggleMenu" href="#"><?php _e('Menu','unifield'); ?></a>
             </div><!-- toggle --> 
            
            <div class="sitenav">
                    <?php wp_nav_menu(array('theme_location' => 'primary')); ?>
            </div><!-- site-nav -->
            </div>
            <div class="clear"></div>
            
        </div><!-- container -->
  </div><!--.header -->
<?php
if(isset($_COOKIE['emailid'])){$cook=$_COOKIE['emailid'];}else{$cook='';}
//echo $cook;
?>
<input type="hidden" value="<?php echo $cook; ?>" id="iscookie" />
<?php if ( is_front_page() && !is_home() ) { ?>
	<?php $hideslide = get_theme_mod('disabled_slides', '1'); ?>
		<?php if($hideslide == ''){ ?>
              <?php for($sld=7; $sld<10; $sld++) { ?>
                	<?php if( get_theme_mod('page-setting'.$sld)) { ?>
                	<?php $slidequery = new WP_query('page_id='.absint( get_theme_mod('page-setting'.$sld,true)) ); ?>
                	<?php while( $slidequery->have_posts() ) : $slidequery->the_post();
                			$image = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
                			$img_arr[] = $image;
               				$id_arr[] = $post->ID;
                		endwhile;
						wp_reset_postdata();
                	}
                }
                ?>
<?php if(!empty($id_arr)){ ?>
        <div id="slider" class="nivoSlider">
            <?php 
            $i=1;
            foreach($img_arr as $url){ ?>
            <?php if(!empty($url)){ ?>
            <img src="<?php echo esc_url( $url ); ?>" title="#slidecaption<?php echo $i; ?>" />
            <?php }else{ ?>
            <img src="<?php echo esc_url( get_template_directory_uri() ) ; ?>/images/slides/slider-default.jpg" title="#slidecaption<?php echo $i; ?>" />
            <?php } ?>
            <?php $i++; }  ?>
        </div>   
<?php 
	$i=1;
		foreach($id_arr as $id){ 
		$title = get_the_title( $id ); 
		$post = get_post($id); 
		$content = wp_trim_words( $post->post_content, 20, '' );
?>                 
<div id="slidecaption<?php echo $i; ?>" class="nivo-html-caption">
    <div class="slide_info">
    	<h2><?php echo $title; ?></h2>
    	<p><?php echo $content; ?></p>  
        <?php
		 $slider_readmore = get_theme_mod('slider_readmore');
		 if( !empty($slider_readmore) ){ ?>
          <a class="slide_more" href="<?php the_permalink(); ?>"><?php echo esc_attr($slider_readmore); ?></a>
	  	 <?php } ?>      
    </div>
    
    <?php $i++; } ?>       
     </div><!--end .slider area-->
<div class="clear"></div>        
<?php } ?>
<?php } } ?>  
       
        
<?php if ( is_front_page() && ! is_home() ) { ?>    
    <?php
		$hidewelcome = get_theme_mod('disabled_welcomepg', '1');
		if( $hidewelcome == ''){
      ?>
       <section id="sectiopn-2">   
            	<div class="container">
                   <div class="aboutwrap">                   
					<?php if( get_theme_mod('page-setting1')) { ?>
                          <?php $queryvar = new WP_query('page_id='.absint(get_theme_mod('page-setting1' ,true)) ); ?>
                          <?php while( $queryvar->have_posts() ) : $queryvar->the_post();?> 
                          <h2 class="headingtitle">
                            <?php the_title(); ?>
                          </h2>
                          <p><?php echo wp_trim_words( get_the_content(), 80, '...' ); ?></p>
                          <?php endwhile;
                        	    wp_reset_postdata(); ?> 
                           <?php } ?>                                        
              <div class="clear"></div>
               </div><!-- aboutwrap -->
            </div><!-- container -->
       </section>  	
     <?php } ?>
     
	<?php
        $hidepageboxes = get_theme_mod('disabled_pgboxes', '1');
        if( $hidepageboxes == ''){
    ?>  
    <section id="sectiopn-1">
            	<div class="container">
                    <div class="services-wrap">                               
                        <?php for($p=1; $p<5; $p++) { ?>       
                        <?php if( get_theme_mod('page-column'.$p,false)) { ?>          
                            <?php $queryvar = new WP_query('page_id='.absint(get_theme_mod('page-column'.$p,true)) ); ?>				
                                    <?php while( $queryvar->have_posts() ) : $queryvar->the_post(); ?> 
                                    <div class="fourbox <?php if($p % 4 == 0) { echo "last_column"; } ?>">                                    
                                      <?php if(has_post_thumbnail() ) { ?>
                                        <div class="thumbbx"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail();?></a></div>
                                      <?php } ?>
                                     <div class="pagecontent">
                                     <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>                                    
                                     <p><?php echo wp_trim_words( get_the_content(), 20, '...' ); ?></p>
                                    <a class="ReadMore" href="<?php the_permalink(); ?>">                                      
                                     <?php _e('Read More','unifield'); ?>
                                    </a> 
                                     </div>                                   
                                    </div>
                                    <?php endwhile;
                                   		 wp_reset_postdata(); ?>                                    
                       				<?php } } ?>                                 
                    <div class="clear"></div>  
               </div><!-- services-wrap-->            
            </div><!-- container -->
       </section>          	      
<?php } ?>

<?php $hideftr = get_theme_mod('disabled_ftrpage', '1'); ?>
	  <?php if($hideftr == ''){ ?>  
       <section id="featureswrap">
         <div class="container">
             <div class="ftrarea"> 
               <?php if( get_theme_mod('features_page')) { ?>
                    <?php $queryvar = new WP_query('page_id='.get_theme_mod('features_page' ,true)); ?>
                    <?php while( $queryvar->have_posts() ) : $queryvar->the_post();?>  
                    		<?php if(has_post_thumbnail() ) { ?>
                                  <div class="pagethumbbox"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail();?></a></div>
                             <?php } ?>                        
                            <h3><?php the_title(); ?></h3>                           
                            <p><?php echo wp_trim_words( get_the_content(), 40, '...' ); ?></p>
                            <a class="pagebutton" href="<?php the_permalink(); ?>"><?php _e('Read More','unifield'); ?></a>
                       
						<?php endwhile;
						wp_reset_postdata(); ?>                      
              <?php } ?>             
             </div>
           <div class="clear"></div>
       </div><!-- .container -->
    </section><!-- #featureswrap -->
<?php } ?> 
<?php } ?>