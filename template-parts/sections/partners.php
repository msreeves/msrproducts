 <?php
/**
 * Layouts > Partners
 *
 * @package WordPress
 * @subpackage QORP
 */

?>
<?php
$args = array(
	'post_type'      => 'partner',
	'posts_per_page' => 12,
	'orderby'        => 'title',
	'order'          => 'ASC',
);

$all_partners = new WP_Query( $args );
?>
<section class="partners-showcase" aria-labelledby="partners-showcase-heading">
	<div class="container">
		<div class="partners-showcase__head">
			<h2 id="partners-showcase-heading"><?php echo esc_html( msrproducts_get_partners_band_title() ); ?></h2>
			<p><?php echo esc_html( msrproducts_get_partners_band_lead() ); ?></p>
		</div>
		<div class="partners-showcase__grid">
			<?php if ( $all_partners->have_posts() ) : ?>
				<?php while ( $all_partners->have_posts() ) : $all_partners->the_post(); ?>
					<?php get_template_part( 'template-parts/cards/partner-card' ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php
				$placeholders = array( 'Brand One', 'Retail Partner', 'Creative Studio', 'Tech Partner', 'Distribution Co', 'Agency Partner' );
				foreach ( $placeholders as $label ) :
					?>
					<article class="partner-placeholder">
						<div class="partner-placeholder__logo" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $label, 0, 2 ) ) ); ?></div>
						<h3><?php echo esc_html( $label ); ?></h3>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>