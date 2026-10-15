<?php
/**
 * Product compare toggle (P14-03 — keep out of files that also emit inquiry links).
 *
 * @package msrproducts
 *
 * @var array $args {
 *     @type int $product_id Product post ID.
 * }
 */

$product_id = isset( $args['product_id'] ) ? absint( $args['product_id'] ) : 0;
if ( $product_id < 1 ) {
	return;
}
?>
<button type="button" class="button msr-compare-btn card-compare-btn" data-compare-toggle data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"><?php esc_html_e( 'Compare', 'msrproducts' ); ?></button>
