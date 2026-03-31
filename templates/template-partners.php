<?php
/**
 * Template Name: Partners template
 *
 * @package WordPress
 * @subpackage msrawards
 * @since msrawards 1.0
 */

get_header();
?>
<section>
  <div class="container">
        <?php the_title( '<h1>', '</h1>' ); ?>
          <?php the_content(); ?>
         <?php 	
      $args = array(
        'post_type' => 'partner',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
      );
      $all_partners = new WP_Query( $args );		
      ?>

      <?php if ( $all_partners->have_posts() ) : ?>
            <div class="row">
          <?php while ( $all_partners->have_posts() ) : $all_partners->the_post(); ?>	
      <?php get_template_part( 'templates/partials/post-listing/listing-partner' ); ?>
          <?php endwhile; ?>
          <?php wp_reset_query() ?>
      </div>
      <?php endif; ?>

</div>
</section>
<?php
get_footer();