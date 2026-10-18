<?php
/**
 * Homepage closing CTA — full catalog lives on the shop archive.
 *
 * @package msrproducts
 */

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/?post_type=product' );
if ( ! is_string( $shop_url ) || $shop_url === '' ) {
	$shop_url = home_url( '/?post_type=product' );
}
?>
<section class="home-catalog-cta" aria-labelledby="home-catalog-cta-title">
	<div class="container">
		<h2 id="home-catalog-cta-title"><?php echo esc_html( msrproducts_get_featured_grid_link_label() ); ?></h2>
		<p><?php esc_html_e( 'Filter, sort, and compare the full demonstration catalogue on the shop page.', 'msrproducts' ); ?></p>
		<a class="button" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( msrproducts_get_hero_primary_cta() ); ?></a>
	</div>
</section>
