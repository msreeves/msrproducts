<?php
/**
 * Displays the featured image
 *
 * @package WordPress
 * @subpackage msrproducts
 * @since msrproducts 1.0
 */

$featured_media_inner_classes = '';

// Make the featured media thinner on archive pages.
if ( ! is_single() ) {
	$featured_media_inner_classes .= ' medium';
}
?>

<figure class="featured-media">

	<div class="featured-media-inner <?php echo $featured_media_inner_classes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>">

		<?php
		$thumb_id = get_post_thumbnail_id();
		if ( ! post_password_required() && $thumb_id && function_exists( 'msrproducts_attachment_file_exists' ) && msrproducts_attachment_file_exists( $thumb_id ) ) {
			the_post_thumbnail();
		} else {
			echo '<img src="' . esc_url( function_exists( 'msrproducts_placeholder_image_url' ) ? msrproducts_placeholder_image_url() : '' ) . '" alt="' . esc_attr__( 'Placeholder image', 'msrproducts' ) . '" loading="lazy" decoding="async" />';
		}

		$caption = ( ! post_password_required() && has_post_thumbnail() ) ? get_the_post_thumbnail_caption() : '';

		if ( $caption ) {
			?>

			<figcaption class="wp-caption-text"><?php echo wp_kses_post( $caption ); ?></figcaption>

			<?php
		}
		?>

	</div><!-- .featured-media-inner -->

</figure><!-- .featured-media -->
