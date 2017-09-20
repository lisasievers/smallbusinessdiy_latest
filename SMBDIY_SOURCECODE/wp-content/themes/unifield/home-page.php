<?php
/**
 * The Template Name: Home Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Unifield
 */

get_header(); ?>
<div class="sitewrapper">
    <div class="">
        <div class="">
            <style>
.carousel-inner > .item {
transform: translate3d(0,0,0) !important;
}
            </style>
            <div id="carousel-example-generic" class="carousel slide home-slider carousel-fade" data-ride="carousel">
                <!--<ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol> -->
              
                <div class="carousel-inner " role="listbox">
                    <?php $slider = get_posts(array('post_type' => 'slider', 'posts_per_page' => 5)); ?>
                      <?php $count = 0; ?>
                      <?php foreach($slider as $slide): ?>
                      <div class="item <?php echo ($count == 0) ? 'active' : ''; ?>">
                        <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($slide->ID)) ?>" class="img-responsive"/>
                      <div class="main-text-top">
                        <div class="slide-title">
                            <h1><?php echo get_post_field('post_content', $slide->ID); ?></h1>
                        </div>
                     </div>
                      </div>
                      <?php $count++; ?>
                    <?php endforeach; ?>
                  </div>

                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span></a><a class="right carousel-control"
                        href="#carousel-example-generic" data-slide="next"><span class="glyphicon glyphicon-chevron-right">
                        </span></a>
            </div>

            
 
        </div>
 <div class="main-text">
                <div class="col-md-12 text-center">
                    <h1>How will your customers <span class="diy-color">FIND</span> you? </h1>
                    
                    <div class="diy-call">
                        <div class="one_half"><div class="iter1"><span class="diy-color">NEED</span> A WEBSITE </div><div class="iter2"><a class="btn-learn" href="<?php echo esc_url( home_url( '/' ) ); ?>sitebuilder/public/signup">LEARN MORE <i class="fa fa-caret-right" aria-hidden="true"></i></a></div></div>
                        <div class="one_half"><div class="iter1"><span class="diy-color">HAVE</span> A WEBSITE </div><div class="iter2"><a class="btn-learn" href="<?php echo esc_url( home_url( '/' ) ); ?>siteresponsive/home/havesite">LEARN MORE <i class="fa fa-caret-right" aria-hidden="true"></i></a></div></div>
                    
                    </div>
                    <div class="learn-diy"><a href="#howitworks"><span class="diy-color">Learn to</span> Do IT <span class="diy-color">Yourself</span> <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a></div>
                 
				</div>
            </div>
    </div>
</div><!-- site wrapper -->
    <div class="home-container">

      <div class="page_content">
             <section class="site-main fullwidth">               
                    <?php while( have_posts() ) : the_post(); ?>
                                  <?php //the_title( '<h1 class="entry-title">', '</h1>' ); ?>    
                                   <div class="entry-content">
                                            <?php the_content(); ?>
                                            <?php
                                                wp_link_pages( array(
                                                'before' => '<div class="page-links">' . __( 'Pages:', 'unifield' ),
                                                'after'  => '</div>',
                                                ) );
                                            ?>
                                            <?php
                                                //If comments are open or we have at least one comment, load up the comment template
                                                if ( comments_open() || '0' != get_comments_number() )
                                                    comments_template();
                                                ?>
                                </div><!-- entry-content -->
                            <?php endwhile; ?> 


                     
            </section><!-- section-->   
    </div><!-- .page_content --> 
 </div><!--home-container --> 
<?php get_footer(); ?>