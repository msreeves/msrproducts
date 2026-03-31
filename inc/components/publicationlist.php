<?php
/**
 * Layouts > Publication list
 *
 * @package WordPress
 * @subpackage QORP
 */

?>

 <section class="publication">
          <div class="container">
       <?php
       $args = [
           "post_type" => 'publication',
           "posts_per_page" => -1,
       ];
       $all_posts = new WP_Query($args);
       ?>

      <?php if ($all_posts->have_posts()): ?>
           <div class="row">   
          <?php while ($all_posts->have_posts()):
              $all_posts->the_post(); ?>	                
			                 
                          <?php get_template_part(
                              "templates/partials/post-listing/listing-publication"
                          ); ?> 
                          
          <?php
          endwhile; ?>
          <?php wp_reset_query(); ?>
                  </div>        
      <?php endif; ?>
       </div>
  </section>
  