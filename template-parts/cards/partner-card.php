<?php
/**
 * Partner listing card.
 *
 * @package msrproducts
 */
?>
<article class="msr-card partner-card">
	<div class="partner-listing-image">
		<?php
		$link = function_exists( 'get_field' ) ? get_field( 'link' ) : get_post_meta( get_the_ID(), 'link', true );
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			$link_url    = $link['url'];
			$link_target = ! empty( $link['target'] ) ? $link['target'] : '_self';
			?>
			<a class="partner-card__link" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Visit %s', 'msrproducts' ), get_the_title() ) ); ?>">
				<?php get_template_part( 'templates/partials/featured-image' ); ?>
			</a>
			<?php
		} else {
			get_template_part( 'templates/partials/featured-image' );
		}
		?>
	</div>
</article>
