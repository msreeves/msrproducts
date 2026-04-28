        <div class="col-xl-4 col-lg-4">
           <div class="panel">
           <?php
$link = function_exists( 'get_field' ) ? get_field( 'link' ) : get_post_meta( get_the_ID(), 'link', true );
$summary = function_exists( 'get_field' ) ? get_field( 'summary' ) : get_post_meta( get_the_ID(), 'summary', true );
$link_url = '#';
if ( is_array( $link ) && ! empty( $link['url'] ) ) {
	$link_url = $link['url'];
} elseif ( is_string( $link ) && $link !== '' ) {
	$link_url = $link;
}
?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="_blank"> <?php get_template_part( 'templates/partials/featured-image' ); ?></a>
          </div>
         </div>
        <div class="col-xl-8">
           <div class="panel">
            <div class="my-auto">
             <h2><?php the_title(); ?></h2>
             <p><?php echo esc_html( $summary ? $summary : 'Publication summary placeholder content for portfolio storytelling.' ); ?></p>
              <a href="<?php echo esc_url( $link_url ); ?>" target="_blank"><button>Read more</button></a>
               </div>
             </div>
         </div>


