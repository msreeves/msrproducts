<?php
/**
 * Displays the featured image or type icon fallback.
 *
 * @package WordPress
 * @subpackage msrproducts
 */

$featured_media_inner_classes = '';
if ( ! is_single() ) {
	$featured_media_inner_classes .= ' medium';
}
$post_id = get_the_ID();
?>
<figure class="featured-media">
	<div class="featured-media-inner <?php echo $featured_media_inner_classes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<?php
		if ( function_exists( 'msrproducts_render_post_media' ) ) {
			msrproducts_render_post_media( (int) $post_id );
		} else {
			the_post_thumbnail();
		}
		$caption = ( ! post_password_required() && has_post_thumbnail() ) ? get_the_post_thumbnail_caption() : '';
		if ( $caption ) {
			echo '<figcaption class="wp-caption-text">' . wp_kses_post( $caption ) . '</figcaption>';
		}
		?>
	</div>
</figure>
