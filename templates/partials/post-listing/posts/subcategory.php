<?php
$categories  = get_the_terms( get_the_ID(), 'product_cat' );
$safe_labels = array();
$price_html  = '';

if ( function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( get_the_ID() );
	if ( $product instanceof WC_Product ) {
		$active_price  = $product->get_price();
		$regular_price = $product->get_regular_price();
		if ( $active_price !== '' ) {
			$price_html = wc_price( (float) $active_price );
			if ( $regular_price !== '' && $regular_price !== $active_price ) {
				$price_html = '<del>' . wc_price( (float) $regular_price ) . '</del> <ins>' . wc_price( (float) $active_price ) . '</ins>';
			}
		}
	}
}

if ( ! is_wp_error( $categories ) && is_array( $categories ) ) {
	foreach ( $categories as $category ) {
		if ( ! ( $category instanceof WP_Term ) ) {
			continue;
		}
		$term_link = function_exists( 'msrproducts_get_term_archive_url' ) ? msrproducts_get_term_archive_url( $category ) : get_term_link( $category->term_id );
		if ( is_wp_error( $term_link ) ) {
			$term_link = home_url( '/?post_type=product' );
		}
		$safe_labels[] = '<a href="' . esc_url( $term_link ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'msrproducts' ), $category->name ) ) . '"><span>' . esc_html( $category->name ) . '</span></a>';
	}
}
?>
<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
	<article class="post panel product-card">
		<div class="listing-image">
			<?php get_template_part( 'templates/partials/featured-image' ); ?>
		</div>
		<div class="listing-text">
			<?php if ( ! empty( $safe_labels ) ) : ?>
				<p class="category"><?php echo implode( '', $safe_labels ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $price_html !== '' ) : ?>
				<p class="card-price"><?php echo wp_kses_post( $price_html ); ?></p>
			<?php endif; ?>
			<h3><?php the_title(); ?></h3>
			<div class="card-actions">
				<a href="<?php the_permalink(); ?>" class="card-link-btn">Request information</a>
				<button type="button" class="card-compare-btn" data-compare-toggle data-product-id="<?php echo esc_attr( (string) get_the_ID() ); ?>">Compare</button>
			</div>
		</div>
	</article>
</div>