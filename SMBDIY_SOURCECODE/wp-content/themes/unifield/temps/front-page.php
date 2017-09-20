<?php
/**
 * The Template Name: Front Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Unifield
 */

get_header(); ?>

    <div class="container">

        <?php  //$slider = get_posts(array('post_type' => 'slider', 'posts_per_page'   => 5)); ?>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <?php $slider = get_posts(array('post_type' => 'slider', 'posts_per_page' => 5)); ?>
      <?php $count = 0; ?>
      <?php foreach($slider as $slide): ?>
      <div class="item <?php echo ($count == 0) ? 'active' : ''; ?>">
        <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($slide->ID)) ?>" class="img-responsive"/>
      </div>
      <?php $count++; ?>
    <?php endforeach; ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
      <div class="page_content">
             <section class="site-main fullwidth">               
                    <?php while( have_posts() ) : the_post(); ?>
                                  <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>    
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
 </div><!-- .container --> 
<?php get_footer(); ?>