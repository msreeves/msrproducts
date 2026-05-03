<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrproducts
 */

?>

<div class="col-xl-4 col-lg-4 col-md-6 search-result-col">
	<article class="post panel product-card search-card">
		<div class="listing-image">
			<?php get_template_part( 'templates/partials/featured-image' ); ?>
		</div>
		<div class="listing-text">
			<?php
			$cat_list = array();
			if ( get_post_type() === 'product' ) {
				$terms = get_the_terms( get_the_ID(), 'product_cat' );
				if ( ! is_wp_error( $terms ) && is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( ! ( $term instanceof WP_Term ) ) {
							continue;
						}
						$term_link = function_exists( 'msrproducts_get_term_archive_url' ) ? msrproducts_get_term_archive_url( $term ) : get_term_link( $term->term_id );
						if ( is_wp_error( $term_link ) ) {
							continue;
						}
						$cat_list[] = '<a href="' . esc_url( $term_link ) . '"><span>' . esc_html( $term->name ) . '</span></a>';
					}
				}
			} else {
				$exclude = array( 6 );
				foreach ( get_the_category() as $cat ) {
					if ( in_array( $cat->term_id, $exclude, true ) ) {
						continue;
					}
					$cat_list[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '"><span>' . esc_html( $cat->name ) . '</span></a>';
				}
			}
			?>
			<?php if ( ! empty( $cat_list ) ) : ?>
				<p class="category"><?php echo implode( ' ', $cat_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<h3><?php search_title_highlight(); ?></h3>
			<?php if ( has_excerpt() ) : ?>
				<?php search_excerpt_highlight(); ?>
			<?php endif; ?>
			<a href="<?php the_permalink(); ?>" class="card-link-btn">Read more</a>
		</div>
	</article>
</div>