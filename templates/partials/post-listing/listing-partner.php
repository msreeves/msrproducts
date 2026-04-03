         <div class="col-xl-3 col-md-6 mx-auto mb-3">
              <div class="panel">
                <div class="partner-listing-image">
               <?php
$link = get_field( 'link' );
if ( is_array( $link ) && ! empty( $link['url'] ) ) {
	$link_url    = $link['url'];
	$link_target = ! empty( $link['target'] ) ? $link['target'] : '_self';
	?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php get_template_part( 'templates/partials/featured-image' ); ?></a>
	<?php
} else {
	get_template_part( 'templates/partials/featured-image' );
}
?>
          </div>
         </div>
             </div>