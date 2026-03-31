 <?php
/**
 * Layouts > Partners
 *
 * @package WordPress
 * @subpackage QORP
 */

?>
 <section>
<div class="container">
    <div class="row">   
      <h1> Our Partners </h1>
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
        <div class="container">
            <div class="row">
          <?php while ( $all_partners->have_posts() ) : $all_partners->the_post(); ?>	
      <?php get_template_part( 'templates/partials/post-listing/listing-partner' ); ?>
          <?php endwhile; ?>
          <?php wp_reset_query() ?>
      </div>
      </div>
      <?php endif; ?>
</div>
      </div>
      </section>